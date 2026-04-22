-- 2026-04-17 DB Changes
-- Elite Cricket Academy
--
-- Contents:
-- 1) Add ValuePrice column to equipment (migration from PurchasePrice)
-- 2) Drop PurchasePrice column from equipment
-- 3) Create equipmentreturn table (supports return status, late fees, damage status/fee, payment status)
-- 4) Add AgreeToTerms to equipmentrental
-- 5) Optional: Set equipment stock to 2 (if needed)

-- =====================================================================
-- 1) Add ValuePrice column to equipment
--    Recommended migration path (run in order):
--      a) Add ValuePrice
--      b) Copy PurchasePrice -> ValuePrice (if PurchasePrice exists)
--      c) Drop PurchasePrice
-- =====================================================================
ALTER TABLE `equipment`
  ADD COLUMN `ValuePrice` DECIMAL(10,2) DEFAULT NULL AFTER `RentalPrice`;

-- If `PurchasePrice` still exists in your DB, migrate it into `ValuePrice` before dropping.
-- UPDATE `equipment` SET `ValuePrice` = `PurchasePrice` WHERE `ValuePrice` IS NULL;

-- =====================================================================
-- 2) Drop PurchasePrice column from equipment
-- =====================================================================
ALTER TABLE `equipment`
  DROP COLUMN `PurchasePrice`;

-- =====================================================================
-- 3) Equipment Return table (one return record per rental)
--    Damage statuses: not_damaged, slight, moderate, high
-- =====================================================================
CREATE TABLE `equipmentreturn` (
  `ReturnID` INT(11) NOT NULL AUTO_INCREMENT,
  `RentalID` INT(11) NOT NULL,

  `ReturnedAt` DATETIME NOT NULL,
  `ReturnStatus` ENUM('received','inspected','completed') NOT NULL DEFAULT 'received',

  `DamageStatus` ENUM('not_damaged','slight','moderate','high') NOT NULL DEFAULT 'not_damaged',
  `DamageFee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,

  `DaysLate` INT(11) NOT NULL DEFAULT 0,
  `LateFee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,

  `TotalReturnPay` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'DamageFee + LateFee',
  `PaymentStatus` ENUM('not_required','pending','paid','refunded') NOT NULL DEFAULT 'not_required',

  `Notes` TEXT DEFAULT NULL,

  `InspectedBy` INT(11) DEFAULT NULL COMMENT 'Shop employee who inspected the return',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`ReturnID`),
  UNIQUE KEY `uq_equipmentreturn_rental` (`RentalID`),
  KEY `idx_equipmentreturn_damage` (`DamageStatus`),
  KEY `idx_equipmentreturn_status` (`ReturnStatus`),
  KEY `idx_equipmentreturn_returnedat` (`ReturnedAt`),
  KEY `idx_equipmentreturn_payment` (`PaymentStatus`),

  CONSTRAINT `fk_equipmentreturn_rental`
    FOREIGN KEY (`RentalID`) REFERENCES `equipmentrental`(`RentalID`)
    ON DELETE CASCADE ON UPDATE CASCADE,

  CONSTRAINT `fk_equipmentreturn_inspectedby`
    FOREIGN KEY (`InspectedBy`) REFERENCES `shopemployeeprofile`(`ShopEmployeeID`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Return processing for equipment rentals (late fees, damage, inspection)';

-- If `equipmentreturn` already exists in your DB, apply these instead of re-creating the table:
ALTER TABLE `equipmentreturn`
ADD COLUMN `TotalReturnPay` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'DamageFee + LateFee',
ADD COLUMN `PaymentStatus` ENUM('not_required','pending','paid','refunded') NOT NULL DEFAULT 'not_required',
  ADD KEY `idx_equipmentreturn_payment` (`PaymentStatus`);

-- =====================================================================
-- 4) Require "Agree to terms" for player rentals
-- =====================================================================
ALTER TABLE `equipmentrental`
  ADD COLUMN `AgreeToTerms` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ProcessedBy`;

-- =====================================================================
-- 5) Optional: Stock update
--    (Use only if you intended to set all equipment stock to 2)
-- =====================================================================
-- UPDATE `equipment` SET `Stock` = 2;
CREATE TABLE IF NOT EXISTS equipmentrentalcart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    PlayerID INT NOT NULL,
    EquipmentID INT NOT NULL,
  
    AddedDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_equipmentrentalcart_item (PlayerID, EquipmentID),
    KEY idx_equipmentrentalcart_player (PlayerID),
    KEY idx_equipmentrentalcart_equipment (EquipmentID),

    CONSTRAINT fk_equipmentrentalcart_player
        FOREIGN KEY (PlayerID) REFERENCES user(UserID)
        ON DELETE CASCADE,
    CONSTRAINT fk_equipmentrentalcart_equipment
        FOREIGN KEY (EquipmentID) REFERENCES equipment(EquipmentID)
        ON DELETE CASCADE
);