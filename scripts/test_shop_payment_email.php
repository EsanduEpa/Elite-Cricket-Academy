<?php
/**
 * Temporary local test for shop payment confirmation emails.
 *
 * Usage:
 *   php scripts/test_shop_payment_email.php
 *   php scripts/test_shop_payment_email.php 6
 *   php scripts/test_shop_payment_email.php 6 your-inbox@example.com
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require_once dirname(__DIR__) . '/app/bootloader.php';
require_once APPROOT . '/libraries/Mailer.php';
require_once APPROOT . '/models/M_Users.php';
require_once APPROOT . '/models/M_Email.php';

$playerId = isset($argv[1]) ? (int)$argv[1] : 6;
$recipientOverride = isset($argv[2]) ? trim((string)$argv[2]) : '';

if ($playerId <= 0) {
    exit("Invalid player ID.\n");
}

$userModel = new M_Users();
$emailModel = new M_Email();
$player = $userModel->getUserById($playerId);

if (!$player) {
    exit("Player not found for UserID {$playerId}.\n");
}

$recipientEmail = $recipientOverride !== '' ? $recipientOverride : (string)($player->Email ?? '');
if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid recipient email: {$recipientEmail}\n");
}

$playerName = trim((string)($player->Name ?? ($player->FirstName ?? 'Player')));
$playerName = $playerName !== '' ? $playerName : 'Player';
$orderId = 'ELITE-TEST-' . $playerId . '-' . time();
$amount = '2500.00';
$currency = 'LKR';
$paidAt = date('Y-m-d H:i:s');

$safeName = htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8');
$safeOrderId = htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8');
$safeAmount = htmlspecialchars(number_format((float)$amount, 2), ENT_QUOTES, 'UTF-8');
$safeCurrency = htmlspecialchars($currency, ENT_QUOTES, 'UTF-8');
$safePaidAt = htmlspecialchars($paidAt, ENT_QUOTES, 'UTF-8');

$subject = "Payment Confirmation - {$orderId}";
$htmlBody = '
    <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
        <h2 style="color: #0f766e; margin-bottom: 8px;">Payment Successful</h2>
        <p>Hi ' . $safeName . ',</p>
        <p>This is a local demo test for a successful shop product payment confirmation.</p>
        <table style="border-collapse: collapse; margin: 16px 0; width: 100%; max-width: 520px;">
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Order ID</td>
                <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeOrderId . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Amount</td>
                <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeCurrency . ' ' . $safeAmount . '</td>
            </tr>
            <tr>
                <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Paid At</td>
                <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safePaidAt . '</td>
            </tr>
        </table>
        <p>Thank you for shopping with Elite Cricket Academy.</p>
        <p style="margin-top: 24px;">Regards,<br>Elite Cricket Academy</p>
    </div>';

echo "Sending test shop payment email...\n";
echo "Player ID: {$playerId}\n";
echo "Recipient: {$recipientEmail}\n";
echo "Order ID: {$orderId}\n";

$sent = Mailer::send($recipientEmail, $subject, $htmlBody, $playerName);
$logged = $emailModel->logPaymentConfirmation(
    $playerId,
    $recipientEmail,
    $subject,
    $sent,
    $sent ? null : 'Temporary test script SMTP send failed. Check PHP error log for Mailer details.'
);

if ($sent && $logged) {
    echo "SUCCESS: Email sent and logged in emaillog.\n";
} elseif ($sent) {
    echo "PARTIAL SUCCESS: Email sent, but emaillog insert failed. Check whether the emaillog table exists.\n";
} else {
    echo "FAILED: Email was not sent. Check PHP/XAMPP error log for SMTP details.\n";
}

exit($sent ? 0 : 1);
