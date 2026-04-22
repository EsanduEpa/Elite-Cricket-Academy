USE cricket_academy;

-- ================================================================
-- Slot Module Data Cleanup
-- File: clear_slot_module_data.sql
--
-- Purpose:
--   Remove all records from the live slot-module tables while keeping
--   the table structures intact.
--
-- Important:
--   1. The default cleanup below keeps slot_time_band intact.
--   2. It deletes slot bookings, occurrences, templates, staff/player
--      assignments, and audit logs.
--   3. A separate optional FULL WIPE section is included only if you also
--      want to remove the seeded slot_time_band rows.
-- ================================================================


-- ================================================================
-- OPTION A: DEFAULT CLEANUP
-- Clears all slot-module data except slot_time_band.
-- Run this when you want to preserve the fixed time bands.
--
-- Uses DELETE in child-to-parent order because TRUNCATE on tables
-- involved in foreign key relationships can fail even when the delete
-- order is logically correct.
-- ================================================================

START TRANSACTION;

DELETE FROM slot_audit_log;
DELETE FROM slot_booking;
DELETE FROM slot_occurrence_staff_override;
DELETE FROM slot_template_staff;
DELETE FROM slot_template_player_assignment;
DELETE FROM slot_occurrence;
DELETE FROM slot_template;

ALTER TABLE slot_audit_log AUTO_INCREMENT = 1;
ALTER TABLE slot_booking AUTO_INCREMENT = 1;
ALTER TABLE slot_occurrence_staff_override AUTO_INCREMENT = 1;
ALTER TABLE slot_template_staff AUTO_INCREMENT = 1;
ALTER TABLE slot_template_player_assignment AUTO_INCREMENT = 1;
ALTER TABLE slot_occurrence AUTO_INCREMENT = 1;
ALTER TABLE slot_template AUTO_INCREMENT = 1;

COMMIT;


-- ================================================================
-- OPTION B: OPTIONAL FULL WIPE OF ALL SLOT-MODULE DATA
--
-- Uncomment this block and run it alone only if you also want to delete
-- the seeded slot_time_band rows.
-- ================================================================

-- START TRANSACTION;
--
-- DELETE FROM slot_audit_log;
-- DELETE FROM slot_booking;
-- DELETE FROM slot_occurrence_staff_override;
-- DELETE FROM slot_template_staff;
-- DELETE FROM slot_template_player_assignment;
-- DELETE FROM slot_occurrence;
-- DELETE FROM slot_template;
-- DELETE FROM slot_time_band;
--
-- ALTER TABLE slot_audit_log AUTO_INCREMENT = 1;
-- ALTER TABLE slot_booking AUTO_INCREMENT = 1;
-- ALTER TABLE slot_occurrence_staff_override AUTO_INCREMENT = 1;
-- ALTER TABLE slot_template_staff AUTO_INCREMENT = 1;
-- ALTER TABLE slot_template_player_assignment AUTO_INCREMENT = 1;
-- ALTER TABLE slot_occurrence AUTO_INCREMENT = 1;
-- ALTER TABLE slot_template AUTO_INCREMENT = 1;
-- ALTER TABLE slot_time_band AUTO_INCREMENT = 1;
--
-- COMMIT;


-- ================================================================
-- OPTIONAL: VERIFY ROW COUNTS AFTER CLEANUP
-- ================================================================

-- SELECT 'slot_time_band' AS TableName, COUNT(*) AS RowCount FROM slot_time_band
-- UNION ALL
-- SELECT 'slot_template', COUNT(*) FROM slot_template
-- UNION ALL
-- SELECT 'slot_template_staff', COUNT(*) FROM slot_template_staff
-- UNION ALL
-- SELECT 'slot_template_player_assignment', COUNT(*) FROM slot_template_player_assignment
-- UNION ALL
-- SELECT 'slot_occurrence', COUNT(*) FROM slot_occurrence
-- UNION ALL
-- SELECT 'slot_occurrence_staff_override', COUNT(*) FROM slot_occurrence_staff_override
-- UNION ALL
-- SELECT 'slot_booking', COUNT(*) FROM slot_booking
-- UNION ALL
-- SELECT 'slot_audit_log', COUNT(*) FROM slot_audit_log;


-- ================================================================
-- OPTIONAL: REBUILD DEFAULT SLOT TIME BANDS AFTER A FULL WIPE
-- ================================================================

-- INSERT IGNORE INTO slot_time_band
--     (SlotID, SlotLabel, StartTime, EndTime, DurationMinutes)
-- VALUES
--     (1, '09:00 AM – 11:00 AM', '09:00:00', '11:00:00', 120),
--     (2, '11:00 AM – 01:00 PM', '11:00:00', '13:00:00', 120),
--     (3, '01:00 PM – 03:00 PM', '13:00:00', '15:00:00', 120),
--     (4, '03:00 PM – 05:00 PM', '15:00:00', '17:00:00', 120),
--     (5, '05:00 PM – 07:00 PM', '17:00:00', '19:00:00', 120),
--     (6, '07:00 PM – 09:00 PM', '19:00:00', '21:00:00', 120),
--     (7, '09:00 PM – 10:00 PM', '21:00:00', '22:00:00', 60);