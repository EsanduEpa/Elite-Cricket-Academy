<?php
// This helper is loaded for every request by app/bootloader.php.
// It starts PHP sessions and centralizes authentication, authorization, flash messages,
// and inactivity-timeout behavior.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store or display one-time messages.
// Controllers set flash('name', 'message'), and views display flash('name').
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            if(!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            if(!empty($_SESSION[$name. '_class'])) {
                unset($_SESSION[$name. '_class']);
            }

            $_SESSION[$name] = $message;
            $_SESSION[$name. '_class'] = $class;
        }
        elseif(empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name. '_class']) ? $_SESSION[$name. '_class'] : '';
            echo '<div class="'.$class.'" id="msg-flash">'.$_SESSION[$name].'</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name. '_class']);
        }
    }
}

function isLoggedIn() {
    // A valid logged-in user must have both an ID and a role in session.
    if(isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        return true;
    } else {
        return false;
    }
}

function redirect($page) {
    // Redirects always use URLROOT so links work from any controller.
    header('location: ' . URLROOT . '/' . $page);
    exit();
}

function hasRole($role) {
    if(isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'] === $role;
    }
    return false;
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'Guest';
}

function isAjaxOrJsonRequest(): bool {
    // AJAX/API requests should receive JSON errors instead of HTML redirects.
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $isJsonRequest = (isset($_SERVER['CONTENT_TYPE']) &&
                     strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
                     (isset($_SERVER['HTTP_ACCEPT']) &&
                     strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    return $isAjax || $isJsonRequest;
}

function destroyUserSession(): void {
    // Clear session data and expire the browser's session cookie.
    // This is used for logout and automatic timeout logout.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function isPublicRoute(string $controller, string $method = 'index'): bool {
    // These routes are allowed without login.
    // PayHere notify must be public because PayHere calls it server-to-server.
    $controller = strtolower($controller);
    $method = strtolower($method ?: 'index');

    $publicRoutes = [
        'home' => ['*'],
        'pages' => ['index', 'logout'],
        'login' => ['index', 'logout', 'forgot_password', 'reset_password'],
        'player' => ['payhere_notify'],
        'register' => ['*'],
    ];

    if (!isset($publicRoutes[$controller])) {
        return false;
    }

    return in_array('*', $publicRoutes[$controller], true) ||
           in_array($method, $publicRoutes[$controller], true);
}

function getRouteAllowedRoles(string $controller): array {
    // Controller-level role map.
    // Core.php calls enforceRouteAccess() before creating the controller.
    $controller = strtolower($controller);

    $roleMap = [
        'admin' => ['Admin'],
        'adminslots' => ['Admin'],
        'coach' => ['Coach'],
        'trainer' => ['Trainer'],
        'nutrition' => ['Trainer'],
        'player' => ['Player'],
        'playerslots' => ['Player'],
        'performance' => ['Player'],
        'shop' => ['Shop', 'ShopEmployee'],
        'staffslots' => ['Admin', 'Coach', 'Trainer'],
        'profile' => ['Admin', 'Coach', 'Trainer', 'Player', 'Shop', 'ShopEmployee'],
        'notifications' => ['Admin', 'Coach', 'Trainer', 'Player', 'Shop', 'ShopEmployee'],
        'remindertasks' => ['Admin', 'Coach', 'Trainer', 'Player', 'Shop', 'ShopEmployee'],
        'posts' => ['Admin'],
        'setup' => ['Admin'],
    ];

    return $roleMap[$controller] ?? ['Admin', 'Coach', 'Trainer', 'Player', 'Shop', 'ShopEmployee'];
}

function handleUnauthorizedAccess(string $message = 'You do not have permission to access this page.'): void {
    // User is logged in but their role is not allowed for this route.
    if (isAjaxOrJsonRequest()) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'success' => false,
            'message' => $message,
        ]);
        exit();
    }

    flash('access_denied', $message, 'alert alert-danger');
    redirectToDashboard();
}

function handleUnauthenticatedAccess(string $message = 'Please login first.'): void {
    // User is not logged in. Browser page requests go home; AJAX gets a 401 JSON response.
    if (isAjaxOrJsonRequest()) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'success' => false,
            'message' => $message,
        ]);
        exit();
    }

    flash('login_required', $message, 'alert alert-danger');
    redirect('');
}

function enforceSessionTimeout(): void {
    // Auto logout after configured inactivity period.
    // Each valid request refreshes last_activity, so active users stay logged in.
    if (!isLoggedIn()) {
        return;
    }

    $timeout = defined('SESSION_TIMEOUT_SECONDS') ? (int) SESSION_TIMEOUT_SECONDS : 1800;
    if ($timeout <= 0) {
        $_SESSION['last_activity'] = time();
        return;
    }

    $lastActivity = (int)($_SESSION['last_activity'] ?? time());
    if ((time() - $lastActivity) > $timeout) {
        destroyUserSession();
        handleUnauthenticatedAccess('Your session expired due to inactivity. Please login again.');
    }

    $_SESSION['last_activity'] = time();
}

