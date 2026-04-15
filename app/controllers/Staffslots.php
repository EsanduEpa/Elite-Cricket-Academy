<?php
class Staffslots extends Controller {

    private int    $userId;
    private string $role;      // 'Coach' or 'Trainer'
    private string $staffType; // 'coach'  or 'trainer'  (lowercase for DB)

    public function __construct() {
        requireAuth(['Coach', 'Trainer']);
        $this->userId = (int) $_SESSION['user_id'];
        $role = $_SESSION['user_role'] ?? '';
        // Normalise: only accept Coach / Trainer — guards against stale sessions in DEV_MODE
        if (!in_array($role, ['Coach', 'Trainer'], true)) {
            $role = 'Coach'; // safe fallback; real auth blocks non-coach/trainer in production
        }
        $this->role      = $role;
        $this->staffType = strtolower($role);   // 'coach' | 'trainer'
    }

    // =========================================================
    // INDEX  —  /staffslots
    // =========================================================
    public function index() {
        redirect('staffslots/calendar');
    }

    // =========================================================
    // CALENDAR  —  /staffslots/calendar[/{startDate}]
    // =========================================================
    public function calendar($startDate = null) {
        $model = $this->model('M_SlotStaff');

        // Parse / normalise week start
        if ($startDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            $weekTs = strtotime($startDate);
        } else {
            $weekTs = time();
        }

        $dow   = (int) date('N', $weekTs);
        $monTs = strtotime('-' . ($dow - 1) . ' days', $weekTs);
        $sunTs = strtotime('+6 days', $monTs);
        $from  = date('Y-m-d', $monTs);
        $to    = date('Y-m-d', $sunTs);

        $occurrences = $model->getMyOccurrences($this->userId, $this->staffType, $from, $to);

        // Group by date for the weekly grid
        $byDate = [];
        foreach ($occurrences as $occ) {
            $byDate[$occ->OccurrenceDate][] = $occ;
        }

        $data = [
            'title'    => 'My Sessions',
            'role'     => $this->role,
            'from'     => $from,
            'to'       => $to,
            'prevWeek' => date('Y-m-d', strtotime('-7 days', $monTs)),
            'nextWeek' => date('Y-m-d', strtotime('+7 days', $monTs)),
            'monTs'    => $monTs,
            'byDate'   => $byDate,
        ];
        $this->view('staff/slots/calendar', $data);
    }

