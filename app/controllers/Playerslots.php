<?php
class Playerslots extends Controller {

    private $slotModel;

    public function __construct() {
        requireAuth(['Player']);
        $this->slotModel = $this->model('M_SlotPlayer');
        require_once APPROOT . '/libraries/SlotBookingService.php';
    }

    // ── Helpers ────────────────────────────────────────────────

    private function playerId(): int {
        return (int)($_SESSION['user_id'] ?? 6);
    }

    private function playerData(): array {
        $userId    = $this->playerId();
        $userModel = $this->model('M_Users');
        $user      = $userModel->getUserWithProfile($userId) ?: $userModel->getUserById($userId);

        if ($user) {
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

    /** /playerslots  →  redirect to available */
    public function index() {
        redirect('playerslots/available');
    }

    /** GET /playerslots/available */
    public function available() {
        $playerId    = $this->playerId();
        $occurrences = $this->slotModel->getAvailableOccurrences($playerId);

        $this->view('player/slots', [
            'title'       => 'Book a Session',
            'player'      => $this->playerData(),
            'occurrences' => $occurrences,
        ]);
    }

    /** POST /playerslots/book */
    public function book() {
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
            $occurrenceId,
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
            $_SESSION['slot_success'] = 'Session booked successfully!';
            redirect('playerslots/bookings');
        }

        $messages = [
            'duplicate'       => 'You have already booked this session.',
            'full'            => 'This session is fully booked.',
            'active_injury'   => 'You have an active medical flag. Please see the admin before booking.',
            'no_subscription' => 'You need an active subscription to book this session.',
            'plan_mismatch'   => 'Your current plan does not include this session type.',
            'not_found'       => 'Session not found.',
            'window_closed'   => 'Bookings for this session are closed.',
            'error'           => 'An unexpected error occurred. Please try again.',
        ];

        $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        redirect('playerslots/available');
    }

    /** GET /playerslots/bookings */
    public function bookings() {
        $playerId = $this->playerId();
        $bookings = $this->slotModel->getPlayerBookings($playerId);

        // Split into upcoming and past
        $today    = date('Y-m-d');
        $upcoming = [];
        $past     = [];

        foreach ($bookings as $b) {
            if ($b->OccurrenceDate >= $today && $b->Status !== 'cancelled') {
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
        $playerId   = $this->playerId();
        $facilityId = (int)($_GET['facility'] ?? 0);
        $date       = trim($_GET['date'] ?? '');
        $slotId     = (int)($_GET['slot'] ?? 0);

        // Sanitise date input
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
            $_SESSION['slot_success'] = 'Facility slot booked successfully!';
            redirect('playerslots/bookings');
        }

        $messages = [
            'duplicate'       => 'You have already booked this slot.',
            'full'            => 'This slot is fully booked.',
            'active_injury'   => 'You have an active medical flag. Please see the admin before booking.',
            'no_subscription' => 'You need an active subscription to book this slot.',
            'plan_mismatch'   => 'Your current plan does not include facility access. Please upgrade your plan.',
            'not_found'       => 'Slot not found.',
            'error'           => 'An unexpected error occurred. Please try again.',
        ];
        $_SESSION['slot_error'] = $messages[$result] ?? $messages['error'];
        redirect('playerslots/facilities');
    }

    /** POST /playerslots/cancel */
    public function cancel() {
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
