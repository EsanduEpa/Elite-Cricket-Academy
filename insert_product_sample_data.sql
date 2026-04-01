-- Sample data for `product` table ONLY
-- Image paths are stored relative to `public/` (e.g., `uploads/shop_product/...`).
-- Safe to re-run: uses ON DUPLICATE KEY UPDATE on the unique `SKU` (and/or `ProductID` if provided).

INSERT INTO `product` (
  `Name`,
  `Description`,
  `Category`,
  `Brand`,
  `Price`,
  `StockQuantity`,
  `Status`,
  `SKU`,
  `Weight`,
  `Dimensions`,
  `AddedDate`,
  `UpdatedBy`,
  `ProductImage`
)
VALUES
  ('Elite Pro English Willow Bat',
   'Hand-selected English willow bat designed for balanced pickup and strong stroke play.',
   'Batting', 'Elite', 12500.00, 18, 'active', 'BAT-ELITE-001', 1.180, '85 x 11 x 7 cm', NOW(), NULL, 'uploads/shop_product/product_8_1761117247.jpg'),

  ('Performance Batting Gloves (Pair)',
   'Breathable batting gloves with reinforced palm and flexible finger protection.',
   'Batting', 'Elite', 1900.00, 40, 'active', 'GLV-ELITE-002', 0.260, '30 x 15 x 8 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Cricket Helmet with Steel Grill',
   'Lightweight protective helmet with adjustable strap and impact-absorbing inner padding.',
   'Protective', 'Elite', 4200.00, 12, 'active', 'HLMT-ELITE-003', 0.820, '35 x 25 x 25 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Leather Cricket Ball (Red) - Match',
   'Premium stitched leather ball suitable for match and club level play.',
   'Bowling', 'Elite', 650.00, 120, 'active', 'BALL-ELITE-004', 0.160, '8 x 8 x 8 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Training Cones Set (20 pcs)',
   'Set of 20 durable cones for fielding drills, agility training, and boundary marking.',
   'Training', 'Elite', 850.00, 55, 'active', 'TRN-ELITE-005', 0.900, '32 x 32 x 20 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Kit Bag (Wheel) - Large',
   'Spacious kit bag with wheels, reinforced base, and multiple compartments.',
   'Accessories', 'Elite', 5600.00, 9, 'active', 'BAG-ELITE-006', 3.800, '95 x 40 x 38 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Academy Training Jersey',
   'Moisture-wicking jersey ideal for training sessions and warm-ups.',
   'Merchandise', 'Elite', 1450.00, 30, 'active', 'MERCH-ELITE-007', 0.220, '35 x 25 x 2 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Arm Guard (Batting)',
   'Comfort-fit arm guard with padded protection for confident front-foot play.',
   'Protective', 'Elite', 1100.00, 25, 'active', 'PRT-ELITE-008', 0.180, '30 x 12 x 6 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png'),

  ('Thigh Pad Set (Pair)',
   'Padded thigh guards for batting protection. Includes both left and right pads.',
   'Protective', 'Elite', 1750.00, 22, 'active', 'PRT-ELITE-009', 0.420, '28 x 20 x 10 cm', NOW(), NULL, 'uploads/shop_product/product_9_1761119174.png')

ON DUPLICATE KEY UPDATE
  `Name` = VALUES(`Name`),
  `Description` = VALUES(`Description`),
  `Category` = VALUES(`Category`),
  `Brand` = VALUES(`Brand`),
  `Price` = VALUES(`Price`),
  `StockQuantity` = VALUES(`StockQuantity`),
  `Status` = VALUES(`Status`),
  `Weight` = VALUES(`Weight`),
  `Dimensions` = VALUES(`Dimensions`),
  `UpdatedBy` = VALUES(`UpdatedBy`),
  `ProductImage` = VALUES(`ProductImage`);
