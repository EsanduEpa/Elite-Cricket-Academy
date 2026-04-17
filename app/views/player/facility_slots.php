<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/player_facility_slots.css?v=<?php echo time(); ?>">

<div class="player-layout">
    <?php $playerActivePage = 'bookings'; require APPROOT . '/views/inc/components/player_sidebar.php'; ?>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <h1><i class="fas fa-building"></i> Facility Booking</h1>
                    <p>Browse facilities, book available slots at your preference.</p>
                </div>
                <div class="header-actions booking-shortcuts">
                    <a href="<?php echo URLROOT; ?>/playerslots/bookings" class="booking-shortcut-btn">
                        <i class="fas fa-list-alt"></i> My Sessions
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/coach" class="booking-shortcut-btn">
                        <i class="fas fa-user-tie"></i> Coach
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/trainer" class="booking-shortcut-btn">
                        <i class="fas fa-dumbbell"></i> Trainer
                    </a>
                    <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="booking-shortcut-btn primary">
                        <i class="fas fa-building"></i> Facilities
                    </a>
                    <a href="<?php echo URLROOT; ?>/player/calendar" class="booking-shortcut-btn">
                        <i class="fas fa-calendar-alt"></i> Calendar
                    </a>
                </div>
            </div>
        </div>

        <?php
        $filter = $data['filter'];
        $hasFilter = $filter['facility'] > 0 || $filter['date'] !== '' || $filter['slot'] > 0;
        ?>

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

        <div class="fac-page">
            <div>
                <p class="fac-section-label">
                    <i class="fas fa-map-marker-alt"></i> &nbsp;All Facilities — click to filter
                </p>
                <div class="fac-cards">
                    <?php foreach ($data['facilities'] as $fac): ?>
                    <?php
                    $isActive = (int)$filter['facility'] === (int)$fac->FacilityID;
                    $href = URLROOT . '/playerslots/facilities?' . http_build_query([
                        'facility' => $isActive ? 0 : $fac->FacilityID,
                        'date' => $filter['date'],
                        'slot' => $filter['slot'],
                    ]);
                    $dotClass = $fac->AvailabilityStatus === 'available' ? '' : 'unavailable';
                    ?>
                    <a href="<?php echo $href; ?>" class="fac-card <?php echo $isActive ? 'active' : ''; ?>">
                        <div class="fac-card-img">
                            <?php if (!empty($fac->facilityImage)): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/facilities/<?php echo htmlspecialchars($fac->facilityImage); ?>" alt="<?php echo htmlspecialchars($fac->Name); ?>">
                            <?php else: ?>
                                <i class="fas fa-building"></i>
                            <?php endif; ?>
                        </div>
                        <div class="fac-card-body">
                            <div class="fac-card-name"><?php echo htmlspecialchars($fac->Name); ?></div>
                            <div class="fac-card-meta">
                                <span class="fac-avail-dot <?php echo $dotClass; ?>"></span>
                                <?php echo htmlspecialchars($fac->Location ?? '—'); ?>
                            </div>
                            <div class="fac-card-meta">
                                <i class="fas fa-users icon-xs"></i>
                                Capacity: <?php echo (int)$fac->Capacity; ?>
                            </div>
                            <?php if ($fac->HourlyRate > 0): ?>
                            <div class="fac-card-rate">
                                <i class="fas fa-tag icon-xs"></i>
                                LKR <?php echo number_format($fac->HourlyRate, 2); ?>/hr
                            </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="GET" action="<?php echo URLROOT; ?>/playerslots/facilities" class="filter-bar">
                <div>
                    <label for="f_facility"><i class="fas fa-building"></i> Facility</label>
                    <select name="facility" id="f_facility">
                        <option value="0">All Facilities</option>
                        <?php foreach ($data['facilities'] as $fac): ?>
                            <option value="<?php echo (int)$fac->FacilityID; ?>" <?php echo (int)$filter['facility'] === (int)$fac->FacilityID ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fac->Name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="f_date"><i class="fas fa-calendar"></i> Date</label>
                    <input type="date" name="date" id="f_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($filter['date']); ?>">
                </div>
                <div>
                    <label for="f_slot"><i class="fas fa-clock"></i> Time Band</label>
                    <select name="slot" id="f_slot">
                        <option value="0">Any Time</option>
                        <?php foreach ($data['timeBands'] as $tb): ?>
                            <option value="<?php echo (int)$tb->SlotID; ?>" <?php echo (int)$filter['slot'] === (int)$tb->SlotID ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tb->SlotLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Find Slots
                </button>
                <?php if ($hasFilter): ?>
                <a href="<?php echo URLROOT; ?>/playerslots/facilities" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>

            <div class="results-card">
                <div class="results-header">
                    <div class="results-title">
                        <i class="fas fa-calendar-check results-title-icon"></i>
                        Available Slots
                    </div>
                    <div class="results-count">
                        <?php
                        $total = count($data['occurrences']);
                        $bookable = count(array_filter($data['occurrences'], fn($o) => !$o->blocked));
                        ?>
                        <?php if ($total === 0): ?>
                            No slots found
                        <?php else: ?>
                            <?php echo $total; ?> slot<?php echo $total !== 1 ? 's' : ''; ?> found
                            &mdash; <span class="results-count-bookable"><?php echo $bookable; ?> bookable</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (empty($data['occurrences'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No facility slots are scheduled for your selection.</p>
                        <?php if ($hasFilter): ?>
                            <small>Try clearing some filters to see more results.</small>
                        <?php else: ?>
                            <small>Contact the academy to request a facility slot or check back later.</small>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                <table class="slot-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-clock"></i> Time</th>
                            <th><i class="fas fa-building"></i> Facility</th>
                            <th><i class="fas fa-map-marker-alt"></i> Location</th>
                            <th><i class="fas fa-tag"></i> Type</th>
                            <th><i class="fas fa-users"></i> Group Size</th>
                            <th><i class="fas fa-coins"></i> Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data['occurrences'] as $occ): ?>
                    <?php $groupCapacity = $occ->OccMax ?: $occ->TplMax; ?>
                    <tr class="<?php echo $occ->blocked ? 'blocked-row' : ''; ?>">
                        <td><strong><?php echo date('D, d M Y', strtotime($occ->OccurrenceDate)); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($occ->SlotLabel); ?><br>
                            <small class="time-note">
                                <?php echo date('g:i A', strtotime($occ->StartTime)); ?> &ndash;
                                <?php echo date('g:i A', strtotime($occ->EndTime)); ?>
                            </small>
                        </td>
                        <td class="facility-name-cell"><?php echo htmlspecialchars($occ->FacilityName); ?></td>
                        <td class="facility-location-cell"><?php echo htmlspecialchars($occ->FacilityLocation ?? '—'); ?></td>
                        <td>
                            <span class="badge-type badge-<?php echo htmlspecialchars($occ->SlotType); ?>">
                                <?php echo ucwords(str_replace('_', ' ', $occ->SlotType)); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($groupCapacity)): ?>
                                <span class="spots-ok"><i class="fas fa-users"></i> Up to <?php echo (int)$groupCapacity; ?></span>
                            <?php else: ?>
                                <span class="muted-dash">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="price-cell">
                            <?php if ($occ->PricePerSession > 0): ?>
                                <strong>LKR <?php echo number_format($occ->PricePerSession, 2); ?></strong>
                            <?php else: ?>
                                <span class="subscription-label">Subscription</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($occ->blocked): ?>
                                <?php $r = $occ->blockReason ?? 'unavailable'; ?>
                                <?php if ($r === 'already_booked'): ?>
                                    <span class="block-tag block-already"><i class="fas fa-check-circle"></i> Booked</span>
                                <?php elseif ($r === 'active_injury'): ?>
                                    <span class="block-tag block-injury"><i class="fas fa-band-aid"></i> Medical hold</span>
                                <?php elseif ($r === 'full'): ?>
                                    <span class="block-tag block-full"><i class="fas fa-users-slash"></i> Full</span>
                                <?php elseif (in_array($r, ['no_subscription', 'plan_mismatch'], true)): ?>
                                    <span class="block-tag block-plan">
                                        <i class="fas fa-lock"></i>
                                        <?php echo $r === 'no_subscription' ? 'No subscription' : 'Plan upgrade needed'; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="block-tag block-full"><i class="fas fa-ban"></i> Unavailable</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <button
                                    type="button"
                                    class="btn-book-pay js-open-facility-details"
                                    data-occ-id="<?php echo (int)$occ->OccurrenceID; ?>"
                                    data-facility="<?php echo htmlspecialchars($occ->FacilityName, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-date="<?php echo htmlspecialchars(date('D, d M Y', strtotime($occ->OccurrenceDate)), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-time="<?php echo htmlspecialchars($occ->SlotLabel, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-slot-type="<?php echo htmlspecialchars($occ->SlotType, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-amount="<?php echo (float)$occ->PricePerSession; ?>"
                                >
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="payModal" class="pay-modal-overlay" data-pay-url="<?php echo URLROOT; ?>/player/facility_payhere_checkout" role="dialog" aria-modal="true" aria-labelledby="payModalTitle">
    <div class="pay-modal">
        <div class="pay-modal-header">
            <h3 id="payModalTitle"><i class="fas fa-info-circle"></i> Slot Details</h3>
            <button type="button" class="pay-close" id="payModalClose" aria-label="Close">&times;</button>
        </div>

        <div class="pay-summary">
            <div class="pay-summary-row">
                <span><i class="fas fa-building summary-icon"></i>Facility</span>
                <strong id="pFacility"></strong>
            </div>
            <div class="pay-summary-row">
                <span><i class="fas fa-calendar summary-icon"></i>Date</span>
                <strong id="pDate"></strong>
            </div>
            <div class="pay-summary-row">
                <span><i class="fas fa-clock summary-icon"></i>Time</span>
                <strong id="pTime"></strong>
            </div>
            <div class="pay-summary-row pay-total">
                <span>Price</span>
                <strong id="pTotal"></strong>
            </div>
        </div>

        <form method="POST" action="<?php echo URLROOT; ?>/player/facility_payhere_checkout" id="payForm">
            <input type="hidden" name="occurrence_id" id="pOccId">
            <input type="hidden" name="amount" id="pAmount">

            <button type="submit" class="btn-pay" id="payConfirmButton">
                <i class="fas fa-lock"></i> Pay &amp; Confirm Booking
            </button>
            <p class="pay-secure-note" id="payNote">
                <i class="fas fa-shield-alt"></i>
                Review the slot details, then continue to the payment portal.
            </p>
        </form>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/player_facility_slots.js?v=<?php echo time(); ?>"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>