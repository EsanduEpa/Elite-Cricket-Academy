-- 2026-04-18 DB Changes
-- Add equipment return fee payments table (tracks pending/completed PayHere payments)

CREATE TABLE IF NOT EXISTS `equipmentreturnpayment` (
  `PaymentID` INT(11) NOT NULL AUTO_INCREMENT,
  `ReturnID` INT(11) NOT NULL,
  `PlayerID` INT(11) NOT NULL,

  `PaymentDate` DATE DEFAULT NULL,
  `DueDate` DATE DEFAULT NULL,

  `Amount` DECIMAL(10,2) NOT NULL,
  `PaymentMethod` ENUM('cash','card','bank_transfer','online') NOT NULL DEFAULT 'online',
  `Status` ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',

  `Gateway` VARCHAR(32) DEFAULT NULL,
  `GatewayOrderId` VARCHAR(80) DEFAULT NULL,
  `GatewayPaymentId` VARCHAR(80) DEFAULT NULL,
  `PaymentReference` VARCHAR(120) DEFAULT NULL,

  `Notes` VARCHAR(255) DEFAULT NULL,

  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `PaidAt` DATETIME DEFAULT NULL,
  `FailedAt` DATETIME DEFAULT NULL,
  `RefundedAt` DATETIME DEFAULT NULL,

  PRIMARY KEY (`PaymentID`),
  UNIQUE KEY `uq_equipmentreturnpayment_return` (`ReturnID`),
  UNIQUE KEY `uq_equipmentreturnpayment_gatewayorder` (`GatewayOrderId`),
  KEY `idx_equipmentreturnpayment_player` (`PlayerID`),
  KEY `idx_equipmentreturnpayment_status` (`Status`),

  CONSTRAINT `fk_equipmentreturnpayment_return`
    FOREIGN KEY (`ReturnID`) REFERENCES `equipmentreturn` (`ReturnID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_equipmentreturnpayment_player`
    FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Payment records for equipment return fees (late/damage)';
