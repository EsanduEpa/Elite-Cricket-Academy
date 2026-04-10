<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .box {
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,.12);
            max-width: 380px;
            width: 90%;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 5px solid #e0e7ff;
            border-top-color: #1a73e8;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2 { color: #1a73e8; font-size: 1.2rem; margin-bottom: 8px; }
        p  { color: #6c757d; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="box">
        <div class="spinner"></div>
        <h2>Redirecting to PayHere…</h2>
        <p>Please do not close this page.</p>
    </div>

    <!-- Auto-submit form to PayHere -->
    <form id="payhere-form" method="POST" action="<?php echo htmlspecialchars($data['gateway']['gateway_url']); ?>">
        <?php
        $g = $data['gateway'];
        $fields = [
            'sandbox'     => '1',
            'merchant_id' => $g['merchant_id'],
            'return_url'  => $g['return_url'],
            'cancel_url'  => $g['cancel_url'],
            'notify_url'  => $g['notify_url'],
            'order_id'    => $g['order_id'],
            'items'       => $g['items'],
            'currency'    => $g['currency'],
            'amount'      => $g['amount'],
            'first_name'  => $g['first_name'],
            'last_name'   => $g['last_name'],
            'email'       => $g['email'],
            'phone'       => $g['phone'],
            'address'     => $g['address'],
            'city'        => $g['city'],
            'country'     => $g['country'],
            'hash'        => $g['hash'],
        ];
        foreach ($fields as $name => $value): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php endforeach; ?>
    </form>

    <script>
        // Auto-submit after a short delay so the spinner is visible
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('payhere-form').submit();
            }, 600);
        });
    </script>
</body>
</html>
