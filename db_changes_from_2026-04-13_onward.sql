-- Database changes from 2026-04-13 onward
-- Consolidated from the recent chat/session

START TRANSACTION;

-- 1) Facility template pricing: copy hourly rate from facility into matching facility-only templates
UPDATE slot_template st
JOIN facility f ON f.FacilityID = st.FacilityID
SET st.PricePerSession = f.HourlyRate
WHERE st.SlotType = 'facility_only';

-- Optional variant if you only want to fill missing/zero prices
-- UPDATE slot_template st
-- JOIN facility f ON f.FacilityID = st.FacilityID
-- SET st.PricePerSession = f.HourlyRate
-- WHERE st.SlotType = 'facility_only'
--   AND (st.PricePerSession IS NULL OR st.PricePerSession = 0);

-- 2) Coach specialization migration
-- Change coachprofile specialization enum to Batting/Bowling/Fielding only
UPDATE coachprofile
SET Specialization = 'Fielding'
WHERE Specialization = 'All-rounder';

ALTER TABLE coachprofile
MODIFY Specialization ENUM('Batting','Bowling','Fielding') DEFAULT NULL;

-- Repair any existing blank/null specialization row after enum transition
UPDATE coachprofile
SET Specialization = 'Fielding'
WHERE CoachID = 3
  AND (Specialization = '' OR Specialization IS NULL);

-- 3) Coach-only seed inserts using the protected password hash for Elite@123
INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
VALUES
('Coach', 'Niroshan', '1988-05-14', '0771112201', 'niroshan.coach@eliteca.com', 'Academy HQ', 'Elite Cricket Academy', 'Coach', 'coach201', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', NOW(), 'active', 0, 1, 'Sample batting coach'),
('Coach', 'Ruwan',    '1990-11-02', '0771112202', 'ruwan.coach@eliteca.com',    'Academy HQ', 'Elite Cricket Academy', 'Coach', 'coach202', '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2', NOW(), 'active', 0, 1, 'Sample bowling coach');

-- The coachprofile rows are auto-created by the user trigger when Role = 'Coach'.
-- These updates set the coach-specific details explicitly.
UPDATE coachprofile
SET Specialization = 'Batting',
    Experience = 8,
    Certifications = 'Level 2 Cricket Coaching',
    IsHeadCoach = 1
WHERE CoachID = (SELECT UserID FROM `user` WHERE Username = 'coach201' LIMIT 1);

UPDATE coachprofile
SET Specialization = 'Bowling',
    Experience = 6,
    Certifications = 'Fast Bowling Specialist',
    IsHeadCoach = 0
WHERE CoachID = (SELECT UserID FROM `user` WHERE Username = 'coach202' LIMIT 1);

-- 4) Coach skill + age group assignments
INSERT INTO coach_skill_age_group_assignment
(`CoachID`, `CoachingType`, `AgeGroup`, `PriorityRank`, `IsActive`, `Notes`, `AssignedBy`)
VALUES
((SELECT UserID FROM `user` WHERE Username = 'coach201' LIMIT 1), 'batting', 'Under 13', 1, 1, 'Batting foundation group', 1),
((SELECT UserID FROM `user` WHERE Username = 'coach201' LIMIT 1), 'batting', 'Under 15', 1, 1, 'Batting development group', 1),
((SELECT UserID FROM `user` WHERE Username = 'coach202' LIMIT 1), 'bowling', 'Under 19', 1, 1, 'Advanced bowling group', 1),
((SELECT UserID FROM `user` WHERE Username = 'coach202' LIMIT 1), 'fielding', 'Open',     2, 1, 'Open age fielding support', 1);

