<?php

require_once APPROOT . '/libraries/Mailer.php';
require_once APPROOT . '/models/M_Email.php';

class RentalEmailService
{
    public static function sendConfirmationEmail(
        ?int $userId,
        string $recipientEmail,
        string $recipientName,
        array $rentalDetails
    ): bool {
        $rentalIdList = implode(', ', array_map('strval', $rentalDetails['rental_ids'] ?? []));
        $subject = 'Rental Confirmation - ' . ($rentalIdList !== '' ? $rentalIdList : 'Elite Cricket Academy');

        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            self::logAttempt($userId, $recipientEmail, $subject, false, 'Skipped because recipient email is missing or invalid.');
            return false;
        }

        $safeName = htmlspecialchars(trim($recipientName) !== '' ? $recipientName : 'Player', ENT_QUOTES, 'UTF-8');
        $safeEquipment = htmlspecialchars((string)($rentalDetails['equipment_name'] ?? 'Equipment'), ENT_QUOTES, 'UTF-8');
        $safeRentalIds = htmlspecialchars($rentalIdList, ENT_QUOTES, 'UTF-8');
        $safeQuantity = htmlspecialchars((string)($rentalDetails['quantity'] ?? 1), ENT_QUOTES, 'UTF-8');
        $safeStart = htmlspecialchars((string)($rentalDetails['start_time'] ?? ''), ENT_QUOTES, 'UTF-8');
        $safeEnd = htmlspecialchars((string)($rentalDetails['end_time'] ?? ''), ENT_QUOTES, 'UTF-8');
        $safePickup = htmlspecialchars((string)($rentalDetails['pickup_label'] ?? 'Academy pickup'), ENT_QUOTES, 'UTF-8');
        $safeTotal = htmlspecialchars(number_format((float)($rentalDetails['total_cost'] ?? 0), 2), ENT_QUOTES, 'UTF-8');

        $htmlBody = '
            <div style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color: #0f766e; margin-bottom: 8px;">Equipment Rental Confirmed</h2>
                <p>Hi ' . $safeName . ',</p>
                <p>Your equipment rental has been confirmed. Here are the details:</p>
                <table style="border-collapse: collapse; margin: 16px 0; width: 100%; max-width: 560px;">
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Rental ID(s)</td><td style="border:1px solid #e5e7eb;padding:10px;">' . $safeRentalIds . '</td></tr>
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Equipment</td><td style="border:1px solid #e5e7eb;padding:10px;">' . $safeEquipment . '</td></tr>
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Quantity</td><td style="border:1px solid #e5e7eb;padding:10px;">' . $safeQuantity . '</td></tr>
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Start</td><td style="border:1px solid #e5e7eb;padding:10px;">' . $safeStart . '</td></tr>
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Return By</td><td style="border:1px solid #e5e7eb;padding:10px;">' . $safeEnd . '</td></tr>
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Pickup Method</td><td style="border:1px solid #e5e7eb;padding:10px;">' . $safePickup . '</td></tr>
                    <tr><td style="border:1px solid #e5e7eb;padding:10px;font-weight:bold;">Total Cost</td><td style="border:1px solid #e5e7eb;padding:10px;">LKR ' . $safeTotal . '</td></tr>
                </table>
                <p>Please return the equipment on time to avoid late fees.</p>
                <p style="margin-top: 24px;">Regards,<br>Elite Cricket Academy</p>
            </div>';

        try {
            $sent = Mailer::send($recipientEmail, $subject, $htmlBody, $recipientName);
        } catch (Throwable $e) {
            error_log('Rental confirmation email failed unexpectedly: ' . $e->getMessage());
            $sent = false;
        }

        self::logAttempt(
            $userId,
            $recipientEmail,
            $subject,
            $sent,
            $sent ? null : (Mailer::getLastError() ?: 'SMTP send failed or recipient mailbox was unavailable. Check PHP error log for Mailer details.')
        );

        return $sent;
    }

    private static function logAttempt(?int $userId, string $recipientEmail, string $subject, bool $sent, ?string $errorMessage): void
    {
        try {
            $emailModel = new M_Email();
            $emailModel->logPaymentConfirmation(
                $userId,
                $recipientEmail !== '' ? $recipientEmail : 'unknown@example.invalid',
                $subject,
                $sent,
                $errorMessage
            );
        } catch (Throwable $e) {
            error_log('Rental email log failed: ' . $e->getMessage());
        }
    }
}
