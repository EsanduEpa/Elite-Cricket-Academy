<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_slots.css?v=<?php echo time(); ?>">

<?php
$plan = $data['plan'] ?? [];
$currentTab = $data['current_tab'] ?? 'my_sessions';
$catalog = $data['catalog'] ?? ['rows' => []];
$catalogPagination = $catalog['pagination'] ?? ['rows' => [], 'page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'has_multiple_pages' => false];
$catalogRows = $catalogPagination['rows'] ?? [];
$sessions = $data['sessions'] ?? [];
$upcomingPagination = $sessions['upcoming'] ?? ['rows' => [], 'page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'has_multiple_pages' => false];
$historyPagination = $sessions['history'] ?? ['rows' => [], 'page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'has_multiple_pages' => false];

$buildPageUrl = static function (array $overrides = []) {
    $params = $_GET;
    foreach ($overrides as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
            continue;
        }
        $params[$key] = $value;
    }

    $query = http_build_query($params);
    $path = strtok($_SERVER['REQUEST_URI'] ?? '', '?') ?: '';
    return $path . ($query !== '' ? '?' . $query : '');
};

$renderPagination = static function (array $pagination, string $pageKey) use ($buildPageUrl) {
    if (empty($pagination['has_multiple_pages'])) {
        return;
    }

    $currentPage = (int) ($pagination['page'] ?? 1);
    $totalPages = (int) ($pagination['total_pages'] ?? 1);

    echo '<div class="table-pagination">';
    echo '<div class="table-pagination-summary">Showing page ' . $currentPage . ' of ' . $totalPages . '</div>';
    echo '<div class="table-pagination-links">';

    if ($currentPage > 1) {
        echo '<a class="table-page-link" href="' . htmlspecialchars($buildPageUrl([$pageKey => $currentPage - 1])) . '">Previous</a>';
    }

    for ($page = 1; $page <= $totalPages; $page++) {
        $class = 'table-page-link' . ($page === $currentPage ? ' active' : '');
        echo '<a class="' . $class . '" href="' . htmlspecialchars($buildPageUrl([$pageKey => $page])) . '">' . $page . '</a>';
    }

    if ($currentPage < $totalPages) {
        echo '<a class="table-page-link" href="' . htmlspecialchars($buildPageUrl([$pageKey => $currentPage + 1])) . '">Next</a>';
    }

    echo '</div>';
    echo '</div>';
};

$slotTypeLabel = static function ($slotType): string {
    $slotType = strtolower((string) $slotType);
    if ($slotType === 'facility_only') {
        return 'Facility';
    }
    if ($slotType === 'private') {
        return 'Private';
    }
    return 'Program';
};

$staffLabel = static function ($row): string {
    $staffType = strtolower((string) ($row->StaffType ?? ''));
    if ($staffType === 'trainer') {
        return 'Trainer';
    }
    if ($staffType === 'coach') {
        return 'Coach';
    }
    return 'Staff';
};

$blockLabels = [
    'already_booked' => 'Already booked',
    'active_injury' => 'Medical hold',
    'full' => 'Full',
    'no_subscription' => 'Subscription required',
    'plan_mismatch' => 'Plan upgrade required',
    'assigned_program' => 'Assigned in your plan',
    'time_conflict' => 'Time conflict',
    'weekly_private_limit' => 'Weekly private limit reached',
    'weekly_facility_limit' => 'Weekly facility limit reached',
];
?>

<div class="player-layout">
    <?php $playerActivePage = 'bookings'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-calendar-check"></i> <?php echo htmlspecialchars($data['title'] ?? 'Bookings'); ?></h1>
                    <p><?php echo htmlspecialchars($data['page_description'] ?? 'Manage your player bookings.'); ?></p>
                </div>
                <div class="booking-plan-summary">
                    <span class="booking-plan-chip"><?php echo htmlspecialchars($plan['label'] ?? 'Membership'); ?></span>
                    <p><?php echo htmlspecialchars($plan['summary'] ?? ''); ?></p>
                </div>
            </div>
        </div>

        <div class="booking-subnav">
            <?php foreach (($data['subnav'] ?? []) as $item): ?>
                <?php if (!empty($item['enabled'])): ?>
                    <a href="<?php echo htmlspecialchars($item['href']); ?>" class="booking-subnav-item <?php echo !empty($item['active']) ? 'active' : ''; ?>">
                        <i class="fas <?php echo htmlspecialchars($item['icon']); ?>"></i>
                        <?php echo htmlspecialchars($item['label']); ?>
                    </a>
                <?php else: ?>
                    <span class="booking-subnav-item disabled" aria-disabled="true" title="Not available for your membership">
                        <i class="fas <?php echo htmlspecialchars($item['icon']); ?>"></i>
                        <?php echo htmlspecialchars($item['label']); ?>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="booking-rules">
            <div class="booking-rule-card">
                <strong>Facility limit</strong>
                <span>Up to <?php echo (int) ($plan['facility_weekly_limit'] ?? 3); ?> facility slots per week</span>
            </div>
            <div class="booking-rule-card">
                <strong>Overlap rule</strong>
                <span>One booking per time slot</span>
            </div>
            <?php if (!empty($plan['private_weekly_limit'])): ?>
                <div class="booking-rule-card">
                    <strong>Private limit</strong>
                    <span>Up to <?php echo (int) $plan['private_weekly_limit']; ?> private sessions per week</span>
                </div>
            <?php endif; ?>
            <div class="booking-rule-card">
                <strong>Cancellation</strong>
                <span>Cancel before <?php echo (int) ($plan['cancel_window_hours'] ?? 24); ?> hours</span>
            </div>
        </div>

        <?php if (!empty($_SESSION['slot_success'])): ?>
            <div class="flash-msg flash-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($_SESSION['slot_success']); unset($_SESSION['slot_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['slot_error'])): ?>
            <div class="flash-msg flash-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($_SESSION['slot_error']); unset($_SESSION['slot_error']); ?>
            </div>
        <?php endif; ?>

        <?php if ($currentTab === 'my_sessions'): ?>
            <div class="schedule-card">
                <div class="card-header">
                    <div class="section-title">Upcoming Sessions</div>
                </div>
                <div class="card-content">
                    <?php if (empty($upcomingPagination['rows'])): ?>
                        <div class="slots-empty-state">
                            <i class="fas fa-calendar-times slots-empty-state-icon"></i>
                            <p class="slots-empty-state-title">No upcoming sessions found.</p>
                        </div>
                    <?php else: ?>
                        <table class="dashboard-table slots-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>Session</th>
                                    <th>Staff</th>
                                    <th>Facility</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingPagination['rows'] as $row): ?>
                                    <?php $hoursLeft = (strtotime(($row->OccurrenceDate ?? '') . ' ' . ($row->StartTime ?? '00:00:00')) - time()) / 3600; ?>
                                    <tr>
                                        <td><strong><?php echo date('D, d M Y', strtotime($row->OccurrenceDate)); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($row->SlotLabel ?? '-'); ?><br>
                                            <small class="slots-time-note">
                                                <?php echo date('g:i A', strtotime($row->StartTime)); ?> - <?php echo date('g:i A', strtotime($row->EndTime)); ?>
                                            </small>
                                        </td>
                                        <td><span class="slot-type-badge badge-<?php echo htmlspecialchars((string) ($row->SlotType ?? 'program')); ?>"><?php echo htmlspecialchars($slotTypeLabel($row->SlotType ?? 'program')); ?></span></td>
                                        <td><?php echo htmlspecialchars($row->TemplateName ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row->StaffNames ?? (($row->BookingSource ?? '') === 'facility_booking' ? '-' : 'Assigned')); ?></td>
                                        <td><?php echo htmlspecialchars($row->FacilityName ?? '-'); ?></td>
                                        <td><span class="status-pill status-<?php echo htmlspecialchars(strtolower((string) ($row->Status ?? 'scheduled'))); ?>"><?php echo htmlspecialchars(ucfirst((string) ($row->Status ?? 'scheduled'))); ?></span></td>
                                        <td>
                                            <?php if ((float) ($row->AmountCharged ?? 0) > 0): ?>
                                                LKR <?php echo number_format((float) $row->AmountCharged, 2); ?>
                                            <?php elseif (($row->PaymentStatus ?? '') === 'paid'): ?>
                                                Paid
                                            <?php else: ?>
                                                Included
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row->BookingID) && strtolower((string) ($row->Status ?? '')) === 'confirmed' && $hoursLeft >= ((int) ($plan['cancel_window_hours'] ?? 24))): ?>
                                                <form method="POST" action="<?php echo URLROOT; ?>/playerslots/cancel">
                                                    <input type="hidden" name="booking_id" value="<?php echo (int) $row->BookingID; ?>">
                                                    <button type="submit" class="btn-inline btn-danger">Cancel</button>
                                                </form>
                                            <?php elseif (!empty($row->BookingID) && strtolower((string) ($row->Status ?? '')) === 'confirmed'): ?>
                                                <span class="table-note">Window closed</span>
                                            <?php else: ?>
                                                <span class="table-note">Assigned</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php $renderPagination($upcomingPagination, 'upcoming_page'); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="schedule-card">
                <div class="card-header">
                    <div class="section-title">History</div>
                </div>
                <div class="card-content">
                    <?php if (empty($historyPagination['rows'])): ?>
                        <div class="slots-empty-state">
                            <p class="slots-empty-state-title">No previous sessions found.</p>
                        </div>
                    <?php else: ?>
                        <table class="dashboard-table slots-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>Session</th>
                                    <th>Staff</th>
                                    <th>Facility</th>
                                    <th>Status</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historyPagination['rows'] as $row): ?>
                                    <tr>
                                        <td><strong><?php echo date('D, d M Y', strtotime($row->OccurrenceDate)); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($row->SlotLabel ?? '-'); ?><br>
                                            <small class="slots-time-note">
                                                <?php echo date('g:i A', strtotime($row->StartTime)); ?> - <?php echo date('g:i A', strtotime($row->EndTime)); ?>
                                            </small>
                                        </td>
                                        <td><span class="slot-type-badge badge-<?php echo htmlspecialchars((string) ($row->SlotType ?? 'program')); ?>"><?php echo htmlspecialchars($slotTypeLabel($row->SlotType ?? 'program')); ?></span></td>
                                        <td><?php echo htmlspecialchars($row->TemplateName ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row->StaffNames ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row->FacilityName ?? '-'); ?></td>
                                        <td><span class="status-pill status-<?php echo htmlspecialchars(strtolower((string) ($row->Status ?? 'scheduled'))); ?>"><?php echo htmlspecialchars(ucfirst((string) ($row->Status ?? 'scheduled'))); ?></span></td>
                                        <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', (string) ($row->BookingSource ?? 'system')))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php $renderPagination($historyPagination, 'history_page'); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php if ($currentTab === 'facility_booking'): ?>
                <?php
                $filter = $catalog['filter'] ?? ['facility' => 0, 'date' => '', 'slot' => 0];
                $facilities = $catalog['facilities'] ?? [];
                $timeBands = $catalog['timeBands'] ?? [];
                $hasFilter = ($filter['facility'] ?? 0) > 0 || ($filter['date'] ?? '') !== '' || ($filter['slot'] ?? 0) > 0;
                ?>
                <form method="GET" action="<?php echo URLROOT ; ?>/playerslots/facilities" class="slot-filter-bar">
                    <div>
                        <label for="facilityFilter">Facility</label>
                        <select name="facility" id="facilityFilter">
                            <option value="0">All facilities</option>
                            <?php foreach ($facilities as $facility): ?>
                                <option value="<?php echo (int) $facility->FacilityID; ?>" <?php echo ((int) ($filter['facility'] ?? 0) === (int) $facility->FacilityID) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($facility->Name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="dateFilter">Date</label>
                        <input type="date" name="date" id="dateFilter" min="<?php echo date('Y-m-d'); ?>" max="<?php echo htmlspecialchars($catalog['maxDate'] ?? date('Y-m-d', strtotime('+1 month'))); ?>" value="<?php echo htmlspecialchars((string) ($filter['date'] ?? '')); ?>">
                    </div>
                    <div>
                        <label for="slotFilter">Time Band</label>
                        <select name="slot" id="slotFilter">
                            <option value="0">Any time</option>
                            <?php foreach ($timeBands as $timeBand): ?>
                                <option value="<?php echo (int) $timeBand->SlotID; ?>" <?php echo ((int) ($filter['slot'] ?? 0) === (int) $timeBand->SlotID) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($timeBand->SlotLabel); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-inline btn-inline-small">Filter</button>
                    <?php if ($hasFilter): ?>
                        <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="btn-inline btn-inline-small btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            <?php endif; ?>

            <div class="schedule-card">
                <div class="card-header">
                    <div class="section-title">
                        <?php echo $currentTab === 'facility_booking' ? 'Available Facility Slots' : 'Available Private Slots'; ?>
                    </div>
                </div>
                <div class="card-content">
                    <?php if (empty($catalogRows)): ?>
                        <div class="slots-empty-state">
                            <i class="fas fa-calendar-times slots-empty-state-icon"></i>
                            <p class="slots-empty-state-title">No slots are currently available for this section.</p>
                        </div>
                    <?php else: ?>
                        <table class="dashboard-table slots-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Type</th>
                                    <th>Session</th>
                                    <th><?php echo $currentTab === 'facility_booking' ? 'Facility' : 'Staff'; ?></th>
                                    <th>Facility</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($catalogRows as $row): ?>
                                    <?php $modalId = 'slotModal-' . (int) $row->OccurrenceID; ?>
                                    <tr class="<?php echo !empty($row->blocked) ? 'blocked-row' : ''; ?>">
                                        <td><strong><?php echo date('D, d M Y', strtotime($row->OccurrenceDate)); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($row->SlotLabel ?? '-'); ?><br>
                                            <small class="slots-time-note">
                                                <?php echo date('g:i A', strtotime($row->StartTime)); ?> - <?php echo date('g:i A', strtotime($row->EndTime)); ?>
                                            </small>
                                        </td>
                                        <td><span class="slot-type-badge badge-<?php echo htmlspecialchars((string) ($row->SlotType ?? 'program')); ?>"><?php echo htmlspecialchars($slotTypeLabel($row->SlotType ?? 'program')); ?></span></td>
                                        <td><?php echo htmlspecialchars($row->TemplateName ?? '-'); ?></td>
                                        <td>
                                            <?php if ($currentTab === 'facility_booking'): ?>
                                                <?php echo htmlspecialchars($row->FacilityName ?? '-'); ?>
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($row->CoachName ?? $row->StaffNames ?? $staffLabel($row)); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row->FacilityName ?? '-'); ?></td>
                                        <td>
                                            <?php if ((float) ($row->PricePerSession ?? 0) > 0): ?>
                                                LKR <?php echo number_format((float) $row->PricePerSession, 2); ?>
                                            <?php else: ?>
                                                Included
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row->blocked)): ?>
                                                <span class="block-reason <?php echo htmlspecialchars((string) ($row->blockReason ?? 'unavailable')); ?>">
                                                    <?php echo htmlspecialchars($blockLabels[$row->blockReason] ?? 'Unavailable'); ?>
                                                </span>
                                            <?php else: ?>
                                                <button type="button" class="btn-inline js-open-slot-modal" data-modal-id="<?php echo htmlspecialchars($modalId); ?>">View Details</button>
                                                <div id="<?php echo htmlspecialchars($modalId); ?>" class="pay-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="<?php echo htmlspecialchars($modalId); ?>-title">
                                                    <div class="pay-modal">
                                                        <div class="pay-modal-header">
                                                            <h3 id="<?php echo htmlspecialchars($modalId); ?>-title">Booking Details</h3>
                                                            <button type="button" class="pay-close js-close-slot-modal" aria-label="Close">&times;</button>
                                                        </div>
                                                        <div class="pay-summary">
                                                            <div class="pay-summary-row">
                                                                <span>Type</span>
                                                                <strong><?php echo htmlspecialchars($slotTypeLabel($row->SlotType ?? 'program')); ?></strong>
                                                            </div>
                                                            <div class="pay-summary-row">
                                                                <span>Session</span>
                                                                <strong><?php echo htmlspecialchars($row->TemplateName ?? '-'); ?></strong>
                                                            </div>
                                                            <div class="pay-summary-row">
                                                                <span>Staff</span>
                                                                <strong><?php echo htmlspecialchars($row->CoachName ?? $row->StaffNames ?? '-'); ?></strong>
                                                            </div>
                                                            <div class="pay-summary-row">
                                                                <span>Facility</span>
                                                                <strong><?php echo htmlspecialchars($row->FacilityName ?? '-'); ?></strong>
                                                            </div>
                                                            <div class="pay-summary-row">
                                                                <span>Date</span>
                                                                <strong><?php echo htmlspecialchars(date('D, d M Y', strtotime($row->OccurrenceDate))); ?></strong>
                                                            </div>
                                                            <div class="pay-summary-row">
                                                                <span>Time</span>
                                                                <strong><?php echo htmlspecialchars($row->SlotLabel ?? '-'); ?></strong>
                                                            </div>
                                                            <div class="pay-summary-row pay-total">
                                                                <span>Price</span>
                                                                <strong>
                                                                    <?php if ((float) ($row->PricePerSession ?? 0) > 0): ?>
                                                                        LKR <?php echo number_format((float) $row->PricePerSession, 2); ?>
                                                                    <?php else: ?>
                                                                        Included
                                                                    <?php endif; ?>
                                                                </strong>
                                                            </div>
                                                        </div>

                                                        <?php if ((float) ($row->PricePerSession ?? 0) > 0): ?>
                                                            <form method="POST" action="<?php echo $currentTab === 'facility_booking' ? URLROOT . '/player/facility_payhere_checkout' : URLROOT . '/player/slot_payhere_checkout'; ?>">
                                                                <input type="hidden" name="occurrence_id" value="<?php echo (int) $row->OccurrenceID; ?>">
                                                                <?php if ($currentTab !== 'facility_booking'): ?>
                                                                    <input type="hidden" name="booking_type" value="<?php echo $currentTab === 'coach_booking' ? 'coach' : 'trainer'; ?>">
                                                                <?php endif; ?>
                                                                <button type="submit" class="btn-pay">Pay and Book</button>
                                                                <p class="pay-secure-note">Continue to payment to confirm this booking.</p>
                                                            </form>
                                                        <?php else: ?>
                                                            <form method="POST" action="<?php echo URLROOT; ?>/playerslots/book">
                                                                <input type="hidden" name="occurrence_id" value="<?php echo (int) $row->OccurrenceID; ?>">
                                                                <input type="hidden" name="redirect_tab" value="<?php echo htmlspecialchars($currentTab); ?>">
                                                                <button type="submit" class="btn-pay">Confirm Booking</button>
                                                                <p class="pay-secure-note">This slot does not require payment.</p>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php $renderPagination($catalogPagination, 'catalog_page'); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/player_slots.js?v=<?php echo time(); ?>"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