-- 5) Player seed inserts for all age groups on the general membership plan
SET @player_hash := '$2y$10$x3pmfugmUXLKqrdyvg4OY.gS1qe3/A3Dq3vm1jwbWs0u6G7unbWU2';

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Ayaan', 'Perera', '2014-02-14', '0772001001', 'ayaan.u13_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_ayaan01', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 13'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u13_ayaan01');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Mihin', 'Silva', '2014-09-20', '0772001002', 'mihin.u13_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_mihin02', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 13'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u13_mihin02');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Ravin', 'Fernando', '2015-03-10', '0772001003', 'ravin.u13_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_ravin03', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 13'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u13_ravin03');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Sadev', 'Jayasinghe', '2013-12-30', '0772001004', 'sadev.u13_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_sadev04', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 13'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u13_sadev04');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Nethuka', 'Weerasinghe', '2014-06-01', '0772001005', 'nethuka.u13_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u13_nethuka05', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 13'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u13_nethuka05');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Kavindu', 'Perera', '2011-06-01', '0772001011', 'kavindu.u15_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_kavindu01', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 15'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u15_kavindu01');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Dulneth', 'Silva', '2012-02-14', '0772001012', 'dulneth.u15_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_dulneth02', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 15'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u15_dulneth02');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Hasitha', 'Fernando', '2011-09-20', '0772001013', 'hasitha.u15_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_hasitha03', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 15'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u15_hasitha03');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Chamith', 'Jayasinghe', '2012-03-10', '0772001014', 'chamith.u15_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_chamith04', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 15'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u15_chamith04');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Imeth', 'Weerasinghe', '2011-12-30', '0772001015', 'imeth.u15_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u15_imeth05', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 15'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u15_imeth05');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Pasindu', 'Perera', '2009-06-01', '0772001021', 'pasindu.u17_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_pasindu01', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 17'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u17_pasindu01');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Nethran', 'Silva', '2010-02-14', '0772001022', 'nethran.u17_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_nethran02', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 17'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u17_nethran02');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Ashen', 'Fernando', '2009-09-20', '0772001023', 'ashen.u17_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_ashen03', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 17'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u17_ashen03');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Tharindu', 'Jayasinghe', '2010-03-10', '0772001024', 'tharindu.u17_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_tharindu04', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 17'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u17_tharindu04');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Dineth', 'Weerasinghe', '2011-01-05', '0772001025', 'dineth.u17_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u17_dineth05', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 17'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u17_dineth05');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Hirun', 'Perera', '2007-06-01', '0772001031', 'hirun.u19_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_hirun01', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 19'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u19_hirun01');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Kethmi', 'Silva', '2008-02-14', '0772001032', 'kethmi.u19_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_kethmi02', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 19'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u19_kethmi02');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Malith', 'Fernando', '2007-09-20', '0772001033', 'malith.u19_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_malith03', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 19'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u19_malith03');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Sanjana', 'Jayasinghe', '2008-03-10', '0772001034', 'sanjana.u19_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_sanjana04', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 19'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u19_sanjana04');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Ruvin', 'Weerasinghe', '2009-01-05', '0772001035', 'ruvin.u19_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u19_ruvin05', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 19'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u19_ruvin05');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Sahan', 'Perera', '2005-06-01', '0772001041', 'sahan.u21_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_sahan01', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 21'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u21_sahan01');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Pasan', 'Silva', '2006-02-14', '0772001042', 'pasan.u21_02@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_pasan02', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 21'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u21_pasan02');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Nisal', 'Fernando', '2005-09-20', '0772001043', 'nisal.u21_03@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_nisal03', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 21'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u21_nisal03');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Dulaj', 'Jayasinghe', '2006-03-10', '0772001044', 'dulaj.u21_04@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_dulaj04', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 21'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u21_dulaj04');

INSERT INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT 'Prabath', 'Weerasinghe', '2007-01-05', '0772001045', 'prabath.u21_05@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Player', 'u21_prabath05', @player_hash, NOW(), 'active', 0, 1, 'Seed player - Under 21'
WHERE NOT EXISTS (SELECT 1 FROM `user` WHERE Username = 'u21_prabath05');

INSERT INTO playersubscription
(PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
SELECT
  u.UserID,
  mp.PlanID,
  '2026-04-14',
  NULL,
  'active',
  4500.00,
  14,
  1
FROM `user` u
JOIN membershipplan mp
  ON mp.PlanName = 'general'
WHERE u.Username IN (
  'u13_ayaan01','u13_mihin02','u13_ravin03','u13_sadev04','u13_nethuka05',
  'u15_kavindu01','u15_dulneth02','u15_hasitha03','u15_chamith04','u15_imeth05',
  'u17_pasindu01','u17_nethran02','u17_ashen03','u17_tharindu04','u17_dineth05',
  'u19_hirun01','u19_kethmi02','u19_malith03','u19_sanjana04','u19_ruvin05',
  'u21_sahan01','u21_pasan02','u21_nisal03','u21_dulaj04','u21_prabath05'
)
AND NOT EXISTS (
  SELECT 1
  FROM playersubscription ps
  WHERE ps.PlayerID = u.UserID
    AND ps.PlanID = mp.PlanID
    AND ps.Status IN ('active','suspended')
);

-- 6) Player skill-to-coach assignments for the seeded players
INSERT INTO player_skill_coach_assignment
(PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, AssignedAt, Notes)
SELECT p.UserID, 'batting', c.UserID, 'Under 13', 'admin_manual', 1, NOW(), 'Seed assignment - Under 13 batting group'
FROM `user` p
JOIN `user` c ON c.Username = 'coach201'
WHERE p.Username IN ('u13_ayaan01','u13_mihin02','u13_ravin03','u13_sadev04','u13_nethuka05')
  AND NOT EXISTS (
      SELECT 1 FROM player_skill_coach_assignment a
      WHERE a.PlayerID = p.UserID
        AND a.CoachingType = 'batting'
  );

INSERT INTO player_skill_coach_assignment
(PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, AssignedAt, Notes)
SELECT p.UserID, 'batting', c.UserID, 'Under 15', 'admin_manual', 1, NOW(), 'Seed assignment - Under 15 batting group'
FROM `user` p
JOIN `user` c ON c.Username = 'coach201'
WHERE p.Username IN ('u15_kavindu01','u15_dulneth02','u15_hasitha03','u15_chamith04','u15_imeth05')
  AND NOT EXISTS (
      SELECT 1 FROM player_skill_coach_assignment a
      WHERE a.PlayerID = p.UserID
        AND a.CoachingType = 'batting'
  );

