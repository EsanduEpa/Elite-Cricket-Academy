<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($data['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/payhere_gateway.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="payhere-redirect-box">
        <div class="payhere-spinner"></div>
        <h2 class="payhere-title">Redirecting to PayHere...</h2>
        <p class="payhere-copy">Please do not close this page.</p>
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

    <script src="<?php echo URLROOT; ?>/js/player/payhere_gateway.js?v=<?php echo time(); ?>"></script>
</body>
</html>
