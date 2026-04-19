<?php
/**
 * Player session/facility booking controller.
 *
 * This controller is only for logged-in players. It shows available sessions,
 * creates bookings, shows booking history, and cancels eligible bookings.
 *
 * Main flow:
 * 1. Player opens available coach/trainer/facility slots.
 * 2. Controller asks M_SlotPlayer for valid occurrences based on plan/rules.
 * 3. Player submits a booking form.
 * 4. M_SlotPlayer validates and inserts the booking.
 * 5. Controller creates notifications and redirects back with a flash message.
 */
class Playerslots extends Controller {

    private $slotModel;

    public function __construct() {
        // Security: Core already checks route access, but this keeps the controller safe
        // if a method is called directly from another route.
        requireAuth(['Player']);
        $this->slotModel = $this->model('M_SlotPlayer');
        require_once APPROOT . '/libraries/SlotBookingService.php';
    }

    private function playerId(): int {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    private function playerData(): array {
        $userId = $this->playerId();
        $userModel = $this->model('M_Users');
        $user = $userModel->getUserWithProfile($userId) ?: $userModel->getUserById($userId);
        $planKey = $this->slotModel->getActivePlanKey($userId);

        return [
            'id' => (int) ($user->UserID ?? $userId),
            'name' => $user->Name ?? 'Player',
            'membership_level' => $planKey ?: 'general',
        ];
    }

    private function filterOccurrencesToNextThreeWeeks(array $occurrences): array {
        $from = (new DateTimeImmutable('today'))->setTime(0, 0, 0)->getTimestamp();
        $to = (new DateTimeImmutable('today'))->modify('+21 days')->setTime(23, 59, 59)->getTimestamp();

        return array_values(array_filter($occurrences, function ($occ) use ($from, $to) {
            $dateStr = (string) ($occ->OccurrenceDate ?? '');
            if ($dateStr === '') {
                return false;
            }

            $startTime = (string) ($occ->StartTime ?? '00:00:00');
            $ts = strtotime($dateStr . ' ' . $startTime);
            if ($ts === false) {
                $ts = strtotime($dateStr);
            }

            return $ts !== false && $ts >= $from && $ts <= $to;
        }));
    }

    private function getPlanMeta(int $playerId): array {
        $planKey = $this->slotModel->getActivePlanKey($playerId);

        $plans = [
            'general' => [
                'key' => 'general',
                'label' => 'General Subscription',
                'coach_enabled' => false,
                'trainer_enabled' => false,
                'facility_enabled' => true,
                'private_weekly_limit' => null,
                'facility_weekly_limit' => 3,
                'cancel_window_hours' => 24,
                'summary' => 'Assigned coach and trainer sessions appear in My Sessions. Facility slots can be paid and booked separately.',
            ],
            'private' => [
                'key' => 'private',
                'label' => 'Private Only',
                'coach_enabled' => true,
                'trainer_enabled' => true,
                'facility_enabled' => true,
                'private_weekly_limit' => 2,
                'facility_weekly_limit' => 3,
                'cancel_window_hours' => 48,
                'summary' => 'Private members can book coach and trainer sessions, up to 2 private sessions per week, plus facility slots.',
            ],
            'facility_only' => [
                'key' => 'facility_only',
                'label' => 'Facility Only',
                'coach_enabled' => false,
                'trainer_enabled' => false,
                'facility_enabled' => true,
                'private_weekly_limit' => null,
                'facility_weekly_limit' => 3,
                'cancel_window_hours' => 24,
                'summary' => 'Facility members can only see and book facility slots.',
            ],
            'pro' => [
                'key' => 'pro',
                'label' => 'Pro',
                'coach_enabled' => true,
                'trainer_enabled' => true,
                'facility_enabled' => true,
                'private_weekly_limit' => null,
                'facility_weekly_limit' => 3,
                'cancel_window_hours' => 24,
                'summary' => 'All booking areas are available for this plan.',
            ],
        ];

        return $plans[$planKey] ?? $plans['general'];
    }

    private function getSubNav(string $activeTab, array $planMeta): array {
        $tabs = [
            [
                'key' => 'my_sessions',
                'label' => 'My Sessions',
                'icon' => 'fa-list-alt',
                'href' => URLROOT . '/playerslots/bookings',
                'enabled' => true,
            ],
            [
                'key' => 'coach_booking',
                'label' => 'Coach Booking',
                'icon' => 'fa-user-tie',
                'href' => URLROOT . '/playerslots/coach',
                'enabled' => (bool) $planMeta['coach_enabled'],
            ],
            [
                'key' => 'trainer_booking',
                'label' => 'Trainer Booking',
                'icon' => 'fa-dumbbell',
                'href' => URLROOT . '/playerslots/trainer',
                'enabled' => (bool) $planMeta['trainer_enabled'],
            ],
            [
                'key' => 'facility_booking',
                'label' => 'Facility Booking',
                'icon' => 'fa-building',
                'href' => URLROOT . '/playerslots/facilities',
                'enabled' => (bool) $planMeta['facility_enabled'],
            ],
        ];

        foreach ($tabs as &$tab) {
            $tab['active'] = $tab['key'] === $activeTab;
        }

        return $tabs;
    }

    private function redirectForTab(string $tab): void {
        $routes = [
            'coach_booking' => 'playerslots/coach',
            'trainer_booking' => 'playerslots/trainer',
            'facility_booking' => 'playerslots/facilities',
            'my_sessions' => 'playerslots/bookings',
        ];

        redirect($routes[$tab] ?? 'playerslots/bookings');
    }

    private function ensureTabAccess(string $tab, array $planMeta): void {
        if ($tab === 'coach_booking' && !$planMeta['coach_enabled']) {
            $_SESSION['slot_error'] = 'Coach booking is not available for your current membership plan.';
            redirect('playerslots/bookings');
        }

        if ($tab === 'trainer_booking' && !$planMeta['trainer_enabled']) {
            $_SESSION['slot_error'] = 'Trainer booking is not available for your current membership plan.';
            redirect('playerslots/bookings');
        }
    }

    private function splitModuleSessions(array $rows): array {
        $now = time();
        $upcoming = [];
        $history = [];

        foreach ($rows as $row) {
            $date = $row->OccurrenceDate ?? $row->Date ?? $row->date ?? null;
            $startTime = $row->StartTime ?? '00:00:00';
            $endTime = $row->EndTime ?? $startTime;
            $startTimestamp = $date ? strtotime($date . ' ' . $startTime) : false;
            $endTimestamp = $date ? strtotime($date . ' ' . $endTime) : false;

            if ($endTimestamp !== false && $endTimestamp <= $now) {
                $history[] = $row;
            } elseif ($startTimestamp !== false) {
                $upcoming[] = $row;
            } else {
                $history[] = $row;
            }
        }

        return ['upcoming' => $upcoming, 'history' => $history];
    }

    private function getPageNumber(string $key): int {
        $page = (int) ($_GET[$key] ?? 1);
        return max(1, $page);
    }

    private function paginateRows(array $rows, int $page, int $perPage = 8): array {
        $totalRows = count($rows);
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        return [
            'rows' => array_slice($rows, $offset, $perPage),
            'page' => $page,
            'per_page' => $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'has_multiple_pages' => $totalPages > 1,
        ];
    }

    private function getCatalogOccurrences(int $playerId, string $tab): array {
        if ($tab === 'facility_booking') {
            $facilityId = (int) ($_GET['facility'] ?? 0);
            $date = trim((string) ($_GET['date'] ?? ''));
            $slotId = (int) ($_GET['slot'] ?? 0);
            $maxDate = date('Y-m-d', strtotime('+21 days'));

            if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $date = '';
            }
            if ($date !== '' && $date < date('Y-m-d')) {
                $date = date('Y-m-d');
            }
            if ($date !== '' && $date > $maxDate) {
                $_SESSION['slot_error'] = 'Slots can only be viewed up to 3 weeks ahead.';
                $date = $maxDate;
            }

            return [
                'rows' => $this->filterOccurrencesToNextThreeWeeks(
                    $this->slotModel->getFacilityOccurrences($playerId, $facilityId, $date, $slotId, $maxDate)
                ),
                'filter' => [
                    'facility' => $facilityId,
                    'date' => $date,
                    'slot' => $slotId,
                ],
                'maxDate' => $maxDate,
                'facilities' => $this->slotModel->getAllFacilities(),
                'timeBands' => $this->slotModel->getTimeBands(),
            ];
        }

        if ($tab === 'coach_booking' || $tab === 'trainer_booking') {
            $staffType = $tab === 'coach_booking' ? 'coach' : 'trainer';
            $maxDate = date('Y-m-d', strtotime('+21 days'));
            $rows = $this->slotModel->getAllPrivateCoachOccurrences($playerId, $maxDate);
            $rows = array_values(array_filter($rows, function ($row) use ($staffType) {
                return strtolower((string) ($row->SlotType ?? '')) === 'private'
                    && strtolower((string) ($row->StaffType ?? 'coach')) === $staffType;
            }));

            return ['rows' => $this->filterOccurrencesToNextThreeWeeks($rows)];
        }

        return ['rows' => []];
    }

