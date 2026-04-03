<?php

require_once __DIR__ . '/M_Session.php';

/**
 * Slot model (admin-created session "open slots" + active claimed sessions).
 *
 * This is a thin wrapper around the existing slot-related methods in `M_Session`
 * so we can keep behavior identical while providing a dedicated model.
 */
class M_Slot {
    private $sessionModel;

    public function __construct() {
        $this->sessionModel = new M_Session();
    }

    // ==================== READ ====================

    public function getOpenSlots(): array {
        return $this->sessionModel->getAllSessions(['status' => 'open']);
    }

    public function getActiveSlots(): array {
        return $this->sessionModel->getAllSessions(['status' => 'active']);
    }

    public function getOpenSlotsCount(): int {
        return $this->sessionModel->getOpenSlotsCount();
    }

    // ==================== CREATE / DELETE ====================

    public function createAdminSlot(array $data) {
        return $this->sessionModel->createAdminSlot($data);
    }

    public function isSlotClaimed(int $slotId): bool {
        return $this->sessionModel->isSlotClaimed($slotId);
    }

    public function cancelAdminSlot(int $slotId): bool {
        return $this->sessionModel->cancelAdminSlot($slotId);
    }
}

