ALTER TABLE playeroverallstats
ADD COLUMN Centuries INT(11) DEFAULT 0,
ADD COLUMN HalfCenturies INT(11) DEFAULT 0,
ADD COLUMN FiveWickets INT(11) DEFAULT 0,
ADD COLUMN FourWickets INT(11) DEFAULT 0,
ADD COLUMN BestBowling VARCHAR(20) DEFAULT NULL COMMENT 'Best bowling figures (e.g., 5/24)';

ALTER TABLE `playermedicalrecord` 
  -- 1. Convert InjuryDetails into a dropdown list of body parts

  drop column `InjuryDetails`,
  add COLUMN `bodyarea` ENUM(
    'Head/Face', 'Neck', 'Shoulder', 'Arm/Elbow', 'Hand/Wrist', 
    'Chest/Back', 'Hip/Groin', 'Thigh', 'Knee', 'Lower Leg', 'Ankle/Foot'
  ) DEFAULT NULL,

  -- 2. Convert Diagnosis into a dropdown of injury categories
  MODIFY COLUMN `Diagnosis` ENUM(
    'Sprain', 'Strain', 'Fracture', 'Dislocation', 
    'Concussion', 'Tear', 'Laceration', 'Overuse/Inflammation', 'Illness'
  ) DEFAULT NULL,

  -- 3. Standardize TreatmentGiven into common medical actions
  MODIFY COLUMN `TreatmentGiven` ENUM(
    'RICE Procedure', 'First Aid/Wound Care', 'Physiotherapy', 
    'Medication', 'Referral to Specialist', 'Surgery', 'Observation'
  ) DEFAULT NULL,

  -- 4. Update RecoveryStatus to match your specific form labels
  MODIFY COLUMN `RecoveryStatus` ENUM(
    'ongoing', 'recovering', 'fully_recovered', 'chronic_condition'
  ) DEFAULT 'ongoing';