    private function getPageContent(string $tab, int $playerId, array $planMeta): array {
        if ($tab === 'my_sessions') {
            $sessionRows = $this->slotModel->getPlayerModuleSessions($playerId);
            $sessionRows = array_values(array_filter($sessionRows, function ($row) {
                return !empty($row->OccurrenceDate) || !empty($row->Date) || !empty($row->date);
            }));
            $splitSessions = $this->splitModuleSessions($sessionRows);

            return [
                'title' => 'My Sessions',
                'description' => 'See your assigned sessions, confirmed private bookings, and facility bookings in one place.',
                'sessions' => [
                    'upcoming' => $this->paginateRows($splitSessions['upcoming'], $this->getPageNumber('upcoming_page')),
                    'history' => $this->paginateRows($splitSessions['history'], $this->getPageNumber('history_page')),
                ],
            ];
        }

        if ($tab === 'coach_booking') {
            $catalog = $this->getCatalogOccurrences($playerId, $tab);
            $catalog['pagination'] = $this->paginateRows($catalog['rows'] ?? [], $this->getPageNumber('catalog_page'));

            return [
                'title' => 'Coach Booking',
                'description' => 'Browse private coach sessions available under your membership plan.',
                'catalog' => $catalog,
            ];
        }

        if ($tab === 'trainer_booking') {
            $catalog = $this->getCatalogOccurrences($playerId, $tab);
            $catalog['pagination'] = $this->paginateRows($catalog['rows'] ?? [], $this->getPageNumber('catalog_page'));

            return [
                'title' => 'Trainer Booking',
                'description' => 'Browse private trainer sessions available under your membership plan.',
                'catalog' => $catalog,
            ];
        }

        $catalog = $this->getCatalogOccurrences($playerId, 'facility_booking');
        $catalog['pagination'] = $this->paginateRows($catalog['rows'] ?? [], $this->getPageNumber('catalog_page'));

        return [
            'title' => 'Facility Booking',
            'description' => 'Facility booking is shared across all player membership plans.',
            'catalog' => $catalog,
        ];
    }

