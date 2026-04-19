<?php
// Registration view: renders the public player registration form.
// Register controller performs the final server-side validation and database inserts.
if (!function_exists('flash')) {
    require_once APPROOT . '/helpers/session_helper.php';
}
?>

<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/register.css">

    <!-- Main Container -->
    <div class="main-container">
        <!-- Registration Form Section -->
        <div class="form-section">
            <div class="form-container">
                <div class="register-page-header">
                    <h1 class="form-title"><i class="fas fa-user-plus form-title-icon"></i> Create Your Account</h1>
                </div>
                
                <?php flash('register_success'); ?>
                <?php flash('register_payment'); ?>

                <?php if (!empty($data['form_err'])): ?>
                    <div class="error-message show" style="display:block; margin-bottom:16px; text-align:center;">
                        <?php echo htmlspecialchars($data['form_err']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="success-message" id="successMessage">
                    Registration successful! Welcome to Elite Cricket Academy.
                </div>
                
                <form id="registrationForm" method="POST" action="<?php echo URLROOT; ?>/register">

                    <div class="form-divider"><span>Personal Info</span></div>

                    <div class="form-row form-row-three">
                        <div class="form-group">
                            <label for="firstName"><i class="fas fa-user"></i> First Name</label>
                            <div class="input-icon-wrap"><i class="fas fa-user field-icon"></i>
                            <input type="text" id="firstName" name="firstName" placeholder="Enter your first name" value="<?php echo $data['firstName']; ?>" required></div>
                            <div class="error-message <?php echo (!empty($data['firstName_err'])) ? 'show' : ''; ?>" id="firstNameError"><?php echo $data['firstName_err']; ?></div>
                        </div>
                        <div class="form-group">
                            <label for="lastName"><i class="fas fa-user"></i> Last Name</label>
                            <div class="input-icon-wrap"><i class="fas fa-user field-icon"></i>
                            <input type="text" id="lastName" name="lastName" placeholder="Enter your last name" value="<?php echo $data['lastName']; ?>" required></div>
                            <div class="error-message <?php echo (!empty($data['lastName_err'])) ? 'show' : ''; ?>" id="lastNameError"><?php echo $data['lastName_err']; ?></div>
                        </div>
                    </div>

                    <div class="form-row form-row-three">
                        <div class="form-group">
                            <label for="dateOfBirth"><i class="fas fa-calendar"></i> Date of Birth</label>
                            <div class="input-icon-wrap"><i class="fas fa-calendar field-icon"></i>
                            <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $data['dateOfBirth']; ?>" max="<?php echo date('Y-m-d'); ?>" required></div>
                            <small class="form-hint">Must be at least 5 years old</small>
                            <div class="error-message <?php echo (!empty($data['dateOfBirth_err'])) ? 'show' : ''; ?>" id="dateOfBirthError"><?php echo $data['dateOfBirth_err']; ?></div>
                        </div>

                         <div class="form-group form-group-address">
                            <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                            <div class="input-icon-wrap"><i class="fas fa-map-marker-alt field-icon"></i>
                                     <input type="text" id="address" name="address" placeholder="e.g., 123/4 Flower Road, Nugegoda" value="<?php echo $data['address']; ?>"></div>
                            <small class="form-hint">Use this format: house number / street / town</small>
                            <div class="error-message <?php echo (!empty($data['address_err'])) ? 'show' : ''; ?>" id="addressError"><?php echo $data['address_err']; ?></div>
                        </div>
                    </div>

                    <div class="form-row form-row-three">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email</label>
                            <div class="input-icon-wrap"><i class="fas fa-envelope field-icon"></i>
                            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $data['email']; ?>" required></div>
                            <div class="error-message <?php echo (!empty($data['email_err'])) ? 'show' : ''; ?>" id="emailError"><?php echo $data['email_err']; ?></div>
                        </div>
                    

            
                        <div class="form-group">
                            <label for="contactNumber"><i class="fas fa-phone"></i> Contact Number</label>
                            <div class="input-icon-wrap"><i class="fas fa-phone field-icon"></i>
                            <input type="tel" id="contactNumber" name="contactNumber" placeholder="Enter 10-digit number starting with 0" value="<?php echo $data['contactNumber']; ?>" inputmode="numeric" maxlength="10" autocomplete="tel" required></div>
                            <small class="form-hint">Example: 0771234567</small>
                            <div class="error-message <?php echo (!empty($data['contactNumber_err'])) ? 'show' : ''; ?>" id="contactNumberError"><?php echo $data['contactNumber_err']; ?></div>
                        </div>
                    </div>

                    <div class="form-row form-row-three">
                        <div class="form-group">
                            <label for="school"><i class="fas fa-school"></i> School / Institution</label>
                            <div class="input-icon-wrap"><i class="fas fa-school field-icon"></i>
                            <input type="text" id="school" name="school" placeholder="Enter your school" value="<?php echo $data['school']; ?>"></div>
                            <div class="error-message <?php echo (!empty($data['school_err'])) ? 'show' : ''; ?>" id="schoolError"><?php echo $data['school_err']; ?></div>
                        </div>
                        <div class="form-group">
                            <label for="username"><i class="fas fa-at"></i> Username</label>
                            <div class="input-icon-wrap"><i class="fas fa-at field-icon"></i>
                            <input type="text" id="username" name="username" placeholder="Choose a username" value="<?php echo $data['username']; ?>" required></div>
                            <div class="error-message <?php echo (!empty($data['username_err'])) ? 'show' : ''; ?>" id="usernameError"><?php echo $data['username_err']; ?></div>
                        </div>
                    </div>

                    <div class="form-divider"><span>Security</span></div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password</label>
                            <div class="input-icon-wrap"><i class="fas fa-lock field-icon"></i>
                            <input type="password" id="password" name="password" placeholder="Min 8 chars, upper, number, symbol" minlength="8" required></div>
                            <div class="error-message <?php echo (!empty($data['password_err'])) ? 'show' : ''; ?>" id="passwordError"><?php echo $data['password_err']; ?></div>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword"><i class="fas fa-lock"></i> Re-enter Password</label>
                            <div class="input-icon-wrap"><i class="fas fa-key field-icon"></i>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required></div>
                            <div class="error-message <?php echo (!empty($data['confirmPassword_err'])) ? 'show' : ''; ?>" id="confirmPasswordError"><?php echo $data['confirmPassword_err']; ?></div>
                        </div>
                    </div>

                    <div class="form-row form-row-address-plan">
                      

                        <div class="form-group form-group-plan">
                            <label for="membershipPlan"><i class="fas fa-medal"></i> Membership Plan</label>
                            <div class="plan-select-row">
                                <select id="membershipPlan" name="membershipPlan" class="plan-select" required>
                                    <option value="">-- Select a Plan --</option>
                                    <?php foreach($data['membershipPlans'] as $plan): ?>
                                    <?php $isFacilityOnly = strtolower((string)$plan->PlanName) === 'facility_only'; ?>
                                    <option value="<?php echo (int)$plan->PlanID; ?>"
                                        data-fee="<?php echo number_format((float)$plan->MonthlyFee, 2, '.', ''); ?>"
                                        data-plan-name="<?php echo htmlspecialchars(ucfirst($plan->PlanName)); ?>"
                                        data-recurring-billing="<?php echo $isFacilityOnly || (float)$plan->MonthlyFee <= 0 ? '0' : '1'; ?>"
                                        <?php echo ($data['membershipPlan'] == $plan->PlanID) ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(htmlspecialchars($plan->PlanName)); ?> &mdash; Rs. <?php echo number_format($plan->MonthlyFee, 2); ?>/month
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" id="seePlanDetailsBtn" class="see-plan-btn">See Plan Details</button>
                            </div>
                            <div class="error-message <?php echo (!empty($data['membershipPlan_err'])) ? 'show' : ''; ?>" id="membershipPlanError"><?php echo $data['membershipPlan_err']; ?></div>
                            <div class="payment-portal-card">
                                <div class="payment-portal-copy">
                                    <h5>Pay the selected membership fee</h5>
                                    <p id="selectedPlanFeeHint">Choose a membership plan, then continue to the payment portal with that monthly fee.</p>
                                </div>
                                <button
                                    type="submit"
                                    id="paymentPortalBtn"
                                    class="payment-portal-btn"
                                    formaction="<?php echo URLROOT; ?>/register/payment_portal"
                                    formmethod="POST"
                                    formnovalidate
                                >
                                    <i class="fas fa-credit-card"></i>
                                    <span id="paymentPortalBtnText">PayNow</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-divider"><span></span></div>

                    <div class="form-footer-actions">
                        <p class="pay-later-copy">Or create the account and pay later.</p>
                        <button type="submit" class="register-submit-btn">
                            <div class="loading" id="registerSubmitSpinner"></div>
                            <i class="fas fa-user-plus"></i>
                            <span id="registerSubmitText">Create Account</span>
                        </button>

                        <div class="login-link">
                            Already have an account? <a href="<?php echo URLROOT; ?>/login">Login</a>
                        </div>
                    </div>
                  
                </form>
            </div>
        </div>
        

    </div>

    <script src="<?php echo URLROOT; ?>/js/register.js"></script>

    <!-- Membership Plan Details Modal -->
    <div id="planDetailsModal" class="plan-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="planModalTitle">
        <div class="plan-modal">
            <div class="plan-modal-header">
                <h2 id="planModalTitle"><i class="fas fa-medal"></i> Membership Plans</h2>
                <button type="button" class="plan-modal-close" id="closePlanModal" aria-label="Close">&times;</button>
            </div>
            <div class="plan-modal-body">
                <div class="plan-cards-grid">
                    <?php foreach($data['membershipPlans'] as $plan): ?>
                    <?php $isFacilityOnly = strtolower((string)$plan->PlanName) === 'facility_only'; ?>
                    <div class="plan-card" data-plan-id="<?php echo (int)$plan->PlanID; ?>">
                        <div class="plan-card-header">
                            <div class="plan-card-badge"><?php echo ucfirst(htmlspecialchars($plan->PlanName)); ?> Plan</div>
                            <h3 class="plan-card-name"><?php echo ucfirst(htmlspecialchars($plan->PlanName)); ?></h3>
                            <?php if ($isFacilityOnly): ?>
                                <div class="plan-card-price">No monthly fee - pay per booking</div>
                            <?php else: ?>
                                <div class="plan-card-price">Rs. <?php echo number_format($plan->MonthlyFee, 2); ?><span>/month</span></div>
                            <?php endif; ?>
                        </div>
                        <div class="plan-card-body">
                            <?php if($plan->Description): ?>
                            <p class="plan-card-desc"><?php echo htmlspecialchars($plan->Description); ?></p>
                            <?php endif; ?>
                            <ul class="plan-features">
                                <li>
                                    <span class="feat-icon">&#128197;</span>
                                    <strong><?php echo (int)$plan->SessionsPerWeek; ?></strong> sessions per week
                                </li>
                                <li>
                                    <span class="feat-icon">&#127947;</span>
                                    <?php if($plan->PrivateSessionsIncluded > 0): ?>
                                        <strong><?php echo (int)$plan->PrivateSessionsIncluded; ?></strong> private sessions included
                                    <?php else: ?>
                                        No private sessions
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <?php if($plan->FacilityAccessIncluded): ?>
                                        <span class="feat-icon feat-yes">&#10003;</span> Facility access included
                                    <?php else: ?>
                                        <span class="feat-icon feat-no">&#10007;</span> No facility access
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                        <button type="button" class="plan-select-btn" data-plan-id="<?php echo (int)$plan->PlanID; ?>"
                            data-plan-label="<?php echo ucfirst(htmlspecialchars($plan->PlanName)); ?> &mdash; Rs. <?php echo number_format($plan->MonthlyFee, 2); ?>/month">
                            Select This Plan
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html> 
