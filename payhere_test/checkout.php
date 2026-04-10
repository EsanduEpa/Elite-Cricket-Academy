<?php
// ── Shared PayHere config (credentials live in one place) ──
require_once __DIR__ . '/../app/libraries/PayHere.php';

$order_id   = 'TEST-' . time();
$amount     = '100.00';
$currency   = 'LKR';
$items      = 'Sample Cricket Academy Payment';

$return_url = 'http://localhost/Elite/payhere_test/return.php';
$cancel_url = 'http://localhost/Elite/payhere_test/cancel.php';
$notify_url = 'http://localhost/Elite/payhere_test/notify.php';

$first_name = 'Esandu';
$last_name  = 'Epa';
$email      = 'test@example.com';
$phone      = '0771234567';
$address    = '123 Main Street';
$city       = 'Colombo';
$country    = 'Sri Lanka';

$merchant_id = PayHere::MERCHANT_ID;
$hash        = PayHere::buildHash($order_id, $amount, $currency);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayHere Sandbox — Test Checkout</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,.1);
            padding: 36px;
            max-width: 480px;
            width: 100%;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }
        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 20px;
        }
        .logo h2 { font-size: 18px; color: #1e293b; }
        .logo p  { font-size: 12px; color: #64748b; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            background: #fef3c7;
            color: #92400e;
            margin-bottom: 20px;
        }
        h3 { font-size: 16px; color: #1e293b; margin-bottom: 16px; }
        .line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #374151;
        }
        .line:last-child { border-bottom: none; }
        .line .label { color: #64748b; }
        .line .value { font-weight: 600; }
        .total {
            background: #f8fafc;
            border-radius: 8px;
            padding: 14px 16px;
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .total .label { font-size: 14px; color: #374151; font-weight: 600; }
        .total .amount { font-size: 22px; font-weight: 800; color: #1e40af; }
        .btn-pay {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .3px;
            transition: opacity .2s;
        }
        .btn-pay:hover { opacity: .9; }
        .security-note {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 16px;
        }
        .security-note i { color: #22c55e; }
        .debug-box {
            background: #1e293b;
            border-radius: 8px;
            padding: 16px;
            margin-top: 28px;
            font-family: monospace;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.6;
        }
        .debug-box .key   { color: #7dd3fc; }
        .debug-box .val   { color: #86efac; }
        .debug-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        hr { border: none; border-top: 1px solid #e2e8f0; margin: 24px 0; }
    </style>
</head>
<body>
    <div class="card">

        <div class="logo">
            <div class="logo-icon">🏏</div>
            <div>
                <h2>Elite Cricket Academy</h2>
                <p>Secure Payment Portal</p>
            </div>
        </div>

        <span class="badge">⚠ SANDBOX MODE — No real money</span>

        <h3>Order Summary</h3>

        <div class="line"><span class="label">Item</span>       <span class="value"><?php echo htmlspecialchars($items); ?></span></div>
        <div class="line"><span class="label">Order ID</span>   <span class="value"><?php echo htmlspecialchars($order_id); ?></span></div>
        <div class="line"><span class="label">Customer</span>   <span class="value"><?php echo htmlspecialchars("$first_name $last_name"); ?></span></div>
        <div class="line"><span class="label">Email</span>      <span class="value"><?php echo htmlspecialchars($email); ?></span></div>

        <div class="total">
            <span class="label">Total</span>
            <span class="amount"><?php echo $currency; ?> <?php echo number_format((float)$amount, 2); ?></span>
        </div>

        <!-- PayHere payment form — posts directly to sandbox -->
        <form method="POST" action="<?php echo PayHere::GATEWAY_URL; ?>">
            <!-- Merchant -->
            <input type="hidden" name="merchant_id"  value="<?php echo $merchant_id; ?>">
            <input type="hidden" name="return_url"   value="<?php echo $return_url; ?>">
            <input type="hidden" name="cancel_url"   value="<?php echo $cancel_url; ?>">
            <input type="hidden" name="notify_url"   value="<?php echo $notify_url; ?>">

            <!-- Order -->
            <input type="hidden" name="order_id"     value="<?php echo $order_id; ?>">
            <input type="hidden" name="items"        value="<?php echo htmlspecialchars($items); ?>">
            <input type="hidden" name="currency"     value="<?php echo $currency; ?>">
            <input type="hidden" name="amount"       value="<?php echo $amount; ?>">

            <!-- Customer -->
            <input type="hidden" name="first_name"   value="<?php echo htmlspecialchars($first_name); ?>">
            <input type="hidden" name="last_name"    value="<?php echo htmlspecialchars($last_name); ?>">
            <input type="hidden" name="email"        value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="phone"        value="<?php echo htmlspecialchars($phone); ?>">
            <input type="hidden" name="address"      value="<?php echo htmlspecialchars($address); ?>">
            <input type="hidden" name="city"         value="<?php echo htmlspecialchars($city); ?>">
            <input type="hidden" name="country"      value="<?php echo htmlspecialchars($country); ?>">

            <!-- Security hash -->
            <input type="hidden" name="hash"         value="<?php echo $hash; ?>">

            <button type="submit" class="btn-pay">
                🔒 &nbsp; Pay <?php echo $currency; ?> <?php echo number_format((float)$amount, 2); ?> with PayHere
            </button>
        </form>

        <p class="security-note">🔒 &nbsp; Secured by PayHere &nbsp;·&nbsp; SSL Encrypted</p>

        <hr>

        <!-- Debug section — remove in production -->
        <p class="debug-title">Debug Info (remove in production)</p>
        <div class="debug-box">
            <span class="key">merchant_id: </span><span class="val"><?php echo $merchant_id; ?></span><br>
            <span class="key">order_id:    </span><span class="val"><?php echo $order_id; ?></span><br>
            <span class="key">amount:      </span><span class="val"><?php echo $amount; ?></span><br>
            <span class="key">currency:    </span><span class="val"><?php echo $currency; ?></span><br>
            <span class="key">hash:        </span><span class="val"><?php echo $hash; ?></span><br>
            <span class="key">notify_url:  </span><span class="val"><?php echo $notify_url; ?></span><br>
        </div>

    </div>
</body>
</html>
