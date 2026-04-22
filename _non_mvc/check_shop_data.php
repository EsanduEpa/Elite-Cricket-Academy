<?php
// Database configuration
$host = 'localhost';
$dbname = 'cricket_academy';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== SHOP DATABASE STATUS ===\n\n";
    
    // Check PlayerProfile IDs
    $result = $db->query('SELECT PlayerID FROM playerprofile LIMIT 5');
    $playerIds = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "Player IDs: " . (count($playerIds) > 0 ? implode(', ', $playerIds) : 'NONE') . "\n\n";
    
    // Check Shop Employee IDs
    $result = $db->query('SELECT ShopEmployeeID FROM shopemployeeprofile LIMIT 1');
    $shopId = $result->fetchColumn();
    echo "Shop Employee ID: " . ($shopId ? $shopId : 'NONE') . "\n\n";
    
    // Check products
    $result = $db->query('SELECT COUNT(*) as count FROM product');
    $count = $result->fetch()['count'];
    echo "Products: $count\n";
    
    // Check product orders
    $result = $db->query('SELECT COUNT(*) as count FROM productorder');
    $count = $result->fetch()['count'];
    echo "Product Orders: $count\n";
    
    // Check product reviews
    $result = $db->query('SELECT COUNT(*) as count FROM productreview');
    $count = $result->fetch()['count'];
    echo "Product Reviews: $count\n";
    
    // Check equipment
    $result = $db->query('SELECT COUNT(*) as count FROM equipment');
    $count = $result->fetch()['count'];
    echo "Equipment: $count\n";
    
    // Check equipment rentals
    $result = $db->query('SELECT COUNT(*) as count FROM equipmentrental');
    $count = $result->fetch()['count'];
    echo "Equipment Rentals: $count\n";
    
    // Check low stock items
    $result = $db->query('SELECT COUNT(*) as count FROM product WHERE StockQuantity <= 5');
    $count = $result->fetch()['count'];
    echo "Low Stock Items: $count\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
