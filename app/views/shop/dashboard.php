<?php require_once APPROOT . '/views/inc/components/header.php'; ?>

<div class="dashboard-container">
    <h1>🛍️ Shop Dashboard</h1>
    <p>Welcome, <?php echo $data['user_name']; ?>!</p>
    
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Total Orders</h3>
            <div style="font-size: 2rem; color: #28a745;"><?php echo $data['total_orders']; ?></div>
        </div>
        
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Pending Orders</h3>
            <div style="font-size: 2rem; color: #ffc107;"><?php echo $data['pending_orders']; ?></div>
        </div>
        
        <div class="stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3>Revenue</h3>
            <div style="font-size: 2rem; color: #17a2b8;"><?php echo $data['total_revenue']; ?></div>
        </div>
    </div>
    
    <div class="top-products" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 20px 0;">
        <h3>Top Products</h3>
        <ul>
            <?php foreach($data['top_products'] as $product): ?>
                <li><?php echo $product['name']; ?> - <?php echo $product['sales']; ?> sales</li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?php echo URLROOT; ?>/login/logout" style="background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Logout
        </a>
    </div>
</div>

<style>
body {
    background: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    color: #343a40;
    margin-bottom: 10px;
}
</style>

</body>
</html>