INSERT INTO player_skill_coach_assignment
(PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, AssignedAt, Notes)
SELECT p.UserID, 'bowling', c.UserID, 'Under 17', 'admin_manual', 1, NOW(), 'Seed assignment - Under 17 bowling group'
FROM `user` p
JOIN `user` c ON c.Username = 'coach202'
WHERE p.Username IN ('u17_pasindu01','u17_nethran02','u17_ashen03','u17_tharindu04','u17_dineth05')
  AND NOT EXISTS (
      SELECT 1 FROM player_skill_coach_assignment a
      WHERE a.PlayerID = p.UserID
        AND a.CoachingType = 'bowling'
  );

INSERT INTO player_skill_coach_assignment
(PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, AssignedAt, Notes)
SELECT p.UserID, 'bowling', c.UserID, 'Under 19', 'admin_manual', 1, NOW(), 'Seed assignment - Under 19 bowling group'
FROM `user` p
JOIN `user` c ON c.Username = 'coach202'
WHERE p.Username IN ('u19_hirun01','u19_kethmi02','u19_malith03','u19_sanjana04','u19_ruvin05')
  AND NOT EXISTS (
      SELECT 1 FROM player_skill_coach_assignment a
      WHERE a.PlayerID = p.UserID
        AND a.CoachingType = 'bowling'
  );

INSERT INTO player_skill_coach_assignment
(PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, AssignedAt, Notes)
SELECT p.UserID, 'fielding', c.UserID, 'Under 21', 'admin_manual', 1, NOW(), 'Seed assignment - Under 21 fielding group'
FROM `user` p
JOIN `user` c ON c.Username = 'coach202'
WHERE p.Username IN ('u21_sahan01','u21_pasan02','u21_nisal03','u21_dulaj04','u21_prabath05')
  AND NOT EXISTS (
      SELECT 1 FROM player_skill_coach_assignment a
      WHERE a.PlayerID = p.UserID
        AND a.CoachingType = 'fielding'
  );

-- 6b) Additional general-membership players to complete 10 per age group, including Open
DROP TEMPORARY TABLE IF EXISTS tmp_general_player_seed;
CREATE TEMPORARY TABLE tmp_general_player_seed (
  Username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL PRIMARY KEY,
  FirstName VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  LastName VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  DateOfBirth DATE NOT NULL,
  PhoneNumber VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  Email VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  Address VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  School VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  AgeGroup VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  CoachUsername VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  CoachingType VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  Notes VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
);

