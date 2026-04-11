<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">

<div class="player-layout">
    <!-- Minimal sidebar omitted intentionally; user lands here from PayHere -->
    <div class="main-content" style="display:flex;align-items:center;justify-content:center;min-height:80vh;">
        <div style="text-align:center;background:#fff;border-radius:16px;padding:48px 40px;box-shadow:0 4px 24px rgba(0,0,0,.1);max-width:440px;width:90%;">
            <div style="font-size:64px;margin-bottom:16px;">✅</div>
            <h2 style="color:#28a745;margin-bottom:8px;">Payment Successful!</h2>
            <p style="color:#666;margin-bottom:8px;">Your order has been placed successfully.</p>
            <?php if ($data['order_id']): ?>
                <p style="color:#888;font-size:.9rem;margin-bottom:24px;">
                    Order reference: <strong><?php echo $data['order_id']; ?></strong>
                </p>
            <?php endif; ?>
            <p style="color:#888;font-size:.85rem;margin-bottom:32px;">
                You will receive a confirmation once the payment is verified.
            </p>
            <a href="<?php echo URLROOT; ?>/player/shopping"
               style="display:inline-block;padding:10px 28px;background:#1a73e8;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;margin-right:10px;">
                Continue Shopping
            </a>
            <a href="<?php echo URLROOT; ?>/player"
               style="display:inline-block;padding:10px 28px;background:#6c757d;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;">
                Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
