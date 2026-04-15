-- 2026-04-15 DB Changes
-- Purpose: Store per-occurence attendance for group sessions shown on the coach dashboard.
-- Checked players are marked present; unchecked players are stored as absent.
-- Coach player list actions were moved into the table header to match the admin table pattern.
-- No schema change was required for that UI update.

CREATE TABLE IF NOT EXISTS `slot_occurrence_attendance` (
  `AttendanceID` INT(11) NOT NULL AUTO_INCREMENT,
  `OccurrenceID` INT(11) NOT NULL,
  `PlayerID` INT(11) NOT NULL,
  `AttendanceStatus` ENUM('present','absent') NOT NULL DEFAULT 'absent',
  `MarkedBy` INT(11) DEFAULT NULL,
  `MarkedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AttendanceID`),
  UNIQUE KEY `uq_slot_occurrence_attendance` (`OccurrenceID`, `PlayerID`),
  KEY `idx_slot_occurrence_attendance_occurrence` (`OccurrenceID`),
  KEY `idx_slot_occurrence_attendance_player` (`PlayerID`),
  KEY `idx_slot_occurrence_attendance_marked_by` (`MarkedBy`),
  CONSTRAINT `fk_slot_occurrence_attendance_occurrence`
    FOREIGN KEY (`OccurrenceID`) REFERENCES `slot_occurrence` (`OccurrenceID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_slot_occurrence_attendance_player`
    FOREIGN KEY (`PlayerID`) REFERENCES `user` (`UserID`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_slot_occurrence_attendance_marked_by`
    FOREIGN KEY (`MarkedBy`) REFERENCES `user` (`UserID`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
