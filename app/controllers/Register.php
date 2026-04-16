<?php

/**
 * REGISTRATION CONTROLLER
 * 
 * Purpose: Handles new user registration for the cricket academy
 * Responsibilities:
 *   1. Display registration form (GET request)
 *   2. Validate user input (POST request)
 *   3. Check for duplicate email/username
 *   4. Create new user account in database
 *   5. Redirect to login after successful registration
 */
class Register extends Controller {
    // Store reference to User model for database operations
    private $userModel;

    /**
     * CONSTRUCTOR - Runs when Register controller is instantiated
     * Loads the M_Users model which handles all database operations for users
     */
    public function __construct() {
        $this->userModel = $this->model('M_Users');
    }

    /**
     * INDEX METHOD - Main registration handler
     * This method handles both:
     *   - GET requests: Display the registration form
     *   - POST requests: Process form submission
     */
    public function index() {
        // SECURITY CHECK: Prevent already logged-in users from accessing registration
        // If user_id exists in session, they're already logged in
        if(isset($_SESSION['user_id'])) {
            redirect('dashboard');
        }

        // DETERMINE REQUEST TYPE: GET (show form) or POST (process form)
        // Load membership plans for both GET and POST
        $membershipPlans = $this->userModel->getActiveMembershipPlans();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // === FORM SUBMISSION - PROCESS REGISTRATION ===
            // Process form - sanitize input data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $this->storeRegistrationDraft($_POST);

            $data = [
                // USER INPUT VALUES - trim() removes leading/trailing whitespace
                'firstName' => trim($_POST['firstName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'dateOfBirth' => trim($_POST['dateOfBirth']),
                'address' => trim($_POST['address']),
                'email' => trim($_POST['email']),
                'contactNumber' => trim($_POST['contactNumber']),
                'school' => trim($_POST['school']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'confirmPassword' => trim($_POST['confirmPassword']),
                'membershipPlan' => trim($_POST['membershipPlan'] ?? ''),
                'form_err' => '',
                
                // ERROR MESSAGE PLACEHOLDERS - Start empty, filled if validation fails
                'firstName_err' => '',
                'lastName_err' => '',
                'dateOfBirth_err' => '',
                'address_err' => '',
                'email_err' => '',
                'contactNumber_err' => '',
                'school_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirmPassword_err' => '',
                'membershipPlan_err' => '',
                'membershipPlans' => $membershipPlans
            ];

            // === STEP 3: VALIDATION - Check all input fields ===
            if(empty($data['firstName'])) {
                $data['firstName_err'] = 'Please enter your first name';
            } elseif(strlen($data['firstName']) < 2) {
                $data['firstName_err'] = 'First name must be at least 2 characters';
            }

            if(empty($data['lastName'])) {
                $data['lastName_err'] = 'Please enter your last name';
            } elseif(strlen($data['lastName']) < 2) {
                $data['lastName_err'] = 'Last name must be at least 2 characters';
            }

            if(empty($data['dateOfBirth'])) {
                $data['dateOfBirth_err'] = 'Please enter your date of birth';
            } else {
                // AGE VALIDATION - Cricket academy has age restrictions
                // Convert date strings to DateTime objects for comparison
                $dob = new DateTime($data['dateOfBirth']);
                $today = new DateTime();
                // Calculate age in years using date difference
                $age = $today->diff($dob)->y;
                
                // Minimum age: 5 years (academy policy)
                if($age < 5) {
                    $data['dateOfBirth_err'] = 'You must be at least 5 years old to register';
                // Maximum age: 100 years (sanity check for invalid dates)
                } elseif($age > 100) {
                    $data['dateOfBirth_err'] = 'Please enter a valid date of birth';
                // Future date check: Birth date cannot be in the future
                } elseif($dob > $today) {
                    $data['dateOfBirth_err'] = 'Date of birth cannot be in the future';
                }
            }

            // VALIDATE ADDRESS - Required field
            if(empty($data['address'])) {
                $data['address_err'] = 'Please enter your address';
            } elseif(strlen($data['address']) < 10) {
                $data['address_err'] = 'Please enter a complete address';
            }

            // VALIDATE EMAIL - Required and must be unique
            if(empty($data['email'])) {
                $data['email_err'] = 'Please enter your email';
            } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Please enter a valid email address';
            } elseif($this->userModel->findUserByEmail($data['email'])) {
                // DATABASE CHECK: Query database to ensure email is not already registered
                // This prevents duplicate accounts with the same email
                $data['email_err'] = 'Email is already taken';
            }

            // VALIDATE CONTACT NUMBER - Required with format checking
            if(empty($data['contactNumber'])) {
                $data['contactNumber_err'] = 'Please enter your contact number';
            } else {
                // PHONE FORMAT VALIDATION: exactly 10 digits and must start with 0
                $phone = preg_replace('/[^0-9]/', '', $data['contactNumber']);
                if(!preg_match('/^0[0-9]{9}$/', $phone)) {
                    $data['contactNumber_err'] = 'Contact number must be exactly 10 digits and start with 0';
                } else {
                    $data['contactNumber'] = $phone;
                }
            }

            // VALIDATE SCHOOL - Required field (player's educational institution)
            if(empty($data['school'])) {
                $data['school_err'] = 'Please enter your school/institution';
            } elseif(strlen($data['school']) < 2) {
                $data['school_err'] = 'Please enter your school/institution';
            }

            // VALIDATE USERNAME - Required and must be unique
            if(empty($data['username'])) {
                $data['username_err'] = 'Please choose a username';
            } elseif(strlen($data['username']) < 4) {
                $data['username_err'] = 'Username must be at least 4 characters long';
            } elseif($this->userModel->findUserByUsername($data['username'])) {
                // DATABASE CHECK: Ensure username is unique in the system
                $data['username_err'] = 'Username is already taken';
            }

            // VALIDATE PASSWORD - Security requirements for strong passwords
            if(empty($data['password'])) {
                $data['password_err'] = 'Please enter a password';
            } elseif(strlen($data['password']) < 8) {
                // Minimum length requirement
                $data['password_err'] = 'Password must be at least 8 characters long';
            } else {
                // ENHANCED PASSWORD VALIDATION
                // Calls custom method that checks for:
                // - Uppercase letters
                // - Lowercase letters  
                // - Numbers
                // - Special characters
                $passwordValidation = $this->validatePassword($data['password']);
                if($passwordValidation !== true) {
                    $data['password_err'] = $passwordValidation;
                }
            }

            // VALIDATE CONFIRM PASSWORD - Must match original password
            if(empty($data['confirmPassword'])) {
                $data['confirmPassword_err'] = 'Please confirm your password';
            } elseif($data['password'] !== $data['confirmPassword']) {
                // Prevent typos by requiring password to be entered twice
                $data['confirmPassword_err'] = 'Passwords do not match';
            }

            // VALIDATE MEMBERSHIP PLAN
            if(empty($data['membershipPlan'])) {
                $data['membershipPlan_err'] = 'Please select a membership plan';
            } else {
                $selectedPlan = $this->userModel->getMembershipPlanById((int)$data['membershipPlan']);
                if(!$selectedPlan) {
                    $data['membershipPlan_err'] = 'Please select a valid membership plan';
                }
            }

            // === STEP 4: CHECK IF VALIDATION PASSED ===
            // If all _err fields are empty, validation passed
                if(empty($data['firstName_err']) && empty($data['lastName_err']) && empty($data['dateOfBirth_err']) && 
               empty($data['address_err']) && empty($data['email_err']) && 
               empty($data['contactNumber_err']) && empty($data['school_err']) && 
               empty($data['username_err']) && empty($data['password_err']) && 
               empty($data['confirmPassword_err']) && empty($data['membershipPlan_err'])) {
                
                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                
                // STEP 6: INSERT USER INTO DATABASE
                // Call model's register() method which executes INSERT query
                // Returns the new UserID if successful, false if failed
                if($userId = $this->userModel->register($data)) {
                    $this->clearRegistrationDraft();

                    // === REGISTRATION SUCCESSFUL ===
                    
                    // STEP 7: CREATE PLAYER PROFILE (Optional - enhances user experience)
                    // Note: Database trigger may auto-create this, but we try manually too
                    try {
                        $this->userModel->createPlayerProfile($userId, [
                            'school' => $data['school']
                        ]);
                    } catch (Exception $e) {
                        error_log("Player profile creation failed: " . $e->getMessage());
                    }

                    // STEP 7b: CREATE PLAYER SUBSCRIPTION
                    try {
                        $subscriptionId = $this->userModel->createPlayerSubscription(
                            $userId,
                            (int)$data['membershipPlan'],
                            $selectedPlan->MonthlyFee
                        );

                        if ($subscriptionId) {
                            if ($this->userModel->planUsesRecurringBilling($selectedPlan)) {
                                $paidRegistrationOrderId = (string)($_SESSION['register_payment_completed_order_id'] ?? '');
                                $paidRegistrationPlanId = (int)($_SESSION['register_payment_completed_plan_id'] ?? 0);

                                if ($paidRegistrationOrderId !== '' && $paidRegistrationPlanId === (int)$data['membershipPlan']) {
                                    $pendingPaymentCreated = $this->userModel->createCompletedSubscriptionPayment(
                                        (int)$subscriptionId,
                                        (float)$selectedPlan->MonthlyFee,
                                        $paidRegistrationOrderId,
                                        'Initial membership payment completed through PayHere during registration.'
                                    );
                                } else {
                                    $pendingPaymentCreated = $this->userModel->createPendingSubscriptionPayment(
                                        (int)$subscriptionId,
                                        (float)$selectedPlan->MonthlyFee,
                                        'Membership is pending. Pay to experience the whole academy services.'
                                    );
                                }

                                if (!$pendingPaymentCreated) {
                                    error_log('Initial pending membership payment could not be created for subscription #' . $subscriptionId);
                                }
                            }

                            $assigned = $this->userModel->autoAssignSkillCoachesAndPrograms(
                                $userId,
                                (int)$data['membershipPlan']
                            );

                            if (!$assigned) {
                                error_log('Auto assignment skipped or failed for player #' . $userId);
                            }
                        }
                    } catch (Exception $e) {
                        // Non-critical: Profile can be completed later by user
                        // Log error for debugging but don't stop registration flow
                        error_log("Subscription creation failed: " . $e->getMessage());
                    }
                    
                    // STEP 8: LOG ACTIVITY (Optional - for admin monitoring)
                    // Track when new accounts are created for security and analytics
                    try {
                        $this->userModel->logActivity(
                            $userId,                              // Who
                            'account_created',                    // What
                            'New player account registered',      // Details
                            $_SERVER['REMOTE_ADDR'] ?? null,      // IP Address
                            $_SERVER['HTTP_USER_AGENT'] ?? null   // Browser info
                        );
                    } catch (Exception $e) {
                        // Non-critical: Activity logging failure shouldn't stop registration
                        error_log("Activity logging failed: " . $e->getMessage());
                    }
                    
                    // STEP 9: REDIRECT TO LOGIN PAGE
                    // flash() stores a one-time message in session to display after redirect
                    // This implements the Post-Redirect-Get (PRG) pattern
                    unset($_SESSION['register_payment_completed_order_id']);
                    unset($_SESSION['register_payment_completed_plan_id']);
                    flash('register_success', 'Registration successful! Welcome to Elite Cricket Academy.');
                    redirect('login');
                } else {
                    $registrationError = (string)$this->userModel->getLastErrorMessage();

                    if (stripos($registrationError, 'Duplicate entry') !== false && stripos($registrationError, 'Email') !== false) {
                        $data['email_err'] = 'Email is already taken';
                    } elseif (stripos($registrationError, 'Duplicate entry') !== false && stripos($registrationError, 'Username') !== false) {
                        $data['username_err'] = 'Username is already taken';
                    } else {
                        $data['form_err'] = 'Registration could not be completed. Please check your details and try again.';
                    }

                    $this->view('v_register', $data);
                    return;
                }
            } else {
                // === VALIDATION FAILED ===
                // Reload the registration form with error messages
                // The $data array contains both user input and error messages
                // This allows user to see what went wrong without re-typing everything
                $this->view('v_register', $data);
            }
        } else {
            // === GET REQUEST - DISPLAY REGISTRATION FORM ===
            // User is visiting the page for the first time
            // Initialize empty data array to avoid undefined variable errors in view
            $data = array_merge([
                'firstName' => '',
                'lastName' => '',
                'dateOfBirth' => '',
                'address' => '',
                'email' => '',
                'contactNumber' => '',
                'school' => '',
                'username' => '',
                'password' => '',
                'confirmPassword' => '',
                'membershipPlan' => '',
                'form_err' => '',
                'firstName_err' => '',
                'lastName_err' => '',
                'dateOfBirth_err' => '',
                'address_err' => '',
                'email_err' => '',
                'contactNumber_err' => '',
                'school_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirmPassword_err' => '',
                'membershipPlan_err' => '',
                'membershipPlans' => $membershipPlans
            ], $this->getRegistrationDraft());

            $data['password'] = '';
            $data['confirmPassword'] = '';
            $data['membershipPlans'] = $membershipPlans;

            // Load view
            $this->view('v_register', $data);
        }
    }

    public function payment_portal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register');
        }

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->storeRegistrationDraft($_POST);