INSERT INTO tmp_general_player_seed
(Username, FirstName, LastName, DateOfBirth, PhoneNumber, Email, Address, School, AgeGroup, CoachUsername, CoachingType, Notes)
VALUES
('u13_06', 'Aarav',   'Jayasuriya',   '2015-04-18', '0772001101', 'aarav.u13_06@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 13', 'coach201', 'batting', 'Seed player - Under 13'),
('u13_07', 'Rehan',   'Perera',       '2014-11-22', '0772001102', 'rehan.u13_07@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 13', 'coach201', 'batting', 'Seed player - Under 13'),
('u13_08', 'Ishan',   'Silva',        '2015-02-09', '0772001103', 'ishan.u13_08@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 13', 'coach201', 'batting', 'Seed player - Under 13'),
('u13_09', 'Navin',   'Fernando',     '2014-07-15', '0772001104', 'navin.u13_09@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 13', 'coach201', 'batting', 'Seed player - Under 13'),
('u13_10', 'Senuth',  'Jayasinghe',   '2015-01-28', '0772001105', 'senuth.u13_10@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 13', 'coach201', 'batting', 'Seed player - Under 13'),
('u15_06', 'Yuvan',   'Perera',       '2012-04-20', '0772001111', 'yuvan.u15_06@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 15', 'coach201', 'batting', 'Seed player - Under 15'),
('u15_07', 'Lakshan', 'Silva',        '2011-11-12', '0772001112', 'lakshan.u15_07@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Under 15', 'coach201', 'batting', 'Seed player - Under 15'),
('u15_08', 'Dasun',   'Fernando',     '2012-08-03', '0772001113', 'dasun.u15_08@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 15', 'coach201', 'batting', 'Seed player - Under 15'),
('u15_09', 'Omith',   'Jayasinghe',   '2011-05-19', '0772001114', 'omith.u15_09@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 15', 'coach201', 'batting', 'Seed player - Under 15'),
('u15_10', 'Thivin',  'Weerasinghe',  '2012-01-30', '0772001115', 'thivin.u15_10@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 15', 'coach201', 'batting', 'Seed player - Under 15'),
('u17_06', 'Madusha', 'Perera',       '2010-04-17', '0772001121', 'madusha.u17_06@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Under 17', 'coach202', 'bowling', 'Seed player - Under 17'),
('u17_07', 'Asela',   'Silva',        '2009-11-09', '0772001122', 'asela.u17_07@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 17', 'coach202', 'bowling', 'Seed player - Under 17'),
('u17_08', 'Ravisha', 'Fernando',     '2010-07-24', '0772001123', 'ravisha.u17_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Under 17', 'coach202', 'bowling', 'Seed player - Under 17'),
('u17_09', 'Kusal',   'Jayasinghe',   '2009-05-14', '0772001124', 'kusal.u17_09@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 17', 'coach202', 'bowling', 'Seed player - Under 17'),
('u17_10', 'Dinura',  'Weerasinghe',  '2010-02-28', '0772001125', 'dinura.u17_10@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 17', 'coach202', 'bowling', 'Seed player - Under 17'),
('u19_06', 'Chathura','Perera',       '2008-04-21', '0772001131', 'chathura.u19_06@eliteca.com','Colombo', 'Elite Cricket Academy', 'Under 19', 'coach202', 'bowling', 'Seed player - Under 19'),
('u19_07', 'Shalitha','Silva',        '2007-10-11', '0772001132', 'shalitha.u19_07@eliteca.com','Colombo', 'Elite Cricket Academy', 'Under 19', 'coach202', 'bowling', 'Seed player - Under 19'),
('u19_08', 'Praveen', 'Fernando',     '2008-06-30', '0772001133', 'praveen.u19_08@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Under 19', 'coach202', 'bowling', 'Seed player - Under 19'),
('u19_09', 'Sachin',  'Jayasinghe',   '2007-03-17', '0772001134', 'sachin.u19_09@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 19', 'coach202', 'bowling', 'Seed player - Under 19'),
('u19_10', 'Hiranya', 'Weerasinghe',  '2008-01-25', '0772001135', 'hiranya.u19_10@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Under 19', 'coach202', 'bowling', 'Seed player - Under 19'),
('u21_06', 'Naveen',  'Perera',       '2006-04-22', '0772001141', 'naveen.u21_06@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 21', 'coach202', 'fielding', 'Seed player - Under 21'),
('u21_07', 'Isuru',   'Silva',        '2005-12-09', '0772001142', 'isuru.u21_07@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 21', 'coach202', 'fielding', 'Seed player - Under 21'),
('u21_08', 'Malsha',  'Fernando',     '2006-09-13', '0772001143', 'malsha.u21_08@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 21', 'coach202', 'fielding', 'Seed player - Under 21'),
('u21_09', 'Heshan',  'Jayasinghe',   '2005-05-26', '0772001144', 'heshan.u21_09@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Under 21', 'coach202', 'fielding', 'Seed player - Under 21'),
('u21_10', 'Vihan',   'Weerasinghe',  '2006-02-14', '0772001145', 'vihan.u21_10@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Under 21', 'coach202', 'fielding', 'Seed player - Under 21'),
('uopen_01', 'Duleesha', 'Perera',     '2004-04-20', '0772001151', 'duleesha.open_01@eliteca.com', 'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_02', 'Sanka',    'Silva',      '2003-10-11', '0772001152', 'sanka.open_02@eliteca.com',    'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_03', 'Chanuka',  'Fernando',   '2002-06-30', '0772001153', 'chanuka.open_03@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_04', 'Isuru',    'Jayasinghe', '2001-03-17', '0772001154', 'isuru.open_04@eliteca.com',    'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_05', 'Akila',    'Weerasinghe','2000-01-25', '0772001155', 'akila.open_05@eliteca.com',    'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_06', 'Raveen',   'Perera',     '2004-11-04', '0772001156', 'raveen.open_06@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_07', 'Tharush',  'Silva',      '2003-08-19', '0772001157', 'tharush.open_07@eliteca.com',  'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_08', 'Kasun',    'Fernando',   '2002-12-08', '0772001158', 'kasun.open_08@eliteca.com',    'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_09', 'Dinesh',   'Jayasinghe', '2001-05-02', '0772001159', 'dinesh.open_09@eliteca.com',   'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open'),
('uopen_10', 'Pasan',    'Weerasinghe','2000-09-16', '0772001160', 'pasan.open_10@eliteca.com',    'Colombo', 'Elite Cricket Academy', 'Open', 'coach202', 'fielding', 'Seed player - Open');

INSERT IGNORE INTO `user`
(`FirstName`, `LastName`, `DateOfBirth`, `PhoneNumber`, `Email`, `Address`, `School`, `Role`, `Username`, `PasswordHash`, `DateJoined`, `Status`, `RequiresPasswordChange`, `CreatedBy`, `Notes`)
SELECT
  s.FirstName, s.LastName, s.DateOfBirth, s.PhoneNumber, s.Email, s.Address, s.School,
  'Player', s.Username, @player_hash, NOW(), 'active', 0, 1, s.Notes
FROM tmp_general_player_seed s;

UPDATE playerprofile pp
JOIN `user` u ON u.UserID = pp.PlayerID
JOIN tmp_general_player_seed s ON s.Username = u.Username
SET pp.SchoolInstitution = s.School,
    pp.SubscriptionType = 'basic';

INSERT INTO playersubscription
(PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee, PaymentDay, AutoRenewal)
SELECT
  u.UserID,
  mp.PlanID,
  CURDATE(),
  NULL,
  'active',
  mp.MonthlyFee,
  DAY(CURDATE()),
  1
FROM tmp_general_player_seed s
JOIN `user` u ON u.Username = s.Username
JOIN membershipplan mp ON mp.PlanName = 'general'
WHERE NOT EXISTS (
  SELECT 1
  FROM playersubscription ps
  WHERE ps.PlayerID = u.UserID
    AND ps.PlanID = mp.PlanID
    AND ps.Status IN ('active','suspended')
);

