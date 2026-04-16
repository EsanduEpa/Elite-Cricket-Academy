<?php

require_once APPROOT . '/libraries/Mailer.php';
require_once APPROOT . '/models/M_Email.php';

class PaymentEmailService
{
    public static function sendSuccessEmail(
        ?int $userId,
        string $recipientEmail,
        string $recipientName,
        string $orderId,
        string $amount,
        string $currency,
        string $paymentTitle,
        string $paymentDescription
    ): bool {
        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            self::logAttempt($userId, $recipientEmail, $orderId, false, 'Skipped because recipient email is missing or invalid.');
            return false;
        }

        $emailModel = new M_Email();
        if ($emailModel->hasPaymentConfirmationBeenSent($orderId)) {
            return true;
        }

        $safeName = htmlspecialchars(trim($recipientName) !== '' ? $recipientName : 'Player', ENT_QUOTES, 'UTF-8');
        $safeOrderId = htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8');
        $safeAmount = htmlspecialchars(number_format((float)$amount, 2), ENT_QUOTES, 'UTF-8');
        $safeCurrency = htmlspecialchars($currency ?: 'LKR', ENT_QUOTES, 'UTF-8');
        $safeTitle = htmlspecialchars($paymentTitle, ENT_QUOTES, 'UTF-8');
        $safeDescription = htmlspecialchars($paymentDescription, ENT_QUOTES, 'UTF-8');
        $paidAt = date('Y-m-d H:i:s');

        $subject = "Payment Confirmation - $orderId";
        $htmlBody = '
            <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color: #0f766e; margin-bottom: 8px;">Payment Successful</h2>
                <p>Hi ' . $safeName . ',</p>
                <p>Your payment was successful. Here are your payment details:</p>
                <table style="border-collapse: collapse; margin: 16px 0; width: 100%; max-width: 560px;">
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Payment For</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeTitle . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Description</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . $safeDescription . '</td>
                    </tr>
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
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">' . htmlspecialchars($paidAt, ENT_QUOTES, 'UTF-8') . '</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 10px; font-weight: bold;">Status</td>
                        <td style="border: 1px solid #e5e7eb; padding: 10px;">Successful</td>
                    </tr>
                </table>
                <p>Thank you for your payment.</p>
                <p style="margin-top: 24px;">Regards,<br>Elite Cricket Academy</p>
            </div>';

        try {
            $sent = Mailer::send($recipientEmail, $subject, $htmlBody, $recipientName);
        } catch (Throwable $e) {
            error_log('Payment success email failed unexpectedly: ' . $e->getMessage());
            $sent = false;
        }

        self::logAttempt(
            $userId,
            $recipientEmail,
            $orderId,
            $sent,
            $sent ? null : (Mailer::getLastError() ?: 'SMTP send failed or recipient mailbox was unavailable. Check PHP error log for Mailer details.')
        );

        return $sent;
    }

    private static function logAttempt(?int $userId, string $recipientEmail, string $orderId, bool $sent, ?string $errorMessage): void
    {
        try {
            $emailModel = new M_Email();
            $emailModel->logPaymentConfirmation(
                $userId,
                $recipientEmail !== '' ? $recipientEmail : 'unknown@example.invalid',
                "Payment Confirmation - $orderId",
                $sent,
                $errorMessage
            );
        } catch (Throwable $e) {
            error_log('Payment email log failed: ' . $e->getMessage());
        }
    }
}
