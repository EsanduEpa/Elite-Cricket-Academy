CREATE TABLE IF NOT EXISTS `emaillog` (
  `EmailID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL COMMENT 'Can be null for system emails',
  `RecipientEmail` varchar(255) NOT NULL,
  `Subject` varchar(255) NOT NULL,
  `EmailType` enum('welcome','password_reset','notification','suspension','promotion') NOT NULL DEFAULT 'notification',
  `Status` enum('queued','sent','failed','bounced') DEFAULT 'queued',
  `SentAt` datetime DEFAULT NULL,
  `ErrorMessage` text DEFAULT NULL COMMENT 'Error details for failed emails',
  `CreatedAt` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`EmailID`),
  KEY `idx_emaillog_user` (`UserID`),
  KEY `idx_emaillog_status` (`Status`),
  KEY `idx_emaillog_type` (`EmailType`),
  KEY `idx_emaillog_created` (`CreatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Email tracking and delivery status monitoring';
