-- Insert sample data for shop dashboard (CORRECTED)

-- Use existing Player IDs
SET @player1 = 7;
SET @player2 = 16;
SET @player3 = 20;

-- Create a shop employee (using PlayerID 7 as shop employee too)
INSERT IGNORE INTO shopemployeeprofile (ShopEmployeeID, Department, HireDate)
VALUES (7, 'General', '2024-01-01');
SET @shop_employee = 7;

-- Get product IDs (we know 3 products exist)
SET @product1 = (SELECT ProductID FROM product LIMIT 1);
SET @product2 = (SELECT ProductID FROM product LIMIT 1 OFFSET 1);
SET @product3 = (SELECT ProductID FROM product LIMIT 1 OFFSET 2);

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

-- Insert order items
INSERT INTO productorderitem (OrderID, ProductID, Quantity, UnitPrice, SubTotal) VALUES
(@order1, @product1, 2, 2000.00, 4000.00),
(@order1, @product2, 1, 500.00, 500.00),
(@order2, @product2, 3, 800.00, 2400.00),
(@order2, @product3, 2, 200.00, 400.00),
(@order3, @product1, 3, 2000.00, 6000.00),
(@order4, @product3, 5, 300.00, 1500.00),
(@order5, @product2, 4, 800.00, 3200.00);

-- Insert product reviews (mix of pending and approved)
INSERT INTO productreview (PlayerID, ProductID, Rating, ReviewText, ReviewDate, Status, VerifiedPurchase) VALUES
(@player1, @product1, 5, 'Excellent quality cricket bat, very happy with the purchase!', DATE_SUB(NOW(), INTERVAL 3 DAY), 'pending', TRUE),
(@player2, @product2, 4, 'Good protective gear, fits well.', DATE_SUB(NOW(), INTERVAL 2 DAY), 'pending', TRUE),
(@player3, @product3, 4, 'Cricket balls are of good quality for practice.', DATE_SUB(NOW(), INTERVAL 1 DAY), 'pending', TRUE),
(@player1, @product2, 5, 'Great value for money!', DATE_SUB(NOW(), INTERVAL 4 DAY), 'approved', TRUE);

-- Get equipment IDs (we know 14 exist)
SET @equipment1 = (SELECT EquipmentID FROM equipment LIMIT 1);
SET @equipment2 = (SELECT EquipmentID FROM equipment LIMIT 1 OFFSET 1);
SET @equipment3 = (SELECT EquipmentID FROM equipment LIMIT 1 OFFSET 2);
SET @equipment4 = (SELECT EquipmentID FROM equipment LIMIT 1 OFFSET 3);
SET @equipment5 = (SELECT EquipmentID FROM equipment LIMIT 1 OFFSET 4);

-- Insert equipment rentals (mix of active and returned)
INSERT INTO equipmentrental (PlayerID, EquipmentID, RentalDate, StartTime, EndTime, Status, TotalCost, ProcessedBy) VALUES
(@player1, @equipment1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_ADD(NOW(), INTERVAL 3 HOUR), 'active', 500.00, @shop_employee),
(@player2, @equipment2, DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_ADD(NOW(), INTERVAL 4 HOUR), 'active', 300.00, @shop_employee),
(@player3, @equipment3, CURDATE(), DATE_SUB(NOW(), INTERVAL 30 MINUTE), DATE_ADD(NOW(), INTERVAL 6 HOUR), 'active', 450.00, @shop_employee),
(@player1, @equipment4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'returned', 500.00, @shop_employee),
(@player2, @equipment5, DATE_SUB(CURDATE(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 'returned', 300.00, @shop_employee);

-- Update some products to have low stock
UPDATE product SET StockQuantity = 3 WHERE ProductID = @product1;
UPDATE product SET StockQuantity = 2 WHERE ProductID = @product2;
UPDATE product SET StockQuantity = 5 WHERE ProductID = @product3;
