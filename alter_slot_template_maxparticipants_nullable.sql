ALTER TABLE `slot_template`
MODIFY COLUMN `MaxParticipants` INT(11) DEFAULT NULL
COMMENT 'NULL = no fixed capacity; program sessions use eligible player assignments instead';