INSERT INTO subscriptionpayment
(SubscriptionID, PaymentDate, Amount, PaymentMethod, Status, PaymentReference, Gateway, DueDate, ProcessedBy, Notes, PaidAt)
SELECT
  ps.SubscriptionID,
  CURDATE(),
  ps.MonthlyFee,
  'online',
  'completed',
  CONCAT('APR2026-', u.Username),
  'manual',
  CURDATE(),
  (SELECT sep.ShopEmployeeID
   FROM shopemployeeprofile sep
   JOIN `user` su ON su.UserID = sep.ShopEmployeeID
   WHERE su.Status = 'active'
   ORDER BY sep.ShopEmployeeID ASC
   LIMIT 1),
  'Seed payment - April 2026 general membership fee',
  NOW()
FROM `user` u
JOIN playersubscription ps ON ps.PlayerID = u.UserID
WHERE u.Username REGEXP '^(u13_|u15_|u17_|u19_|u21_|uopen_)'
  AND ps.Status = 'active'
  AND NOT EXISTS (
    SELECT 1
    FROM subscriptionpayment sp
    WHERE sp.SubscriptionID = ps.SubscriptionID
      AND sp.PaymentDate = CURDATE()
  );

INSERT INTO player_skill_coach_assignment
(PlayerID, CoachingType, CoachID, AgeGroup, AssignmentSource, AssignedBy, AssignedAt, Notes)
SELECT
  u.UserID,
  s.CoachingType,
  c.UserID,
  s.AgeGroup,
  'admin_manual',
  1,
  NOW(),
  CONCAT('Seed assignment - ', s.AgeGroup, ' ', s.CoachingType, ' group')
FROM tmp_general_player_seed s
JOIN `user` u ON u.Username = s.Username
JOIN `user` c ON c.Username = s.CoachUsername
WHERE NOT EXISTS (
  SELECT 1
  FROM player_skill_coach_assignment a
  WHERE a.PlayerID = u.UserID
    AND a.CoachingType = s.CoachingType
);

DROP TEMPORARY TABLE tmp_general_player_seed;

  -- 6) Private 1:1 template seeds across all active coaches and mapped facilities
  INSERT IGNORE INTO slot_template
  (
    TemplateName,
    temp_code,
    SlotType,
    StaffType,
    SlotID,
    DayOfWeek,
    FacilityID,
    AgeGroup,
    Category,
    Description,
    MaxParticipants,
    PricePerSession,
    RequiredPlanFeature,
    IsActive,
    CreatedBy
  )
  SELECT
    CONCAT(TRIM(CONCAT_WS(' ', u.FirstName, u.LastName)), ' - ', d.day_name, ' 1:1 ', f.facility_name) AS TemplateName,
    CONCAT(
      'PVT-',
      CASE c.Specialization
        WHEN 'Batting' THEN 'BAT'
        WHEN 'Bowling' THEN 'BOWL'
        WHEN 'Fielding' THEN 'FLD'
        ELSE 'GEN'
      END,
      '-',
      d.day_code,
      '-S5-F',
      f.FacilityID,
      '-C',
      c.CoachID
    ) AS temp_code,
    'private' AS SlotType,
    'coach' AS StaffType,
    5 AS SlotID,
    d.day_no AS DayOfWeek,
    f.FacilityID,
    'Open' AS AgeGroup,
    c.Specialization AS Category,
    CONCAT('1-on-1 ', c.Specialization, ' coaching with ', TRIM(CONCAT_WS(' ', u.FirstName, u.LastName))) AS Description,
    1 AS MaxParticipants,
    COALESCE(fac.HourlyRate, 0) AS PricePerSession,
    'private_sessions' AS RequiredPlanFeature,
    1 AS IsActive,
    1 AS CreatedBy
  FROM coachprofile c
  JOIN `user` u
    ON u.UserID = c.CoachID
     AND u.Role = 'Coach'
     AND u.Status = 'active'
  JOIN (
    SELECT 1 AS day_no, 'MON' AS day_code, 'Monday' AS day_name
    UNION ALL SELECT 2, 'TUE', 'Tuesday'
    UNION ALL SELECT 3, 'WED', 'Wednesday'
    UNION ALL SELECT 4, 'THU', 'Thursday'
    UNION ALL SELECT 5, 'FRI', 'Friday'
    UNION ALL SELECT 6, 'SAT', 'Saturday'
    UNION ALL SELECT 7, 'SUN', 'Sunday'
  ) d
  JOIN (
    SELECT 1 AS FacilityID, 'Batting' AS Specialization, 'Practice Net 1' AS facility_name
    UNION ALL SELECT 2, 'Batting', 'Practice Net 2'
    UNION ALL SELECT 3, 'Bowling', 'Bowling Machine'
  ) f
    ON f.Specialization = c.Specialization
  JOIN facility fac
    ON fac.FacilityID = f.FacilityID;

-- 7) Assign coaches to private 1:1 templates by coach name, and move Open age groups to assigned age groups
INSERT INTO slot_template_staff
(TemplateID, UserID, StaffType, StaffRole, AssignedBy, AssignedAt)
SELECT
  st.TemplateID,
  c.UserID,
  'coach',
  'primary',
  1,
  NOW()