    private function renderModule(string $tab): void {
        $playerId = $this->playerId();
        $planMeta = $this->getPlanMeta($playerId);

        $this->ensureTabAccess($tab, $planMeta);

        $page = $this->getPageContent($tab, $playerId, $planMeta);

        $this->view('player/slots_module', [
            'title' => $page['title'],
            'page_description' => $page['description'],
            'player' => $this->playerData(),
            'plan' => $planMeta,
            'current_tab' => $tab,
            'subnav' => $this->getSubNav($tab, $planMeta),
            'sessions' => $page['sessions'] ?? [
                'upcoming' => $this->paginateRows([], 1),
                'history' => $this->paginateRows([], 1),
            ],
            'catalog' => $page['catalog'] ?? [
                'rows' => [],
                'pagination' => $this->paginateRows([], 1),
            ],
        ]);
    }

    public function index() {
        $this->bookings();
    }

    public function available() {
        redirect('playerslots/bookings');
    }

    public function bookings() {
        $this->renderModule('my_sessions');
    }

    public function coach() {
        $this->renderModule('coach_booking');
    }

    public function trainer() {
        $this->renderModule('trainer_booking');
    }

    public function facilities() {
        $this->renderModule('facility_booking');
    }

    public function book() {
        // Handles normal coach/trainer session booking.
        // Validation and business rules live in M_SlotPlayer::createBooking().
        // This controller only collects POST data and translates model result codes
        // into user-friendly messages.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/bookings');
        }

