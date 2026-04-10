<?php
// PayHere returns the customer here after a successful payment (browser redirect)
// Do NOT use this to confirm payment — use notify.php for that
// PayHere appends: order_id, payment_id, status_code, method, amount, currency, status_message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <style>
        body { font-family: sans-serif; background:#f0f4f8; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .box { background:#fff; border-radius:14px; padding:40px; max-width:420px; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,.1); }
        .icon { font-size:56px; margin-bottom:16px; }
        h2 { color:#16a34a; margin-bottom:8px; }
        p  { color:#64748b; font-size:14px; margin-bottom:4px; }
        .detail { background:#f0fdf4; border-radius:8px; padding:14px; margin:20px 0; text-align:left; font-size:13px; color:#374151; }
        .detail b { color:#166534; }
        a { display:inline-block; margin-top:20px; padding:10px 24px; background:#3b82f6; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">✅</div>
        <h2>Payment Successful!</h2>
        <p>Thank you. Your payment has been received.</p>
        <p style="font-size:12px;color:#94a3b8;margin-top:4px;">
            Note: This is a sandbox test. No real money was charged.
        </p>

        <div class="detail">
            <?php if (!empty($_GET['order_id'])):   ?><p><b>Order ID:</b> <?php echo htmlspecialchars($_GET['order_id']); ?></p><?php endif; ?>
            <?php if (!empty($_GET['payment_id'])): ?><p><b>Payment ID:</b> <?php echo htmlspecialchars($_GET['payment_id']); ?></p><?php endif; ?>
            <?php if (!empty($_GET['method'])):     ?><p><b>Method:</b> <?php echo htmlspecialchars($_GET['method']); ?></p><?php endif; ?>
            <?php if (!empty($_GET['amount'])):     ?><p><b>Amount:</b> LKR <?php echo htmlspecialchars($_GET['amount']); ?></p><?php endif; ?>
        </div>

        <a href="http://localhost/Elite/payhere_test/checkout.php">← Test Again</a>
    </div>
</body>
</html>
