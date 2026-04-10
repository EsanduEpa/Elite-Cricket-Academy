<?php
// PayHere redirects here when the user clicks "Cancel" on their checkout page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Cancelled</title>
    <style>
        body { font-family: sans-serif; background:#f0f4f8; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .box { background:#fff; border-radius:14px; padding:40px; max-width:420px; text-align:center; box-shadow:0 4px 20px rgba(0,0,0,.1); }
        .icon { font-size:56px; margin-bottom:16px; }
        h2 { color:#dc2626; margin-bottom:8px; }
        p  { color:#64748b; font-size:14px; }
        a  { display:inline-block; margin-top:24px; padding:10px 24px; background:#3b82f6; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">❌</div>
        <h2>Payment Cancelled</h2>
        <p>You cancelled the payment. No charges were made.</p>
        <a href="http://localhost/Elite/payhere_test/checkout.php">← Try Again</a>
    </div>
</body>
</html>
