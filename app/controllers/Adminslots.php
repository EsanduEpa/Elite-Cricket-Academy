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
}
