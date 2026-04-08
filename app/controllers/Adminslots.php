<?php
class Adminslots extends Controller {

    public function __construct() {
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_template'])) {
            $model->toggleTemplate((int)$_POST['toggle_template']);
            redirect('adminslots/templates');
        }

        $data = [
            'title'     => 'Session Templates',
            'templates' => $model->getTemplates(),
        ];
        $this->view('admin/slots/templates', $data);
    }

    // =========================================================
    // NEW TEMPLATE  —  /adminslots/newtemplate
    // =========================================================
    public function newtemplate() {
        $model = $this->model('M_SlotAdmin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post              = $_POST;
            $post['CreatedBy'] = $_SESSION['user_id'];
            $id                = $model->createTemplate($post);
            redirect('adminslots/staff/' . $id);
        }

        $data = [
            'title'     => 'New Session Template',
            'template'  => null,
            'timeBands' => $model->getActiveTimeBands(),
            'facilities'=> $model->getFacilities(),
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->updateTemplate((int)$id, $_POST);
            redirect('adminslots/staff/' . $id);
        }

        $data = [
            'title'     => 'Edit Template',
            'template'  => $template,
            'timeBands' => $model->getActiveTimeBands(),
            'facilities'=> $model->getFacilities(),
        ];
        $this->view('admin/slots/template_form', $data);
    }

    // =========================================================
    // STAFF ASSIGNMENT  —  /adminslots/staff/{templateId}
    // =========================================================
    public function staff($templateId = null) {
        if (!$templateId) redirect('adminslots/templates');
        $model    = $this->model('M_SlotAdmin');
        $template = $model->getTemplateById((int)$templateId);
        if (!$template) redirect('adminslots/templates');

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['remove_staff'])) {
                $model->removeStaff((int)$_POST['remove_staff']);
                redirect('adminslots/staff/' . $templateId);
            }

            $result = $model->assignStaff(
                (int)$templateId,
                (int)$_POST['user_id'],
                $_POST['staff_type'],
                $_POST['staff_role'],
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

        $data = [
            'title'     => 'Assign Staff — ' . $template->TemplateName,
            'template'  => $template,
            'staff'     => $model->getStaffForTemplate((int)$templateId),
            'coaches'   => $model->getAvailableCoaches(),
            'trainers'  => $model->getAvailableTrainers(),
            'error'     => $error,
            'success'   => $success,
        ];
        $this->view('admin/slots/staff', $data);
    }

    // =========================================================
    // GENERATE OCCURRENCES  —  /adminslots/generate
    // =========================================================
    public function generate() {
        $model  = $this->model('M_SlotAdmin');
        $result = null;
        $error  = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $templateId = (int) ($_POST['template_id'] ?? 0);
            $from       = $_POST['from_date'] ?? '';
            $to         = $_POST['to_date']   ?? '';

            if (!$templateId || !$from || !$to) {
                $error = 'Please select a template and fill in both dates.';
            } elseif ($from > $to) {
                $error = 'Start date must be on or before end date.';
            } else {
                $result = $model->generateOccurrences($templateId, $from, $to, (int) $_SESSION['user_id']);
            }
        }

        $data = [
            'title'     => 'Generate Occurrences',
            'templates' => $model->getActiveTemplates(),
            'result'    => $result,
            'error'     => $error,
        ];
        $this->view('admin/slots/generate', $data);
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
    // OCCURRENCE DETAIL  —  /adminslots/occurrence/{id}
    // =========================================================
    public function occurrence($id = null) {
        if (!$id) redirect('adminslots/calendar');
        $model      = $this->model('M_SlotAdmin');
        $occurrence = $model->getOccurrenceById((int) $id);
        if (!$occurrence) redirect('adminslots/calendar');

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['action_cancel'])) {
                $reason = trim($_POST['cancel_reason'] ?? '');
                if (!$reason) {
                    $error = 'A cancellation reason is required.';
                } elseif ($occurrence->Status === 'cancelled') {
                    $error = 'This occurrence is already cancelled.';
                } else {
                    $model->cancelOccurrence((int) $id, $reason, (int) $_SESSION['user_id']);
                    $success    = 'Occurrence cancelled successfully.';
                    $occurrence = $model->getOccurrenceById((int) $id);
                }

            } elseif (isset($_POST['action_substitute'])) {
                $subUserId  = (int) ($_POST['sub_user_id']      ?? 0);
                $subType    =        $_POST['sub_staff_type']   ?? '';
                $subRole    =        $_POST['sub_staff_role']   ?? 'substitute';
                $replacesId = (int) ($_POST['replaces_user_id'] ?? 0);
                $reason     = trim( $_POST['sub_reason']        ?? '');

                if (!$subUserId || !$subType || !$reason) {
                    $error = 'Substitute user, staff type, and reason are all required.';
                } else {
                    $ok = $model->substituteStaff(
                        (int) $id, $subUserId, $subType, $subRole,
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

        $staff = $model->getStaffForOccurrence(
            (int) $id,
            $occurrence->TemplateID ? (int) $occurrence->TemplateID : null
        );

        $data = [
            'title'      => 'Occurrence Detail',
            'occurrence' => $occurrence,
            'staff'      => $staff,
            'coaches'    => $model->getAvailableCoaches(),
            'trainers'   => $model->getAvailableTrainers(),
            'error'      => $error,
            'success'    => $success,
        ];
        $this->view('admin/slots/occurrence', $data);
    }

    // =========================================================
    // AD-HOC OCCURRENCE  —  /adminslots/adhoc
    // =========================================================
    public function adhoc() {
        $model = $this->model('M_SlotAdmin');
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_POST['SlotID']) || empty($_POST['OccurrenceDate']) || empty($_POST['FacilityID'])) {
                $error = 'Time band, date, and facility are all required.';
            } else {
                $newId = $model->createAdHocOccurrence($_POST, (int) $_SESSION['user_id']);
                if ($newId > 0) {
                    redirect('adminslots/occurrence/' . $newId);
                } else {
                    $error = 'Could not create occurrence — this facility and time band may already be booked on that date.';
                }
            }
        }

        $data = [
            'title'      => 'New Ad-hoc Session',
            'timeBands'  => $model->getActiveTimeBands(),
            'facilities' => $model->getFacilities(),
            'error'      => $error,
        ];
        $this->view('admin/slots/adhoc', $data);
    }
}
