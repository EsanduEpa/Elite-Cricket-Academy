<?php
/**
 * PayHere.php — Shared PayHere configuration & helpers.
 *
 * Used by both payhere_test/ standalone files and the MVC Player controller.
 * Change credentials here once; everything picks them up automatically.
 */
class PayHere {

    // ── Sandbox credentials ───────────────────────────────
    // Switch to live credentials when going to production
    const MERCHANT_ID     = '1235084';
    const MERCHANT_SECRET = 'NDA0ODY0NzM4NjEwMzY1MzE1MDI1NzkyMDE4MTExOTc2NjI4ODk5';

    const SANDBOX_URL     = 'https://sandbox.payhere.lk/pay/checkout';
    // const LIVE_URL     = 'https://www.payhere.lk/pay/checkout'; // uncomment for production

    const GATEWAY_URL     = self::SANDBOX_URL;

    // ── Hash for checkout form ────────────────────────────
    // Formula: strtoupper( md5( merchant_id + order_id + amount + currency + strtoupper(md5(secret)) ) )
    public static function buildHash(string $orderId, string $amount, string $currency): string {
        return strtoupper(md5(
            self::MERCHANT_ID .
            $orderId .
            $amount .
            $currency .
            strtoupper(md5(self::MERCHANT_SECRET))
        ));
    }

    // ── Verify server-to-server notify signature ──────────
    // Returns true only when signature matches AND status_code == 2 (success)
    public static function verifyNotify(array $post): bool {
        $merchantId  = $post['merchant_id']     ?? '';
        $orderId     = $post['order_id']         ?? '';
        $amount      = $post['payhere_amount']   ?? '';
        $currency    = $post['payhere_currency'] ?? '';
        $statusCode  = $post['status_code']      ?? '';
        $receivedSig = $post['md5sig']           ?? '';

        if ($merchantId !== self::MERCHANT_ID || !$orderId || !$receivedSig) {
            return false;
        }

        $expectedSig = strtoupper(md5(
            self::MERCHANT_ID .
            $orderId .
            $amount .
            $currency .
            $statusCode .
            strtoupper(md5(self::MERCHANT_SECRET))
        ));

        return hash_equals($expectedSig, $receivedSig) && (int)$statusCode === 2;
    }

    // ── Log helper (optional, used by notify handlers) ────
    public static function log(string $logFile, string $message): void {
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' | ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