FROM slot_template st
JOIN `user` c
  ON c.Role = 'Coach'
 AND c.Status = 'active'
 AND st.SlotType = 'private'
 AND LEFT(st.TemplateName, LOCATE(' - ', st.TemplateName) - 1) = TRIM(CONCAT_WS(' ', c.FirstName, c.LastName))
WHERE NOT EXISTS (
  SELECT 1
  FROM slot_template_staff s
  WHERE s.TemplateID = st.TemplateID
    AND s.UserID = c.UserID
    AND s.StaffType = 'coach'
);

UPDATE slot_template st
JOIN `user` c
  ON c.Role = 'Coach'
 AND c.Status = 'active'
 AND st.SlotType = 'private'
 AND LEFT(st.TemplateName, LOCATE(' - ', st.TemplateName) - 1) = TRIM(CONCAT_WS(' ', c.FirstName, c.LastName))
SET st.AgeGroup = (
  SELECT psa.AgeGroup
  FROM coach_skill_age_group_assignment psa
  WHERE psa.CoachID = c.UserID
    AND psa.IsActive = 1
    AND psa.AgeGroup <> 'Open'
  ORDER BY psa.PriorityRank ASC, psa.AgeGroup ASC
  LIMIT 1
)
WHERE st.AgeGroup = 'Open';

-- 8) Remove fielding 1:1 sessions and their generated occurrences
DELETE so
FROM slot_occurrence so
JOIN slot_template st ON st.TemplateID = so.TemplateID
WHERE st.SlotType = 'private'
  AND st.Category = 'Fielding';

DELETE st
FROM slot_template st
WHERE st.SlotType = 'private'
  AND st.Category = 'Fielding';

-- 9) Generate weekly occurrences for all active templates from this week's matching day through the last matching day in December
-- Year-end cutoff is inlined below to avoid variable assignment syntax issues.

INSERT IGNORE INTO slot_occurrence
(TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, Notes, GeneratedBy, CreatedAt)
SELECT
  st.TemplateID,
  st.SlotID,
  DATE_ADD(
    DATE_ADD(
      DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY),
      INTERVAL (st.DayOfWeek - 1) DAY
    ),
    INTERVAL (w.week_no * 7) DAY
  ) AS OccurrenceDate,
  st.FacilityID,
  'scheduled',
  CASE
    WHEN st.SlotType = 'program' THEN COALESCE((
      SELECT COUNT(*)
      FROM `user` pu
      JOIN playerprofile pp ON pp.PlayerID = pu.UserID
      JOIN playersubscription ps ON ps.PlayerID = pp.PlayerID
      JOIN membershipplan mp ON mp.PlanID = ps.PlanID
      WHERE pu.Role = 'Player'
        AND pu.Status = 'active'
        AND ps.Status = 'active'
        AND mp.PlanName = 'general'
        AND CASE
          WHEN TIMESTAMPDIFF(YEAR, pu.DateOfBirth, CURDATE()) < 11 THEN 'Under 11'
          WHEN TIMESTAMPDIFF(YEAR, pu.DateOfBirth, CURDATE()) BETWEEN 11 AND 12 THEN 'Under 13'
          WHEN TIMESTAMPDIFF(YEAR, pu.DateOfBirth, CURDATE()) BETWEEN 13 AND 14 THEN 'Under 15'
          WHEN TIMESTAMPDIFF(YEAR, pu.DateOfBirth, CURDATE()) BETWEEN 15 AND 16 THEN 'Under 17'
          WHEN TIMESTAMPDIFF(YEAR, pu.DateOfBirth, CURDATE()) BETWEEN 17 AND 18 THEN 'Under 19'
          WHEN TIMESTAMPDIFF(YEAR, pu.DateOfBirth, CURDATE()) BETWEEN 19 AND 20 THEN 'Under 21'
          ELSE 'Open'
        END = st.AgeGroup
    ), 0)
    ELSE st.MaxParticipants
  END,
  CONCAT('Generated from template: ', st.TemplateName),
  1,
  NOW()
FROM slot_template st
JOIN (
  SELECT 0 AS week_no UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL
  SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
  SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL
  SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
  SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL
  SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
  SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL
  SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
  SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL
  SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
  SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52
) w
WHERE st.IsActive = 1
  AND st.DayOfWeek BETWEEN 1 AND 7
  AND st.SlotType IN ('program', 'private', 'facility_only')
  AND DATE_ADD(
    DATE_ADD(
      DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY),
      INTERVAL (st.DayOfWeek - 1) DAY
    ),
    INTERVAL (w.week_no * 7) DAY
  ) <= STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-12-31'), '%Y-%m-%d');

