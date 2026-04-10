<?php
/**
 * payhere_test/notify.php
 * -------------------------------------------------------
 * PayHere server-to-server payment notification handler.
 * PayHere POSTs to this URL after every payment status change.
 * This must return HTTP 200, otherwise PayHere retries.
 * -------------------------------------------------------
 */

// ── Shared PayHere config ────────────────────────────────
require_once __DIR__ . '/../app/libraries/PayHere.php';

// ── Log file ─────────────────────────────────────────────
$log_file = __DIR__ . '/notify_log.txt';

// ── Read POST params ──────────────────────────────────────
$post_merchant_id  = $_POST['merchant_id']      ?? '';
$order_id          = $_POST['order_id']          ?? '';
$payhere_amount    = $_POST['payhere_amount']    ?? '';
$payhere_currency  = $_POST['payhere_currency']  ?? '';
$status_code       = $_POST['status_code']       ?? '';
$md5sig            = $_POST['md5sig']            ?? '';

PayHere::log($log_file, "RECV merchant_id=$post_merchant_id order_id=$order_id amount=$payhere_amount currency=$payhere_currency status_code=$status_code");

if (PayHere::verifyNotify($_POST)) {
    PayHere::log($log_file, "SUCCESS order=$order_id amount=$payhere_amount $payhere_currency");
    // TODO: mark order as paid in DB here
} else {
    $sc = (int)$status_code;
    $label = match($sc) {
        0  => 'PENDING',
        -1 => 'CANCELLED',
        -2 => 'FAILED',
        -3 => 'CHARGEDBACK',
        default => "UNVERIFIED status=$status_code",
    };
    PayHere::log($log_file, "$label order=$order_id");
}

// PayHere expects a 200 response — always send it
http_response_code(200);
echo 'OK';
