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

COMMIT;

-- Notes:
-- - `user.UserID` is the parent key for coach profiles.
-- - `tr_create_role_profile` creates `coachprofile` automatically for `Role = 'Coach'`.
-- - `coachprofile.Specialization` is now limited to Batting, Bowling, and Fielding.