-- 10) Private session requests from staff to admin approval
CREATE TABLE IF NOT EXISTS slot_private_session_request (
  RequestID INT(11) NOT NULL AUTO_INCREMENT,
  RequesterUserID INT(11) NOT NULL,
  StaffType ENUM('coach','trainer') NOT NULL,
  SlotID TINYINT(4) NOT NULL,
  RequestedDate DATE NOT NULL,
  FacilityID INT(11) DEFAULT NULL,
  MaxParticipants INT(11) DEFAULT 10,
  Notes TEXT DEFAULT NULL,
  Status ENUM('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  ReviewNotes TEXT DEFAULT NULL,
  ReviewedBy INT(11) DEFAULT NULL,
  ReviewedAt DATETIME DEFAULT NULL,
  ApprovedOccurrenceID INT(11) DEFAULT NULL,
  CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (RequestID),
  KEY idx_private_session_request_status (Status),
  KEY idx_private_session_request_date (RequestedDate),
  KEY idx_private_session_request_requester (RequesterUserID),
  KEY idx_private_session_request_facility (FacilityID),
  KEY idx_private_session_request_slot (SlotID),
  KEY idx_private_session_request_reviewed_by (ReviewedBy),
  KEY idx_private_session_request_approved_occurrence (ApprovedOccurrenceID),
  CONSTRAINT fk_private_session_request_requester FOREIGN KEY (RequesterUserID) REFERENCES `user` (UserID) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_private_session_request_slot FOREIGN KEY (SlotID) REFERENCES slot_time_band (SlotID) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_private_session_request_facility FOREIGN KEY (FacilityID) REFERENCES facility (FacilityID) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_private_session_request_reviewed_by FOREIGN KEY (ReviewedBy) REFERENCES `user` (UserID) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_private_session_request_occurrence FOREIGN KEY (ApprovedOccurrenceID) REFERENCES slot_occurrence (OccurrenceID) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

-- Notes:
-- - `user.UserID` is the parent key for coach profiles.
-- - `tr_create_role_profile` creates `coachprofile` automatically for `Role = 'Coach'`.
-- - `coachprofile.Specialization` is now limited to Batting, Bowling, and Fielding.

START TRANSACTION;

DROP TEMPORARY TABLE IF EXISTS tmp_group_session_seed;
CREATE TEMPORARY TABLE tmp_group_session_seed (
  AgeGroup VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  Skill VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  TemplateName VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  TempCode VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  DayOfWeek TINYINT(1) NOT NULL,
  SlotID TINYINT(4) NOT NULL,
  FacilityID INT(11) NOT NULL,
  FacilityName VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
);

INSERT INTO tmp_group_session_seed
(AgeGroup, Skill, TemplateName, TempCode, DayOfWeek, SlotID, FacilityID, FacilityName)
VALUES

('Under 11', 'batting',  'Under 11 Batting Group - Practice Net 1',  'U11-BAT-PN1', 1, 4, 1, 'Practice Net 1'),
('Under 11', 'bowling',  'Under 11 Bowling Group - Bowling Machine', 'U11-BOWL-BM', 4, 4, 1, 'Practice Net 1'),
('Under 11', 'fielding', 'Under 11 Fielding Group - Main Ground',    'U11-FLD-MG',  5, 3, 4, 'Main Ground'),

('Under 13', 'batting',  'Under 13 Batting Group - Practice Net 1',   'U13-BAT-PN1', 2, 3, 1, 'Practice Net 1'),
('Under 13', 'bowling',  'Under 13 Bowling Group - Bowling Machine',  'U13-BOWL-PN1', 4, 3, 1, 'Practice Net 1'),
('Under 13', 'fielding', 'Under 13 Fielding Group - Main Ground',     'U13-FLD-MG',  5, 4, 4, 'Main Ground'),

('Under 15', 'batting',  'Under 15 Batting Group - Practice Net 1',   'U15-BAT-PN2', 2, 4, 1, 'Practice Net 1'),
('Under 15', 'bowling',  'Under 15 Bowling Group - Bowling Machine',  'U15-BOWL-BM', 4, 3, 3, 'Bowling Machine'),
('Under 15', 'fielding', 'Under 15 Fielding Group - Main Ground',     'U15-FLD-MG',  2, 3, 4, 'Main Ground'),

('Under 17', 'batting',  'Under 17 Batting Group - Practice Net 1',   'U17-BAT-PN2', 5, 3, 1, 'Practice Net 1'),
('Under 17', 'bowling',  'Under 17 Bowling Group - Bowling Machine',  'U17-BOWL-BM', 1, 4, 3, 'Bowling Machine'),
('Under 17', 'fielding', 'Under 17 Fielding Group - Main Ground',     'U17-FLD-MG',  3, 3, 4, 'Main Ground'),

('Under 19', 'batting',  'Under 19 Batting Group - Practice Net 1',   'U19-BAT-PN2', 5, 4, 1, 'Practice Net 1'),
('Under 19', 'bowling',  'Under 19 Bowling Group - Bowling Machine',  'U19-BOWL-BM', 1, 4, 3, 'Bowling Machine'),
('Under 19', 'fielding', 'Under 19 Fielding Group - Main Ground',     'U19-FLD-MG',  2, 4, 4, 'Main Ground'),

('Under 21', 'batting',  'Under 21 Batting Group - Practice Net 1',   'U21-BAT-PN1', 6, 1, 1, 'Practice Net 1'),
('Under 21', 'bowling',  'Under 21 Bowling Group - Bowling Machine',  'U21-BOWL-BM', 2, 4, 3, 'Bowling Machine'),
('Under 21', 'fielding', 'Under 21 Fielding Group - Main Ground',     'U21-FLD-MG',  3, 4, 4, 'Main Ground'),

('Open',     'batting',  'Open Batting Group - Practice Net 2',       'OPEN-BAT-PN2', 1, 5, 1, 'Practice Net 1'),
('Open',     'bowling',  'Open Bowling Group - Bowling Machine',      'OPEN-BOWL-BM', 4, 5, 3, 'Bowling Machine'),
('Open',     'fielding', 'Open Fielding Group - Main Ground',         'OPEN-FLD-MG',  6, 1, 4, 'Main Ground');

INSERT IGNORE INTO slot_template
(TemplateName, temp_code, SlotType, StaffType, SlotID, DayOfWeek, FacilityID, AgeGroup, Category, Description, MaxParticipants, PricePerSession, RequiredPlanFeature, IsActive, CreatedBy)
SELECT
  s.TemplateName,
  s.TempCode,
  'program',
  'coach',
  s.SlotID,
  s.DayOfWeek,
  s.FacilityID,
  s.AgeGroup,
  s.Skill,
  CONCAT('Weekly ', s.AgeGroup, ' ', s.Skill, ' group session at ', s.FacilityName) AS Description,
  COALESCE((
    SELECT COUNT(DISTINCT psca.PlayerID)
    FROM player_skill_coach_assignment psca
    JOIN `user` pu ON pu.UserID = psca.PlayerID
    WHERE psca.CoachID = csg.CoachID
      AND psca.CoachingType = s.Skill
      AND psca.AgeGroup = s.AgeGroup
      AND pu.Role = 'Player'
      AND pu.Status = 'active'
  ), 10) AS MaxParticipants,
  0.00,
  'sessions',
  1,
  1
FROM tmp_group_session_seed s
JOIN coach_skill_age_group_assignment csg
  ON csg.AgeGroup = s.AgeGroup
 AND csg.CoachingType = s.Skill
 AND csg.IsActive = 1
 AND csg.PriorityRank = (
     SELECT MIN(csg2.PriorityRank)
     FROM coach_skill_age_group_assignment csg2
     WHERE csg2.AgeGroup = s.AgeGroup
       AND csg2.CoachingType = s.Skill
       AND csg2.IsActive = 1
 )
JOIN `user` u
  ON u.UserID = csg.CoachID
 AND u.Role = 'Coach'
 AND u.Status = 'active';

INSERT IGNORE INTO slot_template_staff
(TemplateID, UserID, StaffType, StaffRole, AssignedBy, AssignedAt)
SELECT
  st.TemplateID,
  csg.CoachID,
  'coach',
  'lead',
  1,
  NOW()
FROM slot_template st
JOIN tmp_group_session_seed s
  ON s.TempCode = st.temp_code
JOIN coach_skill_age_group_assignment csg
  ON csg.AgeGroup = s.AgeGroup
 AND csg.CoachingType = s.Skill
 AND csg.IsActive = 1
 AND csg.PriorityRank = (
     SELECT MIN(csg2.PriorityRank)
     FROM coach_skill_age_group_assignment csg2
     WHERE csg2.AgeGroup = s.AgeGroup
       AND csg2.CoachingType = s.Skill
       AND csg2.IsActive = 1
 )
JOIN `user` u
  ON u.UserID = csg.CoachID
 AND u.Role = 'Coach'
 AND u.Status = 'active';

INSERT IGNORE INTO slot_occurrence
(TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, Notes, GeneratedBy, CreatedAt)
SELECT
  st.TemplateID,
  st.SlotID,
  DATE_ADD(
    DATE_ADD(
      DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY),
      INTERVAL (st.DayOfWeek - 1) DAY
    ),
    INTERVAL (w.week_no * 7) DAY
  ) AS OccurrenceDate,
  st.FacilityID,
  'scheduled',
  st.MaxParticipants,
  CONCAT('Generated from template: ', st.TemplateName),
  1,
  NOW()
FROM slot_template st
JOIN tmp_group_session_seed s
  ON s.TempCode = st.temp_code
JOIN (
  SELECT 0 AS week_no UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL
  SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
  SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL
  SELECT 15 UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL
  SELECT 20 UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL
  SELECT 25 UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL
  SELECT 30 UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL
  SELECT 35 UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL
  SELECT 40 UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL
  SELECT 45 UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL
  SELECT 50 UNION ALL SELECT 51 UNION ALL SELECT 52
) w
WHERE st.IsActive = 1
  AND st.SlotType = 'program'
  AND DATE_ADD(
    DATE_ADD(
      DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY),
      INTERVAL (st.DayOfWeek - 1) DAY
    ),
    INTERVAL (w.week_no * 7) DAY
  ) <= STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-12-31'), '%Y-%m-%d');

DROP TEMPORARY TABLE IF EXISTS tmp_group_session_seed;

COMMIT;

DELETE so
FROM slot_occurrence so
JOIN slot_template st ON st.TemplateID = so.TemplateID
WHERE st.SlotType = 'private'
AND (
(st.DayOfWeek = 1 AND st.FacilityID = 1 AND st.SlotID = 5)
OR
(st.DayOfWeek = 4 AND st.FacilityID = 3 AND st.SlotID = 5)
);

DELETE FROM slot_template
WHERE SlotType = 'private'
AND (
(DayOfWeek = 1 AND FacilityID = 1 AND SlotID = 5)
OR
(DayOfWeek = 4 AND FacilityID = 3 AND SlotID = 5)
);

COMMIT;


