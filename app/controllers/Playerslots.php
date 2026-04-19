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

    // ── Helpers ────────────────────────────────────────────────

    private function playerId(): int {
        // Current player ID comes from the login session created by Login controller.
        return (int)$_SESSION['user_id'];
    }

    private function playerData(): array {
        // Common player summary passed to slot-related views.
        // The view uses this for headings/profile display, not for security decisions.
        $userId    = $this->playerId();
        $userModel = $this->model('M_Users');
        $user      = $userModel->getUserWithProfile($userId) ?: $userModel->getUserById($userId);

        if ($user) {
            // Membership level comes from payment/subscription data.
            // If no subscription row exists, the UI falls back to "Standard".
            $sub = $this->model('M_Payment')->getPlayerSubscription($userId);
            return [
                'id'               => $user->UserID,
                'name'             => $user->Name,
                'membership_level' => $sub->PlanName ?? 'Standard',
            ];
        }
        return ['id' => $userId, 'name' => 'Player', 'membership_level' => 'Standard'];
    }

    // ── Routes ─────────────────────────────────────────────────

    private function renderSessionCatalog(?string $staffType = null): void {
        // Shared renderer for coach sessions, trainer sessions, and all available sessions.
        // $staffType:
        // - "coach" means show coach sessions allowed by membership.
        // - "trainer" means filter trainer-led sessions.
        // - null means show general available occurrences.
        $playerId    = $this->playerId();
        if ($staffType === 'coach') {
            // Membership rules control which coach session types the player can see.
            $planKey = $this->slotModel->getActivePlanKey($playerId);

            if ($planKey === 'facility_only') {
                $_SESSION['slot_error'] = 'Facility-only members cannot access coach sessions.';
                redirect('playerslots/bookings');
            }

            $slotTypes = match ($planKey) {
                // General members can book program sessions.
                'general' => ['program'],
                // Private members can book private sessions.
                'private' => ['private'],
                // Pro members can access both program and private sessions.
                'pro' => ['program', 'private'],
                default => [],
            };

            // Private-only players can choose from private coach occurrences.
            // Other plans are restricted to assigned coaches and allowed slot types.
            $occurrences = $planKey === 'private'
                ? $this->slotModel->getAllPrivateCoachOccurrences($playerId)
                : $this->slotModel->getAssignedCoachOccurrences($playerId, $slotTypes);
        } else {
            $occurrences = $this->slotModel->getAvailableOccurrences($playerId);
        }

        if ($staffType !== null && $staffType !== 'coach') {
            // For trainer route, remove facility-only slots and keep only matching staff type.
            $occurrences = array_values(array_filter($occurrences, function ($occ) use ($staffType) {
                return ($occ->SlotType ?? '') !== 'facility_only'
                    && strcasecmp((string)($occ->StaffType ?? ''), $staffType) === 0;
            }));
        }

        $titles = [
            'coach' => 'Coach Sessions',
            'trainer' => 'Trainer Bookings',
            null => 'Book a Session',
        ];

        $descriptions = [
            'coach' => 'See the sessions available through your assigned coaches.',
            'trainer' => 'Browse available trainer-led sessions and book your next appointment.',
            null => 'Browse available training slots and book your next session.',
        ];

        $this->view('player/slots', [
            // The same view is reused for multiple booking pages.
            // These values customize the title, description, and displayed slot list.
            'title' => $titles[$staffType] ?? $titles[null],
            'player' => $this->playerData(),
            'occurrences' => $occurrences,
            'booking_type' => $staffType,
            'page_description' => $descriptions[$staffType] ?? $descriptions[null],
        ]);
    }

    /** GET /playerslots */
    public function index() {
        $this->bookings();
    }

    /** GET /playerslots/available */
    public function available() {
        $this->renderSessionCatalog();
    }

    /** GET /playerslots/coach */
    public function coach() {
        if ($this->slotModel->getActivePlanKey($this->playerId()) === 'facility_only') {
            $_SESSION['slot_error'] = 'Facility-only members cannot access coach sessions.';
            redirect('playerslots/bookings');
        }

        $this->renderSessionCatalog('coach');
    }

    /** GET /playerslots/trainer */
    public function trainer() {
        $this->renderSessionCatalog('trainer');
    }

    /** POST /playerslots/book */
    public function book() {
        // Handles normal coach/trainer session booking.
        // Validation and business rules live in M_SlotPlayer::createBooking().
        // This controller only collects POST data and translates model result codes
        // into user-friendly messages.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/available');
        }

        $occurrenceId     = (int)($_POST['occurrence_id'] ?? 0);
        $playerId         = $this->playerId();
        $participantCount = max(1, (int)($_POST['participant_count'] ?? 1));

        if ($occurrenceId <= 0) {
            $_SESSION['slot_error'] = 'Invalid session selected.';
            redirect('playerslots/available');
        }

        $result = $this->slotModel->createBooking(
            // OccurrenceID identifies the exact session date/time being booked.
            $occurrenceId,
            // PlayerID identifies who owns this booking.
            $playerId,
            'self',           // source
            $playerId,        // bookedBy = player themselves
            null,             // subscriptionId  — model resolves via entitlement check
            0.0,              // amount (subscription covers cost)
            null,             // payMethod
            'not_required',   // payStatus
            $participantCount
        );

        if ($result === true) {
            // Create in-app notifications for both the player and assigned staff.
            $this->createSessionBookedNotification($playerId, $occurrenceId);
            $_SESSION['slot_success'] = 'Session booked successfully!';
            redirect('playerslots/bookings');
        }

        $messages = [
            // These keys are returned by M_SlotPlayer::createBooking().
            // Keeping messages here separates business logic from presentation wording.
            'duplicate'       => 'You have already booked this session.',
            'full'            => 'This session is fully booked.',
            'time_conflict'   => 'You already have another booking at the same date and time.',
            'active_injury'   => 'You have an active medical flag. Please see the admin before booking.',
            'no_subscription' => 'You need an active subscription to book this session.',
            'plan_mismatch'   => 'Your current plan does not include this session type.',
            'weekly_private_limit' => 'Private-only members can book at most 3 private sessions per week.',
            'weekly_facility_limit' => 'You can book facilities at most 6 times per week.',
            'not_found'       => 'Session not found.',
            'window_closed'   => 'Bookings for this session are closed.',
            'error'           => 'An unexpected error occurred. Please try again.',
        ];

        $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        redirect('playerslots/available');
    }

    /** GET /playerslots/bookings */
    public function bookings() {
        // Shows the player's bookings split into upcoming and past groups.
        // This page is shown at /playerslots/bookings.
        $playerId = $this->playerId();
        $bookings = $this->slotModel->getPlayerBookings($playerId);

        // Split into upcoming and past using the full occurrence datetime.
        $now      = time();
        $upcoming = [];
        $past     = [];

        foreach ($bookings as $b) {
            // Combine date + start time so the system can compare against current time.
            $sessionDateTime = !empty($b->OccurrenceDate)
                ? strtotime($b->OccurrenceDate . ' ' . ($b->StartTime ?? '00:00:00'))
                : false;

            if ($sessionDateTime !== false && $sessionDateTime >= $now && $b->Status !== 'cancelled') {
                $upcoming[] = $b;
            } else {
                $past[] = $b;
            }
        }

        $this->view('player/slot_bookings', [
            'title'    => 'My Session Bookings',
            'player'   => $this->playerData(),
            'upcoming' => $upcoming,
            'past'     => $past,
        ]);
    }

    /** GET /playerslots/facilities[?facility=N&date=YYYY-MM-DD&slot=N] */
    public function facilities() {
        // Facility booking page supports filters by facility, date, and time band.
        // Filters come from the query string, for example:
        // /playerslots/facilities?facility=2&date=2026-04-18&slot=4
        $playerId   = $this->playerId();
        $facilityId = (int)($_GET['facility'] ?? 0);
        $date       = trim($_GET['date'] ?? '');
        $slotId     = (int)($_GET['slot'] ?? 0);

        // Sanitise date input
        // Only accept YYYY-MM-DD. Invalid dates are ignored instead of trusted.
        if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = '';
        }
        if ($date !== '' && $date < date('Y-m-d')) {
            $date = date('Y-m-d');
        }

        $facilities  = $this->slotModel->getAllFacilities();
        $timeBands   = $this->slotModel->getTimeBands();
        $occurrences = $this->slotModel->getFacilityOccurrences($playerId, $facilityId, $date, $slotId);

        $this->view('player/facility_slots', [
            'title'       => 'Facility Booking',
            'player'      => $this->playerData(),
            'facilities'  => $facilities,
            'timeBands'   => $timeBands,
            'occurrences' => $occurrences,
            'filter'      => [
                'facility' => $facilityId,
                'date'     => $date,
                'slot'     => $slotId,
            ],
        ]);
    }

    /** POST /playerslots/bookfacility */
    public function bookfacility() {
        // Facility booking is similar to session booking, but may include a payment amount.
        // At this point, the current implementation records the payment status in the booking.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/facilities');
        }

        $occurrenceId     = (int)($_POST['occurrence_id'] ?? 0);
        $playerId         = $this->playerId();
        $amount           = max(0.0, (float)($_POST['amount'] ?? 0.0));
        $participantCount = max(1, (int)($_POST['participant_count'] ?? 1));
        $payStatus        = $amount > 0.0 ? 'paid'         : 'not_required';
        $payMethod        = $amount > 0.0 ? 'card'         : null;

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
            $this->createSessionBookedNotification($playerId, $occurrenceId);
            $_SESSION['slot_success'] = 'Facility slot booked successfully!';
            redirect('playerslots/bookings');
        }

        $messages = [
            'duplicate'       => 'You have already booked this slot.',
            'full'            => 'This slot is fully booked.',
            'time_conflict'   => 'You already have another booking at the same date and time.',
            'active_injury'   => 'You have an active medical flag. Please see the admin before booking.',
            'no_subscription' => 'You need an active subscription to book this slot.',
            'plan_mismatch'   => 'Your current plan does not include facility access. Please upgrade your plan.',
            'weekly_private_limit' => 'Private-only members can book at most 3 private sessions per week.',
            'weekly_facility_limit' => 'You can book facilities at most 6 times per week.',
            'not_found'       => 'Slot not found.',
            'error'           => 'An unexpected error occurred. Please try again.',
        ];
        $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        redirect('playerslots/facilities');
    }

    private function createSessionBookedNotification(int $playerId, int $occurrenceId): void {
        // Notification failure should never block the booking itself.
        // That is why all notification logic is inside try/catch.
        if ($playerId <= 0 || $occurrenceId <= 0) {
            return;
        }

        try {
            // Read extra details so the notification message is useful to the user.
            $details = $this->slotModel->getOccurrenceNotificationDetails($occurrenceId);
            $sessionName = (string)($details->TemplateName ?? 'Session');
            $date = (string)($details->OccurrenceDate ?? '');
            $startTime = (string)($details->StartTime ?? '');
            $displayDate = $date !== '' ? date('D, d M Y', strtotime($date)) : 'the selected date';
            $displayTime = $startTime !== '' ? date('g:i A', strtotime($startTime)) : 'the selected time';
            $facility = (string)($details->FacilityName ?? 'Academy');

            $notificationModel = $this->model('M_Notification');
            // Player notification: confirms their own booking.
            // The unique key prevents duplicate notifications if the same flow is retried.
            $notificationModel->createOnceForOrder(
                $playerId,
                'slot-booked-' . $playerId . '-' . $occurrenceId,
                'session',
                'Session booked successfully',
                "{$sessionName} has been booked for {$displayDate} at {$displayTime} at {$facility}.",
                URLROOT . '/playerslots/bookings'
            );

            $staffIds = $this->slotModel->getOccurrenceStaffUserIds($occurrenceId);
            // Staff IDs usually come from assigned coach/trainer users for the slot.
            $player = $this->playerData();
            $playerName = (string)($player['name'] ?? 'A player');
            // Staff notification: informs coaches/trainers when a player books their session.
            // createOnceForUsers() sends the same alert to each staff user safely.
            $notificationModel->createOnceForUsers(
                $staffIds,
                'staff-slot-booked-' . $playerId . '-' . $occurrenceId,
                'session',
                'New session booking',
                "{$playerName} booked {$sessionName} for {$displayDate} at {$displayTime}.",
                URLROOT . '/staffslots/occurrence/' . $occurrenceId
            );
        } catch (Throwable $e) {
            error_log('Session booking notification failed: ' . $e->getMessage());
        }
    }

    /** POST /playerslots/cancel */
    public function cancel() {
        // Players can cancel only their own bookings and only within the allowed window.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('playerslots/bookings');
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $playerId  = $this->playerId();

        $result = $this->slotModel->cancelBooking($bookingId, $playerId);

        if ($result === true) {
            $_SESSION['slot_success'] = 'Booking cancelled successfully.';
        } else {
            $messages = [
                'not_found'        => 'Booking not found.',
                'forbidden'        => 'You are not authorised to cancel this booking.',
                'already_cancelled'=> 'This booking has already been cancelled.',
                'window_closed'    => 'Cancellations must be made at least 24 hours before the session.',
                'error'            => 'An error occurred. Please try again.',
            ];
            $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        }

        redirect('playerslots/bookings');
    }
}