        $occurrenceId = (int) ($_POST['occurrence_id'] ?? 0);
        $playerId = $this->playerId();
        $participantCount = max(1, (int) ($_POST['participant_count'] ?? 1));
        $redirectTab = trim((string) ($_POST['redirect_tab'] ?? 'my_sessions'));

        if ($occurrenceId <= 0) {
            $_SESSION['slot_error'] = 'Invalid session selected.';
            $this->redirectForTab($redirectTab);
        }

        $result = $this->slotModel->createBooking(
            // OccurrenceID identifies the exact session date/time being booked.
            $occurrenceId,
            // PlayerID identifies who owns this booking.
            $playerId,
            'self',
            $playerId,
            null,
            0.0,
            null,
            'not_required',
            $participantCount
        );

        if ($result === true) {
            $_SESSION['slot_success'] = 'Booking confirmed successfully.';
            redirect('playerslots/bookings');
        }

        $messages = [
            'duplicate' => 'You have already booked this session.',
            'full' => 'This session is fully booked.',
            'time_conflict' => 'You already have another booking at the same time.',
            'active_injury' => 'You have an active medical flag. Please contact the academy before booking.',
            'no_subscription' => 'You need an active subscription to book this session.',
            'plan_mismatch' => 'This booking type is not included in your current membership plan.',
            'weekly_private_limit' => 'Private-only members can book at most 2 private sessions per week.',
            'weekly_facility_limit' => 'Players can book at most 3 facility slots per week.',
            'not_found' => 'Session not found.',
            'window_closed' => 'Bookings for this session are closed.',
            'error' => 'An unexpected error occurred. Please try again.',
        ];

        $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        $this->redirectForTab($redirectTab);
    }

    public function bookfacility() {
        // Facility booking is similar to session booking, but may include a payment amount.
        // At this point, the current implementation records the payment status in the booking.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/facilities');
        }

        $occurrenceId = (int) ($_POST['occurrence_id'] ?? 0);
        $playerId = $this->playerId();
        $amount = max(0.0, (float) ($_POST['amount'] ?? 0.0));
        $participantCount = max(1, (int) ($_POST['participant_count'] ?? 1));
        $payStatus = $amount > 0.0 ? 'paid' : 'not_required';
        $payMethod = $amount > 0.0 ? 'card' : null;

        if ($occurrenceId <= 0) {
            $_SESSION['slot_error'] = 'Invalid slot selected.';
            redirect('playerslots/facilities');
        }

        $result = $this->slotModel->createBooking(
            $occurrenceId,
            $playerId,
            'self',
            $playerId,
            null,
            $amount,
            $payMethod,
            $payStatus,
            $participantCount
        );

        if ($result === true) {
            $_SESSION['slot_success'] = 'Facility booking confirmed successfully.';
            redirect('playerslots/bookings');
        }

        $messages = [
            'duplicate' => 'You have already booked this facility slot.',
            'full' => 'This facility slot is fully booked.',
            'time_conflict' => 'You already have another booking at the same time.',
            'active_injury' => 'You have an active medical flag. Please contact the academy before booking.',
            'no_subscription' => 'You need an active subscription to book this facility slot.',
            'plan_mismatch' => 'Facility booking is not available in your current plan.',
            'weekly_private_limit' => 'Private-only members can book at most 2 private sessions per week.',
            'weekly_facility_limit' => 'Players can book at most 3 facility slots per week.',
            'not_found' => 'Slot not found.',
            'error' => 'An unexpected error occurred. Please try again.',
        ];

        $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        redirect('playerslots/facilities');
    }

    public function cancel() {
        // Players can cancel only their own bookings and only within the allowed window.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/bookings');
        }

        $bookingId = (int) ($_POST['booking_id'] ?? 0);
        $playerId = $this->playerId();

        $result = $this->slotModel->cancelBooking($bookingId, $playerId);

        if ($result === true) {
            $_SESSION['slot_success'] = 'Booking cancelled successfully.';
        } else {
            $messages = [
                'not_found' => 'Booking not found.',
                'forbidden' => 'You are not authorised to cancel this booking.',
                'already_cancelled' => 'This booking has already been cancelled.',
                'window_closed' => 'This booking can no longer be cancelled because the cancellation window has closed.',
                'error' => 'An error occurred. Please try again.',
            ];
            $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        }

        redirect('playerslots/bookings');
    }
}
