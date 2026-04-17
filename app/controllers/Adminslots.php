<?php
class Adminslots extends Controller {

    public function __construct() {
        require_once APPROOT . '/libraries/SlotBookingService.php';
        requireAuth(['Admin']);
    }

    // Default: redirect to time bands
    public function index() {
        redirect('adminslots/timeslots');
    }

    // =========================================================
    // TIME BANDS  —  /adminslots/timeslots
    // =========================================================
    public function timeslots() {
        $model = $this->model('M_SlotAdmin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_band'])) {
            $model->toggleTimeBand((int)$_POST['toggle_band']);
            redirect('adminslots/timeslots');
        }

        $data = [
            'title'     => 'Manage Time Bands',
            'timeBands' => $model->getTimeBands(),
        ];
        $this->view('admin/slots/timeslots', $data);
    }

    // =========================================================
    // TEMPLATES  —  /adminslots/templates
    // =========================================================
    public function templates() {
        $model = $this->model('M_SlotAdmin');
        $templateNotice = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_template'])) {
            $model->toggleTemplate((int)$_POST['toggle_template']);
            redirect('adminslots/templates');
        }

        if (isset($_GET['created'])) {
            $templateNotice = 'Template created successfully.';
        } elseif (isset($_GET['saved'])) {
            $templateNotice = 'Template updated successfully.';
        }

        $data = [
            'title'     => 'Session Templates',
            'templates' => $model->getTemplates(),
            'timeBands' => $model->getTimeBands(),
            'templateNotice' => $templateNotice,
        ];
        $this->view('admin/slots/templates', $data);
    }

    // =========================================================
    // TEMPLATE DETAIL  —  /adminslots/template_detail/{id}
    // =========================================================
    public function template_detail($id = null) {
        if (!$id) redirect('adminslots/templates');

        $model = $this->model('M_SlotAdmin');
        $slotService = new SlotBookingService();
        $template = $model->getTemplateById((int)$id);
        if (!$template) redirect('adminslots/templates');

        $staff = $model->getStaffForTemplate((int)$id);
        $occurrences = $model->getOccurrencesForTemplate((int)$id);
        $eligiblePlayerCount = 0;
        if (($template->SlotType ?? '') === 'program') {
            $eligiblePlayerCount = $slotService->getEligiblePlayerCountForTemplate((int)$id);
        }

        $data = [
            'title' => 'Template Detail - ' . ($template->temp_code ?? ('T' . $template->TemplateID)),
            'template' => $template,
            'staff' => $staff,
            'occurrences' => $occurrences,
            'occurrenceCount' => count($occurrences),
            'staffCount' => count($staff),
            'eligiblePlayerCount' => $eligiblePlayerCount,
        ];

        $this->view('admin/slots/template_detail', $data);
    }

    // =========================================================
    // NEW TEMPLATE  —  /adminslots/newtemplate
    // =========================================================
    public function newtemplate() {
        $model = $this->model('M_SlotAdmin');
        $error = null;
        $templateData = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = $_POST;
            $post['CreatedBy'] = $_SESSION['user_id'];
            $templateData = (object) $post;

            try {
                $id = $model->createTemplate($post);
                $slotType = strtolower(trim($post['SlotType'] ?? ''));
                if ($slotType === 'facility_only') {
                    redirect('adminslots/generate?created=1&template_id=' . $id);
                } else {
                    redirect('adminslots/staff/' . $id . '?created=1');
                }
            } catch (InvalidArgumentException $e) {
                $error = $e->getMessage();
            }
        }

        $data = [
            'title'     => 'New Session Template',
            'template'  => $templateData,
            'error'     => $error,
            'timeBands' => $model->getActiveTimeBands(),
            'facilities'=> $model->getFacilities(),
            'membershipPlans' => $model->getActiveMembershipPlans(),
        ];
        $this->view('admin/slots/template_form', $data);
    }

    // =========================================================
    // EDIT TEMPLATE  —  /adminslots/edittemplate/{id}
    // =========================================================
    public function edittemplate($id = null) {
        if (!$id) redirect('adminslots/templates');
        $model    = $this->model('M_SlotAdmin');
        $template = $model->getTemplateById((int)$id);
        if (!$template) redirect('adminslots/templates');
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateData = (object) array_merge((array) $template, $_POST);

            try {
                $model->updateTemplate((int)$id, $_POST);
                redirect('adminslots/generate?saved=1&template_id=' . (int)$id);
            } catch (InvalidArgumentException $e) {
                $error = $e->getMessage();
                $template = $templateData;
            }
        }

        $data = [
            'title'     => 'Edit Template',
            'template'  => $template,
            'error'     => $error,
            'timeBands' => $model->getActiveTimeBands(),
            'facilities'=> $model->getFacilities(),
            'membershipPlans' => $model->getActiveMembershipPlans(),
        ];
        $this->view('admin/slots/template_form', $data);
    }

    // =========================================================
    // STAFF ASSIGNMENT  —  /adminslots/staff/{templateId}
    // =========================================================
    public function staff($templateId = null) {
        if (!$templateId) redirect('adminslots/templates');
        $userModel = $this->model('M_Users');
        $model    = $this->model('M_SlotAdmin');
        $template = $model->getTemplateById((int)$templateId);
        if (!$template) redirect('adminslots/templates');

        $error   = null;
        $success = null;
        $fromCreate = isset($_GET['created']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['remove_staff'])) {
                $model->removeStaff((int)$_POST['remove_staff']);
                redirect('adminslots/staff/' . $templateId);
            }

            // "Done" button — proceed to occurrence generator
            if (isset($_POST['proceed_generate'])) {
                redirect('adminslots/generate?created=1&template_id=' . $templateId);
            }

            $result = $model->assignStaff(
                (int)$templateId,
                (int)$_POST['user_id'],
                $_POST['staff_type'],
                'lead',  // Default to 'lead' role (removed from form as per UI changes)
                (int)$_SESSION['user_id']
            );

            if ($result === true) {
                $success = 'Staff member assigned successfully.';
            } elseif ($result === 'type_mismatch') {
                $error = 'Cannot assign: this template requires a ' . ucfirst($template->StaffType) . '. You selected a different staff type.';
            } elseif ($result === 'role_mismatch') {
                $error = 'Cannot assign: the selected user\'s role does not match the staff type.';
            } elseif ($result === 'duplicate') {
                $error = 'This staff member is already assigned to this template.';
            } else {
                $error = 'User not found.';
            }
        }

        // Only show coaches in the assignment matrix (exclude admins/managers)
        $coaches = $userModel->getAllCoachProfiles();
        $coachAssignments = $userModel->getCoachSkillAgeGroupAssignments();
        $coachAssignedPlayers = [];

        foreach ($coaches as $coach) {
            $coachAssignedPlayers[(int)$coach->coach_id] = $userModel->getCoachAssignedPlayers((int)$coach->coach_id);
        }
        $data = [
            'title'     => 'Assign Staff — ' . ($template->temp_code ?? 'TMP') . ' · ' . $template->TemplateName,
            'template'  => $template,
            'staff'     => $model->getStaffForTemplate((int)$templateId),
            'coaches'   => $coaches,
            'coachAssignments' => $coachAssignments,
            'coachAssignedPlayers' => $coachAssignedPlayers,
            'trainers'  => $model->getAvailableTrainers(),
            'error'     => $error,
            'success'   => $success,
            'fromCreate' => $fromCreate,
        ];
        $this->view('admin/slots/admin_slots_staff', $data);
    }

    // =========================================================
    // PRIVATE SESSION REQUESTS  —  /adminslots/private_requests
    // =========================================================
    public function private_requests() {
        $model = $this->model('M_SlotAdmin');
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestId = (int) ($_POST['request_id'] ?? 0);
            $action = strtolower(trim((string) ($_POST['request_action'] ?? '')));
            $reviewNotes = trim((string) ($_POST['review_notes'] ?? ''));

            if ($requestId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
                $error = 'Please choose a valid request action.';
            } else {
                $result = $model->reviewPrivateSessionRequest(
                    $requestId,
                    $action === 'approve' ? 'approved' : 'rejected',
                    (int) $_SESSION['user_id'],
                    $reviewNotes !== '' ? $reviewNotes : null
                );

                if ($result === true || (is_int($result) && $result > 0)) {
                    if ($action === 'approve') {
                        $message = is_int($result)
                            ? 'Private session request approved and occurrence #' . $result . ' created.'
                            : 'Private session request approved successfully.';
                    } else {
                        $message = 'Private session request rejected successfully.';
                    }
                    flash('private_session_request', $message, 'alert alert-success');
                    redirect('adminslots/private_requests');
                } elseif ($result === 'time_conflict') {
                    $error = 'The requested facility is already occupied for that date and time.';
                } elseif ($result === 'already_reviewed') {
                    $error = 'This request has already been reviewed.';
                } elseif ($result === 'not_found') {
                    $error = 'The selected request could not be found.';
                } else {
                    $error = 'Could not process the request. Please try again.';
                }
            }
        }

        $data = [
            'title' => 'Private Session Requests',
            'requests' => $model->getPrivateSessionRequests(),
            'error' => $error,
            'success' => $success,
        ];
        $this->view('admin/slots/private_requests', $data);
    }

    // =========================================================
    // GENERATE OCCURRENCES  —  /adminslots/generate
    // =========================================================
    public function generate() {
        $model  = $this->model('M_SlotAdmin');
        $result = null;
        $error  = null;
        $notice = null;
        $selectedTemplateId = (int) ($_GET['template_id'] ?? 0);
        $existingOccurrences = [];

        if (isset($_GET['created'])) {
            $notice = 'Template created successfully. You can now generate occurrences for it.';
        } elseif (isset($_GET['saved'])) {
            $notice = 'Template updated successfully. You can now generate occurrences for it.';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateId = (int) ($_POST['template_id'] ?? 0);
            $from       = $_POST['from_date'] ?? '';
            $to         = $_POST['to_date']   ?? '';
            $selectedTemplateId = $templateId;

            if (!$templateId || !$from || !$to) {
                $error = 'Please select a template and fill in both dates.';
            } elseif ($from > $to) {
                $error = 'Start date must be on or before end date.';
            } else {
                // Get template to check day of week
                $templates = $model->getActiveTemplates();
                $template = null;
                foreach ($templates as $t) {
                    if ($t->TemplateID == $templateId) {
                        $template = $t;
                        break;
                    }
                }

                if (!$template) {
                    $error = 'Template not found.';
                } else {
                    // Validate dates match template's day of week (if specific day required)
                    if ($template->DayOfWeek > 0) {
                        $dayNames = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $fromDay = (int) date('N', strtotime($from)); // 1=Monday, 7=Sunday
                        $toDay = (int) date('N', strtotime($to));

                        if ($fromDay !== (int)$template->DayOfWeek) {
                            $error = "From date must be a {$dayNames[(int)$template->DayOfWeek]} (template's scheduled day). '{$from}' is a {$dayNames[$fromDay]}.";
                        } elseif ($toDay !== (int)$template->DayOfWeek) {
                            $error = "To date must be a {$dayNames[(int)$template->DayOfWeek]} (template's scheduled day). '{$to}' is a {$dayNames[$toDay]}.";
                        }
                    }

                    if (!$error) {
                        $result = $model->generateOccurrences($templateId, $from, $to, (int) $_SESSION['user_id']);
                        if (!empty($result['error'])) {
                            $error = $result['error'];
                        }
                        if (!empty($result['inserted']) && $result['inserted'] > 0) {
                            redirect('adminslots/calendar?generated=1&count=' . $result['inserted']);
                        }
                    }
                }
            }
        }

        if ($selectedTemplateId > 0) {
            $existingOccurrences = $model->getOccurrencesForTemplate($selectedTemplateId);
        }

        $data = [
            'title'     => 'Generate Occurrences',
            'templates' => $model->getActiveTemplates(),
            'result'    => $result,
            'error'     => $error,
            'notice'    => $notice,
            'selectedTemplateId' => $selectedTemplateId,
            'existingOccurrences' => $existingOccurrences,
        ];
        $this->view('admin/slots/generate', $data);
    }

    private function handleOccurrenceGeneration($model, ?int $defaultTemplateId = null): array {
        $templateId = (int) ($_POST['template_id'] ?? $defaultTemplateId ?? 0);
        $from = $_POST['from_date'] ?? '';
        $to = $_POST['to_date'] ?? '';

        $values = [
            'template_id' => $templateId > 0 ? (string)$templateId : '',
            'from_date' => $from,
            'to_date' => $to,
        ];

        if (!$templateId || !$from || !$to) {
            return [
                'result' => null,
                'error' => 'Please select a template and fill in both dates.',
                'values' => $values,
            ];
        }

        if ($from > $to) {
            return [
                'result' => null,
                'error' => 'Start date must be on or before end date.',
                'values' => $values,
            ];
        }

        $result = $model->generateOccurrences($templateId, $from, $to, (int) $_SESSION['user_id']);

        if (!empty($result['error'])) {
            return [
                'result' => $result,
                'error' => $result['error'],
                'values' => $values,
            ];
        }

        return [
            'result' => $result,
            'error' => $result['error'] ?? null,
            'values' => $values,
        ];
    }

    // =========================================================
    // CALENDAR  —  /adminslots/calendar
    // =========================================================
    public function calendar() {
        $model      = $this->model('M_SlotAdmin');
        $startParam = $_GET['start'] ?? null;

        if ($startParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startParam)) {
            $weekTs = strtotime($startParam);
        } else {
            $weekTs = time();
        }

        // Normalise to Monday of that week
        $dow   = (int) date('N', $weekTs);
        $monTs = strtotime('-' . ($dow - 1) . ' days', $weekTs);
        $sunTs = strtotime('+6 days', $monTs);
        $from  = date('Y-m-d', $monTs);
        $to    = date('Y-m-d', $sunTs);

        $occurrences = $model->getOccurrencesForCalendar($from, $to);

        // Group by date for the view
        $byDate = [];
        foreach ($occurrences as $occ) {
            $byDate[$occ->OccurrenceDate][] = $occ;
        }

        $data = [
            'title'    => 'Occurrence Calendar',
            'from'     => $from,
            'to'       => $to,
            'prevWeek' => date('Y-m-d', strtotime('-7 days', $monTs)),
            'nextWeek' => date('Y-m-d', strtotime('+7 days', $monTs)),
            'byDate'   => $byDate,
            'monTs'    => $monTs,
        ];
        $this->view('admin/slots/calendar', $data);
    }

    // =========================================================
    // WEEKLY TIMETABLE  —  /adminslots/weeklytimetable
    // =========================================================
    public function weeklytimetable() {
        $model      = $this->model('M_SlotAdmin');
        $startParam = $_GET['start'] ?? null;

        if ($startParam && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startParam)) {
            $weekTs = strtotime($startParam);
        } else {
            $weekTs = time();
        }

        $dow   = (int) date('N', $weekTs);
        $monTs = strtotime('-' . ($dow - 1) . ' days', $weekTs);
        $sunTs = strtotime('+6 days', $monTs);
        $from  = date('Y-m-d', $monTs);
        $to    = date('Y-m-d', $sunTs);

        $occurrences = $model->getOccurrencesForCalendar($from, $to);
        $timeBands = $model->getTimeBands();

        $grid = [];
        foreach ($occurrences as $occurrence) {
            $slotId = (int) ($occurrence->SlotID ?? 0);
            if ($slotId <= 0) {
                continue;
            }

            $dateKey = (string) $occurrence->OccurrenceDate;
            if (!isset($grid[$slotId])) {
                $grid[$slotId] = [];
            }
            if (!isset($grid[$slotId][$dateKey])) {
                $grid[$slotId][$dateKey] = [];
            }

            $grid[$slotId][$dateKey][] = $occurrence;
        }

        $data = [
            'title'     => 'Weekly Timetable',
            'from'      => $from,
            'to'        => $to,
            'prevWeek'  => date('Y-m-d', strtotime('-7 days', $monTs)),
            'nextWeek'  => date('Y-m-d', strtotime('+7 days', $monTs)),
            'monTs'     => $monTs,
            'timeBands' => $timeBands,
            'grid'      => $grid,
        ];
        $this->view('admin/slots/weekly_timetable', $data);
    }

    // =========================================================
    // OCCURRENCE DETAIL  —  /adminslots/occurrence/{id}
    // =========================================================
    public function occurrence($id = null) {
        if (!$id) redirect('adminslots/calendar');
        $model      = $this->model('M_SlotAdmin');
        $occurrence = $model->getOccurrenceById((int) $id);
        if (!$occurrence) redirect('adminslots/calendar');

        $error   = null;
        $success = null;
        $canCancelError = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['action_cancel'])) {
                $reason = trim($_POST['cancel_reason'] ?? '');
                if (!$reason) {
                    $error = 'A cancellation reason is required.';
                } elseif ($occurrence->Status === 'cancelled') {
                    $error = 'This occurrence is already cancelled.';
                } else {
                    // Check cancellation eligibility
                    $canCancel = $this->canCancelOccurrence($occurrence);
                    if ($canCancel !== true) {
                        $error = $canCancel;
                    } else {
                        $model->cancelOccurrence((int) $id, $reason, (int) $_SESSION['user_id']);
                        $success    = 'Occurrence cancelled successfully.';
                        $occurrence = $model->getOccurrenceById((int) $id);
                    }
                }

            } elseif (isset($_POST['action_substitute'])) {
                $subUserId  = (int) ($_POST['sub_user_id']      ?? 0);
                $subType    =        $_POST['sub_staff_type']   ?? '';
                $replacesId = (int) ($_POST['replaces_user_id'] ?? 0);
                $reason     = trim( $_POST['sub_reason']        ?? '');

                if (!$subUserId || !$subType || !$reason) {
                    $error = 'Substitute user, staff type, and reason are all required.';
                } else {
                    $ok = $model->substituteStaff(
                        (int) $id, $subUserId, $subType,
                        $replacesId, $reason, (int) $_SESSION['user_id']
                    );
                    if ($ok) {
                        $success = 'Staff substitute recorded successfully.';
                    } else {
                        $error = 'Could not save substitute — this person may already be assigned to this occurrence.';
                    }
                }
            }
        }

        // Check cancellation eligibility for display
        if ($occurrence->Status !== 'cancelled') {
            $canCancelError = $this->canCancelOccurrence($occurrence);
            if ($canCancelError === true) {
                $canCancelError = null;
            }
        }

        $staff = $model->getStaffForOccurrence(
            (int) $id,
            $occurrence->TemplateID ? (int) $occurrence->TemplateID : null
        );

        $data = [
            'title'           => 'Occurrence Detail',
            'occurrence'      => $occurrence,
            'staff'           => $staff,
            'bookings'        => $model->getBookingsForOccurrence((int) $id),
            'coaches'         => $model->getAvailableCoaches(),
            'trainers'        => $model->getAvailableTrainers(),
            'error'           => $error,
            'success'         => $success,
            'canCancelError'  => $canCancelError,
        ];
        $this->view('admin/slots/occurrence', $data);
    }

    private function canCancelOccurrence($occurrence): bool|string {
        $occDateTime = new DateTime($occurrence->OccurrenceDate . ' ' . $occurrence->StartTime, new DateTimeZone('UTC'));
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $timeUntilOcc = $now->diff($occDateTime);

        // Check 48-hour rule for program sessions
        if (($occurrence->SlotType ?? '') === 'program') {
            if ($timeUntilOcc->invert === 1) {
                // Already happened
                return 'Cannot cancel a past occurrence.';
            }
            $hoursRemaining = $timeUntilOcc->h + ($timeUntilOcc->days * 24);
            if ($hoursRemaining < 48) {
                return 'Cannot cancel within 48 hours of the scheduled time.';
            }
        } elseif (($occurrence->SlotType ?? '') === 'private' || ($occurrence->SlotType ?? '') === 'facility_only') {
            // For private or facility-only, can cancel if no bookings
            if (($occurrence->BookingCount ?? 0) > 0) {
                return 'Cannot cancel: this occurrence has bookings. Contact affected players first.';
            }
        }

        return true;
    }

    // =========================================================
    // ACADEMY EVENT  —  /adminslots/adhoc
    // =========================================================
    public function adhoc() {
        $eventModel = $this->model('Event');
        $error = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $requiredFields = [
                'event_name',
                'event_type',
                'event_venue',
                'start_date',
                'start_time',
                'end_date',
                'end_time',
                'primary_contact',
                'contact_email',
                'contact_phone',
            ];

            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $error = 'Please complete all required event fields.';
            } else {
                $registrationStart = !empty($_POST['registration_start']) ? str_replace('T', ' ', $_POST['registration_start']) . ':00' : null;
                $registrationEnd = !empty($_POST['registration_end']) ? str_replace('T', ' ', $_POST['registration_end']) . ':00' : null;

                $eventData = [
                    'name' => trim($_POST['event_name']),
                    'type' => $_POST['event_type'],
                    'category' => 'academy',
                    'description' => !empty($_POST['event_description']) ? trim($_POST['event_description']) : null,
                    'start_date' => $_POST['start_date'] . ' ' . $_POST['start_time'] . ':00',
                    'end_date' => $_POST['end_date'] . ' ' . $_POST['end_time'] . ':00',
                    'location' => trim($_POST['event_venue']),
                    'status' => !empty($_POST['event_status']) ? $_POST['event_status'] : 'upcoming',
                    'max_participants' => !empty($_POST['max_participants']) ? (int) $_POST['max_participants'] : null,
                    'registration_fee' => !empty($_POST['registration_fee']) ? (float) $_POST['registration_fee'] : null,
                    'registration_start' => $registrationStart,
                    'registration_end' => $registrationEnd,
                    'primary_contact' => trim($_POST['primary_contact']),
                    'contact_email' => trim($_POST['contact_email']),
                    'contact_phone' => trim($_POST['contact_phone']),
                ];

                $created = $eventModel->createEvent($eventData);
                if ($created) {
                    flash('event_message', 'Academy event "' . $eventData['name'] . '" created successfully.', 'alert alert-success');
                    redirect('admin/events');
                } else {
                    $error = 'Could not create academy event. Please check the entered details and try again.';
                }
            }
        }

        $data = [
            'title'      => 'New Academy Event',
            'error'      => $error,
            'success'    => $success,
        ];
        $this->view('admin/slots/adhoc', $data);
    }

    // =========================================================
    // CLEAR MEDICAL FLAG  —  POST /adminslots/clearmedical/{bookingId}
    // =========================================================
    public function clearmedical($bookingId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$bookingId) {
            redirect('adminslots/calendar');
        }

        $model   = $this->model('M_SlotAdmin');
        $booking = $model->getBookingById((int) $bookingId);

        $model->clearMedicalFlag((int) $bookingId, (int) $_SESSION['user_id']);

        if ($booking) {
            redirect('adminslots/occurrence/' . $booking->OccurrenceID);
        }
        redirect('adminslots/calendar');
    }
}
