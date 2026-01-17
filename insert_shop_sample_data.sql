-- Insert sample data for shop dashboard

-- First, get some player IDs
SET @player1 = (SELECT UserID FROM User WHERE Role = 'Player' LIMIT 1);
SET @player2 = (SELECT UserID FROM User WHERE Role = 'Player' LIMIT 1 OFFSET 1);
SET @player3 = (SELECT UserID FROM User WHERE Role = 'Player' LIMIT 1 OFFSET 2);
SET @shop_employee = (SELECT UserID FROM User WHERE Role = 'Shop' LIMIT 1);

-- Insert sample product orders
INSERT INTO productorder (PlayerID, OrderDate, TotalAmount, PaymentMethod, Status, ProcessedBy, OrderNotes) VALUES
(@player1, DATE_SUB(NOW(), INTERVAL 5 DAY), 4500.00, 'card', 'completed', @shop_employee, 'First order - Cricket bat and gloves'),
(@player2, DATE_SUB(NOW(), INTERVAL 3 DAY), 2800.00, 'cash', 'completed', @shop_employee, 'Protective gear purchase'),
(@player1, DATE_SUB(NOW(), INTERVAL 2 DAY), 6200.00, 'online', 'processing', @shop_employee, 'Complete cricket kit'),
(@player3, DATE_SUB(NOW(), INTERVAL 1 DAY), 1500.00, 'card', 'pending', NULL, 'Cricket balls order'),
(@player2, NOW(), 3200.00, 'card', 'pending', NULL, 'Training equipment');

-- Get the order IDs we just inserted
SET @order1 = LAST_INSERT_ID();
SET @order2 = @order1 + 1;
SET @order3 = @order1 + 2;
SET @order4 = @order1 + 3;
SET @order5 = @order1 + 4;

-- Get product IDs
SET @product1 = (SELECT ProductID FROM product LIMIT 1);
SET @product2 = (SELECT ProductID FROM product LIMIT 1 OFFSET 1);
SET @product3 = (SELECT ProductID FROM product LIMIT 1 OFFSET 2);

-- Insert order items
INSERT INTO productorderitem (OrderID, ProductID, Quantity, UnitPrice, SubTotal) VALUES
(@order1, @product1, 2, 2250.00, 4500.00),
(@order2, @product2, 1, 2800.00, 2800.00),
(@order3, @product1, 1, 2250.00, 2250.00),
(@order3, @product3, 1, 3950.00, 3950.00),
(@order4, @product2, 1, 1500.00, 1500.00),
(@order5, @product1, 1, 2250.00, 2250.00),
(@order5, @product3, 1, 950.00, 950.00);

-- Insert product reviews (some pending for moderation)
INSERT INTO productreview (ProductID, PlayerID, OrderID, Rating, ReviewTitle, ReviewText, Pros, Cons, WouldRecommend, VerifiedPurchase, ReviewDate, Status) VALUES
(@product1, @player1, @order1, 5, 'Excellent Quality!', 'Great product, highly recommend for professional use.', 'Durable, Good grip, Professional quality', 'Slightly expensive', 1, 1, DATE_SUB(NOW(), INTERVAL 2 DAY), 'pending'),
(@product2, @player2, @order2, 4, 'Good Value', 'Nice product but could be better.', 'Affordable, Good design', 'Average quality', 1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), 'pending'),
(@product1, @player2, NULL, 4, 'Decent Product', 'Works well for practice sessions.', 'Lightweight, Easy to use', NULL, 1, 0, NOW(), 'pending'),
(@product3, @player3, NULL, 5, 'Best in Market', 'Outstanding quality and performance.', 'Excellent build, Great comfort', NULL, 1, 0, NOW(), 'approved');

-- Get equipment IDs
SET @equipment1 = (SELECT EquipmentID FROM equipment WHERE Category = 'Batting' LIMIT 1);
SET @equipment2 = (SELECT EquipmentID FROM equipment WHERE Category = 'Bowling' LIMIT 1);
SET @equipment3 = (SELECT EquipmentID FROM equipment WHERE Category = 'Protective' LIMIT 1);

-- Insert equipment rentals (some active)
INSERT INTO equipmentrental (EquipmentID, PlayerID, RentalDate, StartTime, EndTime, Status, TotalCost, ProcessedBy, LateFee) VALUES
(@equipment1, @player1, CURDATE(), NOW(), DATE_ADD(NOW(), INTERVAL 4 HOUR), 'active', 100.00, @shop_employee, 0.00),
(@equipment2, @player2, CURDATE(), NOW(), DATE_ADD(NOW(), INTERVAL 6 HOUR), 'active', 30.00, @shop_employee, 0.00),
(@equipment3, @player3, CURDATE(), NOW(), DATE_ADD(NOW(), INTERVAL 3 HOUR), 'active', 60.00, @shop_employee, 0.00),
(@equipment1, @player1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '10:00:00', '16:00:00', 'returned', 150.00, @shop_employee, 0.00),
(@equipment2, @player2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '14:00:00', '18:00:00', 'returned', 20.00, @shop_employee, 0.00);

-- Update some products to have low stock
UPDATE product SET StockQuantity = 3 WHERE ProductID = @product1;
UPDATE product SET StockQuantity = 2 WHERE ProductID = @product2;
UPDATE product SET StockQuantity = 5 WHERE ProductID = @product3;

SELECT 'Sample data inserted successfully!' as message;
