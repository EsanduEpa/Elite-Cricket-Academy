-- ============================================================
-- Workout Plan Business Process Improvements
-- Run against: cricket_academy
-- ============================================================

-- 1. Add plan lifecycle status to workoutplan
--    active   = visible & assignable
--    draft    = trainer is still editing, not yet published
--    archived = retired plan, kept for history
ALTER TABLE `workoutplan`
    ADD COLUMN `Status` ENUM('Active', 'Completed', 'Paused')NOT NULL DEFAULT 'active' AFTER `Benefits`;

-- 2. Extend workoutplan_player with assignment lifecycle
--    AssignedBy  = which trainer created this assignment (audit trail)
--    Status      = active / completed / paused 
--    EndDate     = optional planned end date for the program

ALTER TABLE `workoutplan_player`
    ADD COLUMN `AssignedBy` INT(11) NULL AFTER `AssignedDate`,
    ADD COLUMN `Status`     ENUM('active','completed','paused') NOT NULL DEFAULT 'active' AFTER `AssignedBy`,
    ADD COLUMN `EndDate`    DATE NULL AFTER `Status`;

-- FK: AssignedBy → trainerprofile (SET NULL on delete so history is kept)
ALTER TABLE `workoutplan_player`
    ADD CONSTRAINT `fk_wpp_assigned_by`
        FOREIGN KEY (`AssignedBy`) REFERENCES `trainerprofile`(`TrainerID`)
        ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Helpful indexes
--    Speed up "all active plans assigned to player X" (player portal)
CREATE INDEX `idx_wpp_player_status`  ON `workoutplan_player` (`PlayerID`, `Status`);
--    Speed up "how many players are on plan X, and what's the status"
CREATE INDEX `idx_wpp_plan_status`    ON `workoutplan_player` (`PlanID`,   `Status`);
--    Speed up "all plans assigned by trainer Y"
CREATE INDEX `idx_wpp_assigned_by`    ON `workoutplan_player` (`AssignedBy`);
--    Speed up trainer plan library filtered by status
CREATE INDEX `idx_wp_trainer_status`  ON `workoutplan`        (`TrainerID`, `Status`);

-- 4. Verify
SELECT 'workoutplan columns:' AS info;
DESCRIBE workoutplan;
SELECT 'workoutplan_player columns:' AS info;
DESCRIBE workoutplan_player;


-- ============================================================
-- Workout Plan: NotSuitableFor column → ENUM
-- Run against: cricket_academy
-- ============================================================
-- The original ALTER TABLE was run manually but contained a
-- line-break inside 'Acute Ankle Sprain', creating the broken
-- value 'A\r\n    cute Ankle Sprain'. This migration corrects
-- the ENUM definition and keeps any existing data intact.
-- ============================================================

-- Step 1: Fix any rows that have the broken value from the
--         mis-typed original ALTER TABLE.
UPDATE `workoutplan`
SET `NotSuitableFor` = 'None (General)'
WHERE `NotSuitableFor` NOT IN (
    'None (General)',
    'Post-Surgery',
    'Active Lower Back Pain',
    'Knee Injuries',
    'Shoulder Instability',
    'Acute Ankle Sprain',
    'Heart Conditions',
    'Concussion Protocol'
)
OR `NotSuitableFor` IS NULL;

-- Step 2: Apply the corrected ENUM definition.
ALTER TABLE `workoutplan`
    MODIFY COLUMN `NotSuitableFor` ENUM(
        'None (General)',
        'Post-Surgery',
        'Active Lower Back Pain',
        'Knee Injuries',
        'Shoulder Instability',
        'Acute Ankle Sprain',
        'Heart Conditions',
        'Concussion Protocol'
    ) NOT NULL DEFAULT 'None (General)';

-- Verify
SELECT 'workoutplan.NotSuitableFor after migration:' AS info;
SHOW COLUMNS FROM `workoutplan` LIKE 'NotSuitableFor';


-- ============================================================
-- Workout Plan: Fix TrainerID foreign key reference
-- Repoint workoutplan.TrainerID from trainerprofile to user,
-- so any trainer (user row) can create plans without needing
-- a trainerprofile row first.
-- ============================================================
ALTER TABLE `workoutplan`
    DROP FOREIGN KEY `workoutplan_ibfk_1`,
    ADD CONSTRAINT `fk_workoutplan_user`
        FOREIGN KEY (`TrainerID`) REFERENCES `user` (`UserID`)
        ON DELETE CASCADE ON UPDATE CASCADE;

-- Also repoint workoutplan_player.AssignedBy from trainerprofile to user
-- so assignment history is preserved even for trainers without a profile.
ALTER TABLE `workoutplan_player`
    DROP FOREIGN KEY `fk_wpp_assigned_by`,
    ADD CONSTRAINT `fk2_wpp_assigned_by`
        FOREIGN KEY (`AssignedBy`) REFERENCES `user` (`UserID`)
        ON DELETE SET NULL ON UPDATE CASCADE;
