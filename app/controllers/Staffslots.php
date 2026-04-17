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
        $dayLabels = [];
        $attendanceAll = [];
        $attendanceProgram = [];
        $attendancePrivate = [];

        for ($i = 0; $i < 7; $i++) {
            $dayTs = strtotime("+{$i} days", $monTs);
            $dateKey = date('Y-m-d', $dayTs);
            $dayLabels[] = date('D j M', $dayTs);
            $attendanceAll[$dateKey] = 0;
            $attendanceProgram[$dateKey] = 0;
            $attendancePrivate[$dateKey] = 0;
        }

        foreach ($occurrences as $occ) {
            $byDate[$occ->OccurrenceDate][] = $occ;

            $occDate = (string) ($occ->OccurrenceDate ?? '');
            if (!array_key_exists($occDate, $attendanceAll)) {
                continue;
            }

            $slotType = strtolower((string) ($occ->SlotType ?? 'program'));
            $participantCount = $slotType === 'program'
                ? (int) ($occ->EligiblePlayerCount ?? 0)
                : (int) ($occ->BookingCount ?? 0);

            $attendanceAll[$occDate] += $participantCount;
            if ($slotType === 'program') {
                $attendanceProgram[$occDate] += $participantCount;
            } else {
                $attendancePrivate[$occDate] += $participantCount;
            }
        }

        $attendanceValues = [
            'all' => array_values($attendanceAll),
            'program' => array_values($attendanceProgram),
            'private' => array_values($attendancePrivate),
        ];

        $data = [
            'title'    => 'My Sessions',
            'role'     => $this->role,
            'from'     => $from,
            'to'       => $to,
            'prevWeek' => date('Y-m-d', strtotime('-7 days', $monTs)),
            'nextWeek' => date('Y-m-d', strtotime('+7 days', $monTs)),
            'monTs'    => $monTs,
            'byDate'   => $byDate,
            'attendanceChartData' => [
                'labels' => $dayLabels,
                'datasets' => [
                    'all' => ['label' => 'All Sessions', 'values' => $attendanceValues['all']],
                    'program' => ['label' => 'Program Sessions', 'values' => $attendanceValues['program']],
                    'private' => ['label' => 'Private Sessions', 'values' => $attendanceValues['private']],
                ],
            ],
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
            } elseif (isset($_POST['action_update_occurrence_status'])) {
                $status = trim((string) ($_POST['occurrence_status'] ?? ''));
                $reason = trim((string) ($_POST['occurrence_status_reason'] ?? ''));
                $result = $model->updateOccurrenceStatus((int) $id, $status, $this->userId, $reason);

                if ($result === true) {
                    $success = 'Session occurrence status updated successfully.';
                    $occurrence = $model->getOccurrenceDetail((int) $id, $this->userId);
                } elseif ($result === 'invalid_status') {
                    $error = 'That occurrence status is not allowed.';
                } elseif ($result === 'not_past') {
                    $error = 'Occurrence status can only be updated after the session has ended.';
                } elseif ($result === 'reason_required') {
                    $error = 'Please provide a reason when marking a session as cancelled.';
                } elseif ($result === 'not_assigned') {
                    $error = 'You are not assigned to this session.';
                } else {
                    $error = 'Could not update the occurrence status. Please try again.';
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
                    flash('session_message', 'Private session request submitted successfully. An admin will review availability and approve it if the facility is free.', 'alert alert-success');
                    redirect('staffslots/calendar');
                } elseif ($result === 'time_conflict') {
                    $error = 'You are already assigned to another session in that time band on that date.';
                } elseif ($result === 'duplicate') {
                    $error = 'You already have a pending request for that date, time band, and facility.';
                } else {
                    $error = 'Could not submit the request. Please try again.';
                }
            }
        }

        $data = [
            'title'      => 'Request Private Session',
            'role'       => $this->role,
            'timeBands'  => $model->getActiveTimeBands(),
            'facilities' => $model->getFacilities(),
            'error'      => $error,
            'post'       => $_POST, // repopulate form on error
        ];
        $this->view('staff/slots/private_session', $data);
    }

    // =========================================================
    // PAST REQUESTS  —  /staffslots/past_requests
    // =========================================================
    public function past_requests() {
        $model = $this->model('M_SlotStaff');

        $data = [
            'title' => 'Past Requests',
            'role' => $this->role,
            'requests' => $model->getPrivateSessionRequestsByStaff($this->userId),
        ];

        $this->view('staff/slots/past_requests', $data);
    }
}