        $membershipPlans = $this->userModel->getActiveMembershipPlans();
        $selectedPlanId = (int)($_POST['membershipPlan'] ?? 0);
        $selectedPlan = $selectedPlanId > 0 ? $this->userModel->getMembershipPlanById($selectedPlanId) : null;

        if (!$selectedPlan) {
            $data = [
                'firstName' => trim($_POST['firstName'] ?? ''),
                'lastName' => trim($_POST['lastName'] ?? ''),
                'dateOfBirth' => trim($_POST['dateOfBirth'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'contactNumber' => trim($_POST['contactNumber'] ?? ''),
                'school' => trim($_POST['school'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => '',
                'confirmPassword' => '',
                'membershipPlan' => trim($_POST['membershipPlan'] ?? ''),
                'form_err' => '',
                'firstName_err' => '',
                'lastName_err' => '',
                'dateOfBirth_err' => '',
                'address_err' => '',
                'email_err' => '',
                'contactNumber_err' => '',
                'school_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirmPassword_err' => '',
                'membershipPlan_err' => 'Please select a membership plan before continuing to payment.',
                'membershipPlans' => $membershipPlans
            ];

            $this->view('v_register', $data);
            return;
        }

        require_once APPROOT . '/libraries/PayHere.php';

        $orderId = 'ELITE-REG-' . $selectedPlan->PlanID . '-' . time();
        $amount = number_format((float)$selectedPlan->MonthlyFee, 2, '.', '');

        $firstName = trim($_POST['firstName'] ?? 'Guest');
        $lastName = trim($_POST['lastName'] ?? 'Registration');
        $email = trim($_POST['email'] ?? 'guest@elite.local');
        $phone = preg_replace('/[^0-9]/', '', trim($_POST['contactNumber'] ?? ''));
        $address = trim($_POST['address'] ?? 'Elite Cricket Academy');

        if ($firstName === '') {
            $firstName = 'Guest';
        }

        if ($lastName === '') {
            $lastName = 'Registration';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'guest@elite.local';
        }

        if ($phone === '') {
            $phone = '0000000000';
        }

        $_SESSION['register_payment_plan_id'] = (int)$selectedPlan->PlanID;
        $_SESSION['register_payment_order_id'] = $orderId;
        $_SESSION['register_pending_payment'] = [
            'order_id' => $orderId,
            'plan_id' => (int)$selectedPlan->PlanID,
            'plan_name' => (string)$selectedPlan->PlanName,
            'amount' => $amount,
            'currency' => 'LKR',
            'email' => $email,
            'name' => trim($firstName . ' ' . $lastName),
        ];

        $data = [
            'title' => 'Redirecting to PayHere...',
            'gateway' => [
                'merchant_id' => PayHere::MERCHANT_ID,
                'gateway_url' => PayHere::GATEWAY_URL,
                'order_id' => $orderId,
                'amount' => $amount,
                'currency' => 'LKR',
                'items' => ucfirst((string)$selectedPlan->PlanName) . ' Membership Plan',
                'hash' => PayHere::buildHash($orderId, $amount, 'LKR'),
                'return_url' => URLROOT . '/register/payhere_return',
                'cancel_url' => URLROOT . '/register/payhere_cancel',
                'notify_url' => URLROOT . '/register/payhere_notify',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'city' => 'Colombo',
                'country' => 'Sri Lanka',
            ],
        ];

        $this->view('player/payhere_gateway', $data);
    }

    public function payhere_return() {
        $pendingPayment = $_SESSION['register_pending_payment'] ?? null;
        if (is_array($pendingPayment) && !empty($pendingPayment['order_id'])) {
            $orderId = (string)$pendingPayment['order_id'];
            $this->sendRegistrationPaymentSuccessEmail($pendingPayment);
            $_SESSION['register_payment_completed_order_id'] = $orderId;
            $_SESSION['register_payment_completed_plan_id'] = (int)($pendingPayment['plan_id'] ?? 0);
            unset($_SESSION['register_pending_payment']);
        }

        flash('register_payment', 'Payment completed. You can now finish creating your account.', 'alert alert-success');
        redirect('register');
    }

    public function payhere_cancel() {
        unset($_SESSION['register_pending_payment']);
        flash('register_payment', 'Payment was cancelled. You can select a plan and try again.', 'alert alert-warning');
        redirect('register');
    }

    public function payhere_notify() {
        require_once APPROOT . '/libraries/PayHere.php';

        $logFile = APPROOT . '/../payhere_notify_log.txt';
        $orderId = $_POST['order_id'] ?? 'unknown';
        $amount = $_POST['payhere_amount'] ?? '';
        $currency = $_POST['payhere_currency'] ?? '';
        $statusCode = $_POST['status_code'] ?? '';

        if (PayHere::verifyNotify($_POST)) {
            PayHere::log($logFile, "REGISTER PAYMENT SUCCESS order={$orderId} amount={$amount} {$currency}");
            http_response_code(200);
            echo 'OK';
            return;
        }

        PayHere::log($logFile, "REGISTER PAYMENT FAILED order={$orderId} status={$statusCode}");
        http_response_code(400);
        echo 'INVALID';
    }

    private function sendRegistrationPaymentSuccessEmail(array $payment): bool {
        require_once APPROOT . '/libraries/PaymentEmailService.php';

        return PaymentEmailService::sendSuccessEmail(
            null,
            (string)($payment['email'] ?? ''),
            (string)($payment['name'] ?? 'Player'),
            (string)($payment['order_id'] ?? ''),
            (string)($payment['amount'] ?? '0.00'),
            (string)($payment['currency'] ?? 'LKR'),
            'Registration Membership Payment',
            ucfirst((string)($payment['plan_name'] ?? 'Membership')) . ' membership plan paid successfully. Please complete your account registration.'
        );
    }

    private function storeRegistrationDraft(array $source): void {
        $_SESSION['register_form_draft'] = [
            'firstName' => trim((string)($source['firstName'] ?? '')),
            'lastName' => trim((string)($source['lastName'] ?? '')),
            'dateOfBirth' => trim((string)($source['dateOfBirth'] ?? '')),
            'address' => trim((string)($source['address'] ?? '')),
            'email' => trim((string)($source['email'] ?? '')),
            'contactNumber' => trim((string)($source['contactNumber'] ?? '')),
            'school' => trim((string)($source['school'] ?? '')),
            'username' => trim((string)($source['username'] ?? '')),
            'membershipPlan' => trim((string)($source['membershipPlan'] ?? '')),
        ];
    }

    private function getRegistrationDraft(): array {
        return is_array($_SESSION['register_form_draft'] ?? null)
            ? $_SESSION['register_form_draft']
            : [];
    }

    private function clearRegistrationDraft(): void {
        unset($_SESSION['register_form_draft']);
    }

    /**
     * ENHANCED PASSWORD VALIDATION METHOD
     * 
     * Purpose: Enforce strong password requirements for security
     * 
     * Password Requirements:
     *   - Minimum 8 characters
     *   - At least 1 uppercase letter (A-Z)
     *   - At least 1 lowercase letter (a-z)
     *   - At least 1 number (0-9)
     *   - At least 1 special character (!@#$%^&*)
     * 
     * Why: Strong passwords prevent:
     *   - Brute force attacks
     *   - Dictionary attacks
     *   - Password guessing
     * 
     * Returns: true if valid, error message string if invalid
     */
    private function validatePassword($password) {
        // Check minimum length (8 characters)
        if(strlen($password) < 8) {
            return 'Password must be at least 8 characters long';
        }
        
        // Check for at least one uppercase letter using regex
        // [A-Z] matches any character from A to Z
        if(!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter';
        }
        
        // Check for at least one lowercase letter
        // [a-z] matches any character from a to z
        if(!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter';
        }
        
        // Check for at least one digit
        // [0-9] matches any number from 0 to 9
        if(!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one number';
        }
        
        // Check for at least one special character
        // Matches common special characters used in passwords
        if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            return 'Password must contain at least one special character (!@#$%^&*(),.?":{}|<>)';
        }
        
        // All validation checks passed - password is strong
        return true;
    }
}
?>