    // =========================================================
    // OCCURRENCE DETAIL  —  /staffslots/occurrence/{id}
    // =========================================================
    public function occurrence($id = null) {
        if (!$id) redirect('staffslots/calendar');

        $model      = $this->model('M_SlotStaff');
        $occurrence = $model->getOccurrenceDetail((int) $id, $this->userId);

        // Null = not found OR this staff member is not assigned → 403/redirect
        if (!$occurrence) redirect('staffslots/calendar');

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action_cancel'])) {
                $reason = trim($_POST['cancel_reason'] ?? '');

                if (!$reason) {
                    $error = 'A cancellation reason is required.';
                } else {
                    $result = $model->cancelOccurrence((int) $id, $reason, $this->userId);

                    if ($result === true) {
                        $success    = 'Session cancelled successfully.';
                        $occurrence = $model->getOccurrenceDetail((int) $id, $this->userId);
                    } elseif ($result === 'already_cancelled') {
                        $error = 'This session is already cancelled.';
                    } elseif ($result === 'not_assigned') {
                        $error = 'You are not assigned to this session.';
                    } else {
                        $error = 'Could not cancel the session. Please try again.';
                    }
                }
            } elseif (isset($_POST['action_update_booking'])) {
                $bookingId = (int) ($_POST['booking_id'] ?? 0);
                $status = trim((string) ($_POST['booking_status'] ?? ''));

                if ($bookingId <= 0 || $status === '') {
                    $error = 'Please choose a valid booking status.';
                } else {
                    $result = $model->markAttendance($bookingId, $status, $this->userId);

                    if ($result === true) {
                        $success = 'Booking status updated successfully.';
                    } elseif ($result === 'not_assigned') {
                        $error = 'You can only update bookings for your own slot sessions.';
                    } elseif ($result === 'not_found') {
                        $error = 'The selected booking could not be found.';
                    } elseif ($result === 'invalid_status') {
                        $error = 'That booking status is not allowed.';
                    } else {
                        $error = 'Could not update the booking status. Please try again.';
                    }
                }
            }
        }

        $data = [
            'title'      => 'Session Detail',
            'role'       => $this->role,
            'occurrence' => $occurrence,
            'bookings'   => $model->getBookingsForOccurrence((int) $id),
            'error'      => $error,
            'success'    => $success,
        ];
        $this->view('staff/slots/occurrence', $data);
    }

    // =========================================================
    // ATTENDANCE  —  /staffslots/attendance/{id}
    // =========================================================
    public function attendance($id = null) {
        if (!$id) redirect('staffslots/calendar');

        $model      = $this->model('M_SlotStaff');
        $occurrence = $model->getOccurrenceDetail((int) $id, $this->userId);

        if (!$occurrence) redirect('staffslots/calendar');

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance'])) {
            $statuses = $_POST['attendance']; // [bookingId => 'completed'|'not_attended'|'confirmed']
            $marked   = 0;
            $failed   = 0;

            foreach ($statuses as $bookingId => $status) {
                if (!in_array($status, ['confirmed', 'attended', 'missed', 'completed', 'not_attended'], true)) {
                    continue;
                }
                $result = $model->markAttendance((int) $bookingId, $status, $this->userId);
                if ($result === true) {
                    $marked++;
                } else {
                    $failed++;
                }
            }

            if ($failed > 0 && $marked === 0) {
                $error = 'Could not save attendance. Please try again.';
            } elseif ($failed > 0) {
                $success = "Saved {$marked} record(s); {$failed} could not be updated.";
            } else {
                $success = "Attendance saved for {$marked} player(s).";
            }

            // Reload bookings after update
        }

        $data = [
            'title'      => 'Mark Attendance',
            'role'       => $this->role,
            'occurrence' => $occurrence,
            'bookings'   => $model->getBookingsForOccurrence((int) $id),
            'error'      => $error,
            'success'    => $success,
        ];
        $this->view('staff/slots/attendance', $data);
    }

    // =========================================================
    // PRIVATE SESSION  —  /staffslots/private
    // =========================================================
    public function private_session() {
        $model = $this->model('M_SlotStaff');
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slotId   = trim($_POST['SlotID']          ?? '');
            $date     = trim($_POST['OccurrenceDate']  ?? '');
            $facility = trim($_POST['FacilityID']      ?? '');

            // Basic validation
            if (!$slotId || !$date || !$facility) {
                $error = 'Time band, date, and facility are all required.';
            } elseif ($date < date('Y-m-d')) {
                $error = 'Session date cannot be in the past.';
            } else {
                $result = $model->createPrivateSession($_POST, $this->userId, $this->staffType);

                if (is_int($result) && $result > 0) {
                    redirect('staffslots/occurrence/' . $result);
                } elseif ($result === 'time_conflict') {
                    $error = 'You are already assigned to another session in that time band on that date.';
                } elseif ($result === 'duplicate') {
                    $error = 'This facility is already booked for that time band on that date.';
                } else {
                    $error = 'Could not create the session. Please try again.';
                }
            }
        }

        $data = [
            'title'      => 'Add Private Session',
            'role'       => $this->role,
            'timeBands'  => $model->getActiveTimeBands(),
            'facilities' => $model->getFacilities(),
            'error'      => $error,
            'post'       => $_POST, // repopulate form on error
        ];
        $this->view('staff/slots/private_session', $data);
    }
}