function enforceRouteAccess(string $controller, string $method = 'index'): void {
    // Main route security function used by Core.php.
    // It combines public-route checks, login checks, timeout checks, and role checks.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isPublicRoute($controller, $method)) {
        if (isLoggedIn()) {
            enforceSessionTimeout();
        }
        return;
    }

    if (!isLoggedIn()) {
        handleUnauthenticatedAccess('Please login first to continue.');
    }

    enforceSessionTimeout();

    $allowedRoles = getRouteAllowedRoles($controller);
    if ($allowedRoles && !in_array($_SESSION['user_role'] ?? '', $allowedRoles, true)) {
        handleUnauthorizedAccess();
    }

    if (($_SESSION['user_role'] ?? '') === 'Player') {
        $playerId = (int)($_SESSION['user_id'] ?? 0);
        if ($playerId > 0 && function_exists('isPlayerInitialSubscriptionPaymentOutstanding') && isPlayerInitialSubscriptionPaymentOutstanding($playerId)) {
            $controllerKey = strtolower($controller);
            $methodKey = strtolower($method ?: 'index');

            $allowedWhenUnpaid = [
                'player' => [
                    'index',
                    'payments',
                    'subscription_payhere_checkout',
                    'payhere_gateway',
                    'payhere_return',
                    'payhere_cancel',
                    'pay_return_fee',
                ],
            ];

            $isAllowed = isset($allowedWhenUnpaid[$controllerKey])
                && in_array($methodKey, $allowedWhenUnpaid[$controllerKey], true);

            if (!$isAllowed) {
                $message = 'Please complete your initial membership payment to unlock the player dashboard.';

                if (isAjaxOrJsonRequest()) {
                    header('Content-Type: application/json');
                    http_response_code(402);
                    echo json_encode([
                        'status' => 'error',
                        'success' => false,
                        'message' => $message,
                    ]);
                    exit();
                }

                flash('payment_required', $message, 'alert alert-warning');
                redirect('player/payments');
            }
        }
    }
}

/**
 * Returns true if the player has an active membership plan that requires billing,
 * but has not completed ANY subscription payment yet (initial membership payment outstanding).
 */
function isPlayerInitialSubscriptionPaymentOutstanding(int $playerId): bool {
    static $cache = [];

    if ($playerId <= 0) {
        return false;
    }

    if (array_key_exists($playerId, $cache)) {
        return (bool)$cache[$playerId];
    }

    try {
        $db = new Database();

        $db->query('SELECT ps.SubscriptionID, ps.MonthlyFee AS SubscriptionFee, mp.PlanName
            FROM playersubscription ps
            JOIN membershipplan mp ON ps.PlanID = mp.PlanID
            WHERE ps.PlayerID = :player_id
              AND ps.Status = "active"
            ORDER BY ps.StartDate DESC
            LIMIT 1');
        $db->bind(':player_id', $playerId, PDO::PARAM_INT);
        $subscription = $db->single();

        if (!$subscription || empty($subscription->SubscriptionID)) {
            $cache[$playerId] = false;
            return false;
        }

        $planName = strtolower(trim((string)($subscription->PlanName ?? '')));
        $fee = (float)($subscription->SubscriptionFee ?? 0);

        if ($planName === 'facility_only' || $fee <= 0) {
            $cache[$playerId] = false;
            return false;
        }

        $db->query('SELECT 1 AS has_any
            FROM subscriptionpayment
            WHERE SubscriptionID = :subscription_id
            LIMIT 1');
        $db->bind(':subscription_id', (int)$subscription->SubscriptionID, PDO::PARAM_INT);
        $anyRow = $db->single();

        if (empty($anyRow)) {
            $cache[$playerId] = false;
            return false;
        }

        $db->query('SELECT 1 AS has_paid
            FROM subscriptionpayment
            WHERE SubscriptionID = :subscription_id
              AND Status = "completed"
            LIMIT 1');
        $db->bind(':subscription_id', (int)$subscription->SubscriptionID, PDO::PARAM_INT);
        $paidRow = $db->single();

        $outstanding = empty($paidRow);
        $cache[$playerId] = $outstanding;
        return $outstanding;
    } catch (Throwable $e) {
        // Fail open to avoid locking users out due to transient DB/schema issues.
        $cache[$playerId] = false;
        return false;
    }
}

// Controller-level guard for methods that need stricter checks inside the controller.
function requireAuth($allowedRoles = []) {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $isAjaxOrJson = isAjaxOrJsonRequest();
    
    // Check if user is logged in
    if (!isLoggedIn()) {
        if ($isAjaxOrJson) {
            // Return JSON error for AJAX requests
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => 'Please login to access this resource'
            ]);
            exit();
        }
        handleUnauthenticatedAccess('Please login first to continue.');
    }

    enforceSessionTimeout();
    
    // Check if role is allowed when a controller passes an allowed role list.
    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        if ($isAjaxOrJson) {
            // Return JSON error for AJAX requests
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'success' => false,
                'message' => 'You do not have permission to access this resource'
            ]);
            exit();
        }
        flash('access_denied', 'You do not have permission to access this page', 'alert alert-danger');
        // Redirect to their own dashboard
        redirectToDashboard();
    }
}

// Redirect user to their appropriate dashboard after login or after denied access.
function redirectToDashboard() {
    if (!isset($_SESSION['user_role'])) {
        redirect('');
        return;
    }
    
    switch($_SESSION['user_role']) {
        case 'Admin':
            redirect('admin/dashboard');
            break;
        case 'Coach':
            redirect('coach/dashboard');
            break;
        case 'Trainer':
            redirect('trainer/dashboard');
            break;
        case 'Shop':
            redirect('shop/dashboard');
            break;
        case 'ShopEmployee':
            redirect('shop/dashboard');
            break;
        case 'Player':
        default:
            redirect('player/dashboard');
            break;
    }
}
