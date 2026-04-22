-- =============================================================
-- TEST DATA: Facility Booking Module  (playerslots/facilities)
-- =============================================================
-- Run this file against cricket_academy to load test data.
-- It is safe to re-run; each block uses INSERT IGNORE or
-- checks for existence before inserting.
--
-- What it creates
-- ───────────────
-- slot_template     : 5 new templates (facility_only x4, private x1)
-- slot_occurrence   : 16 upcoming occurrences (Apr 10 – Apr 20 2026)
-- playersubscription: player 7  → general plan (no facility access)
--                     player 20 → pro plan     (full facility + private)
-- slot_booking      : player 15 fills Bowling Machine Apr-10  (FULL demo)
--                     player 15 books Practice Net 1  Apr-10  (ALREADY BOOKED demo)
--
-- UI states exercised per player
-- ────────────────────────────────────────────────────────────
-- Player 15 (Swairi)     – private plan  – [Already Booked] + [Full] + [Book]
-- Player 20 (esandu yapa)– pro plan      – [Book] on everything
-- Player 7  (Vijini)     – general plan  – [Plan upgrade] on facility_access/private slots
-- Any no-plan player     –               – [No subscription] on gated slots
-- =============================================================

USE cricket_academy;

-- ─────────────────────────────────────────────────────────────
-- 1. UNDO (clean previous run so this is idempotent)
-- ─────────────────────────────────────────────────────────────
DELETE FROM slot_booking
WHERE OccurrenceID IN (
    SELECT OccurrenceID FROM slot_occurrence
    WHERE TemplateID IN (4,5,6,7,8)
);

DELETE FROM slot_occurrence_staff_override
WHERE OccurrenceID IN (
    SELECT OccurrenceID FROM slot_occurrence
    WHERE TemplateID IN (4,5,6,7,8)
);

DELETE FROM slot_occurrence  WHERE TemplateID IN (4,5,6,7,8);
DELETE FROM slot_template    WHERE TemplateID IN (4,5,6,7,8);

DELETE FROM playersubscription WHERE PlayerID IN (7,20) AND PlanID IN (1,3);

-- ─────────────────────────────────────────────────────────────
-- 2. SLOT TEMPLATES
-- ─────────────────────────────────────────────────────────────
-- RequiredPlanFeature:
--   none             → anyone can book (open access)
--   facility_access  → player must have FacilityAccessIncluded = 1
--   private_sessions → player must have PrivateSessionsIncluded > 0

INSERT INTO slot_template
  (TemplateID, TemplateName, SlotType, StaffType, SlotID, FacilityID,
  MaxParticipants, PricePerSession, RequiredPlanFeature,
  IsActive, CreatedBy)
VALUES
  -- ID 4 – open, Practice Net 1, 11 am–1 pm, max 3
  (4, 'Practice Net 1 (Open)',
  'facility_only', 'none', 2, 1, 3, 0.00, 'none', 1, 1),

  -- ID 5 – gated, Bowling Machine, 1 pm–3 pm, max 1  ← tests FULL state
  (5, 'Bowling Machine Session',
    'facility_only', 'none', 3, 3, 1, 0.00, 'facility_access', 1, 1),

  -- ID 6 – gated, Indoor Training Hall, 3 pm–5 pm, max 8
  (6, 'Indoor Hall Access',
    'facility_only', 'none', 4, 5, 8, 0.00, 'facility_access', 1, 1),

  -- ID 7 – open, Practice Net 2, 5 pm–7 pm, max 3
  (7, 'Practice Net 2 (Evening)',
    'facility_only', 'none', 5, 2, 3, 0.00, 'none', 1, 1),

  -- ID 8 – private coaching, Practice Net 1, 3 pm–5 pm, max 1, LKR 1500
  (8, 'Private 1-on-1 Coaching',
    'private', 'coach', 4, 1, 1, 1500.00, 'private_sessions', 1, 1);

  UPDATE slot_template st
  LEFT JOIN slot_time_band tb ON tb.SlotID = st.SlotID
  SET st.temp_code = CASE
    WHEN st.SlotType = 'facility_only' THEN CONCAT(
      'FAC-',
      COALESCE(
        CASE st.DayOfWeek
          WHEN 1 THEN 'MON'
          WHEN 2 THEN 'TUE'
          WHEN 3 THEN 'WED'
          WHEN 4 THEN 'THU'
          WHEN 5 THEN 'FRI'
          WHEN 6 THEN 'SAT'
          WHEN 7 THEN 'SUN'
        END,
        'ANY'
      ),
      '-',
      COALESCE(NULLIF(REPLACE(REPLACE(UPPER(tb.SlotLabel), ' ', ''), '-', ''), ''), CONCAT('S', st.SlotID)),
      '-',
      st.TemplateID
    )
    WHEN st.SlotType = 'private' THEN CONCAT(
      'PVT-',
      COALESCE(
        CASE st.Category
          WHEN 'Batting' THEN 'BAT'
          WHEN 'Bowling' THEN 'BOWL'
          WHEN 'Fielding' THEN 'FLD'
          WHEN 'Fitness' THEN 'FIT'
        END,
        'GEN'
      ),
      '-',
      COALESCE(NULLIF(REPLACE(REPLACE(UPPER(tb.SlotLabel), ' ', ''), '-', ''), ''), CONCAT('S', st.SlotID)),
      '-',
      st.TemplateID
    )
    ELSE CONCAT(
      COALESCE(
        CASE st.AgeGroup
          WHEN 'Under 11' THEN 'U11'
          WHEN 'Under 13' THEN 'U13'
          WHEN 'Under 15' THEN 'U15'
          WHEN 'Under 17' THEN 'U17'
          WHEN 'Under 19' THEN 'U19'
          WHEN 'Under 21' THEN 'U21'
          WHEN 'Open' THEN 'OPEN'
        END,
        'GEN'
      ),
      '-',
      COALESCE(
        CASE st.Category
          WHEN 'Batting' THEN 'BAT'
          WHEN 'Bowling' THEN 'BOWL'
          WHEN 'Fielding' THEN 'FLD'
          WHEN 'Fitness' THEN 'FIT'
        END,
        'GEN'
      ),
      '-',
      COALESCE(NULLIF(REPLACE(REPLACE(UPPER(tb.SlotLabel), ' ', ''), '-', ''), ''), CONCAT('S', st.SlotID)),
      '-',
      st.TemplateID
    )
  END
  WHERE st.TemplateID IN (4,5,6,7,8);

-- ─────────────────────────────────────────────────────────────
-- 3. OCCURRENCES  (dates: Apr 10–20 2026)
-- ─────────────────────────────────────────────────────────────

-- Template 4: Practice Net 1 (Open) – SlotID 2, FacilityID 1
INSERT INTO slot_occurrence (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy) VALUES
  (4, 2, '2026-04-10', 1, 'scheduled', 1),
  (4, 2, '2026-04-11', 1, 'scheduled', 1),
  (4, 2, '2026-04-14', 1, 'scheduled', 1),
  (4, 2, '2026-04-17', 1, 'scheduled', 1),
  (4, 2, '2026-04-20', 1, 'scheduled', 1);

-- Template 5: Bowling Machine – SlotID 3, FacilityID 3, MaxParticipants=1 per row
INSERT INTO slot_occurrence (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, GeneratedBy) VALUES
  (5, 3, '2026-04-10', 3, 'scheduled', 1, 1),
  (5, 3, '2026-04-15', 3, 'scheduled', 1, 1),
  (5, 3, '2026-04-19', 3, 'scheduled', 1, 1);

-- Template 6: Indoor Hall Access – SlotID 4, FacilityID 5
INSERT INTO slot_occurrence (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy) VALUES
  (6, 4, '2026-04-11', 5, 'scheduled', 1),
  (6, 4, '2026-04-16', 5, 'scheduled', 1),
  (6, 4, '2026-04-18', 5, 'scheduled', 1);

-- Template 7: Practice Net 2 (Evening) – SlotID 5, FacilityID 2
INSERT INTO slot_occurrence (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy) VALUES
  (7, 5, '2026-04-12', 2, 'scheduled', 1),
  (7, 5, '2026-04-15', 2, 'scheduled', 1),
  (7, 5, '2026-04-19', 2, 'scheduled', 1);

-- Template 8: Private 1-on-1 Coaching – SlotID 4, FacilityID 1, MaxParticipants=1
INSERT INTO slot_occurrence (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, GeneratedBy) VALUES
  (8, 4, '2026-04-13', 1, 'scheduled', 1, 1),
  (8, 4, '2026-04-20', 1, 'scheduled', 1, 1);

-- ─────────────────────────────────────────────────────────────
-- 4. PLAYER SUBSCRIPTIONS
-- ─────────────────────────────────────────────────────────────
-- Note: playersubscription.PlayerID FKs to playerprofile.PlayerID
-- Both players 7 and 20 have playerprofile rows.

-- Player 7 (Vijini) – general plan: FacilityAccessIncluded=0, PrivateSessionsIncluded=0
-- Expected UI: plan_mismatch on facility_access and private_sessions templates
INSERT INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee)
VALUES (7, 1, '2026-04-01', '2027-04-01', 'active', 4500.00);

-- Player 20 (esandu yapa) – pro plan: FacilityAccessIncluded=1, PrivateSessionsIncluded=2
-- Expected UI: [Book] on every template
INSERT INTO playersubscription (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee)
VALUES (20, 3, '2026-04-01', '2027-04-01', 'active', 10000.00);

-- ─────────────────────────────────────────────────────────────
-- 5. PRE-FILLED BOOKINGS (for blocked-state testing)
-- ─────────────────────────────────────────────────────────────

-- Player 15 fills the Bowling Machine on Apr-10 (max=1)
-- → every other player sees [Full]
INSERT INTO slot_booking (OccurrenceID, PlayerID, BookingSource, Status, AmountCharged, PaymentStatus, BookedBy)
SELECT o.OccurrenceID, 15, 'self', 'confirmed', 0.00, 'not_required', 15
FROM slot_occurrence o
WHERE o.TemplateID = 5 AND o.OccurrenceDate = '2026-04-10';

-- Player 15 also books Practice Net 1 on Apr-10
-- → player 15 sees [Already Booked] for that row
INSERT INTO slot_booking (OccurrenceID, PlayerID, BookingSource, Status, AmountCharged, PaymentStatus, BookedBy)
SELECT o.OccurrenceID, 15, 'self', 'confirmed', 0.00, 'not_required', 15
FROM slot_occurrence o
WHERE o.TemplateID = 4 AND o.OccurrenceDate = '2026-04-10';

-- ─────────────────────────────────────────────────────────────
-- 6. VERIFICATION
-- ─────────────────────────────────────────────────────────────
SELECT
  so.OccurrenceDate,
  tb.SlotLabel,
  f.Name                                          AS Facility,
  st.TemplateName,
  st.SlotType,
  st.RequiredPlanFeature,
  COALESCE(so.MaxParticipants, st.MaxParticipants) AS MaxSlots,
  COUNT(sb.BookingID)                             AS Booked
FROM slot_occurrence so
JOIN slot_template   st ON st.TemplateID = so.TemplateID
JOIN slot_time_band  tb ON tb.SlotID     = so.SlotID
JOIN facility         f ON f.FacilityID  = so.FacilityID
LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID
                          AND sb.Status != 'cancelled'
WHERE st.SlotType IN ('facility_only', 'private')
  AND so.OccurrenceDate >= CURDATE()
GROUP BY so.OccurrenceID
ORDER BY so.OccurrenceDate, tb.StartTime;

-- Covers all UI states visible on the player facility page:
--   [Book]            players with matching plan or open-access template
--   [Plan upgrade]    Player 6 (general plan) on facility_access templates
--   [No subscription] Player 7 (no plan) on facility_access templates
--   [Full]            Bowling Machine Apr-10 (max 1, filled by player 15)
--   [Already Booked]  Player 15 on Practice Net 1 Apr-10
--   [Medical hold]    Trigger manually via admin injury record if needed
-- =============================================================

USE cricket_academy;

-- ─────────────────────────────────────────────────────────────
-- 1. SLOT TEMPLATES  (facility_only + private)
-- ─────────────────────────────────────────────────────────────
INSERT INTO slot_template
  (TemplateName, SlotType, StaffType, SlotID, FacilityID,
  MaxParticipants, PricePerSession, RequiredPlanFeature,
  IsActive, CreatedBy)
VALUES
  -- Open access, no plan required — Practice Net 1, 11am-1pm, max 3
  ('Practice Net 1 (Open)',
  'facility_only', 'none', 2, 1, 3, 0.00, 'none', 1, 1),

  -- Facility-access plan required — Bowling Machine, 1pm-3pm, max 1 (testing FULL state)
  ('Bowling Machine Session',
    'facility_only', 'none', 3, 3, 1, 0.00, 'facility_access', 1, 1),

  -- Facility-access plan required — Indoor Training Hall, 3pm-5pm, max 8
  ('Indoor Hall Access',
    'facility_only', 'none', 4, 5, 8, 0.00, 'facility_access', 1, 1),

  -- Open access, no plan required — Practice Net 2, 5pm-7pm, max 3
  ('Practice Net 2 (Evening)',
    'facility_only', 'none', 5, 2, 3, 0.00, 'none', 1, 1),

  -- Private coaching — needs private_sessions plan, max 1, LKR 1500
  ('Private 1-on-1 Coaching',
    'private', 'coach', 4, 1, 1, 1500.00, 'private_sessions', 1, 1);

-- ─────────────────────────────────────────────────────────────
-- 2. PLAYER SUBSCRIPTIONS
-- ─────────────────────────────────────────────────────────────
-- Player 6  -> general plan (FacilityAccessIncluded=0)
--              shows plan_mismatch on facility_access templates
INSERT INTO playersubscription
  (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee)
VALUES
  (6, 1, '2026-04-01', '2027-04-01', 'active', 4500.00);

-- Player 20 -> pro plan (FacilityAccessIncluded=1, PrivateSessionsIncluded=2)
--              can book every template including private coaching
INSERT INTO playersubscription
  (PlayerID, PlanID, StartDate, EndDate, Status, MonthlyFee)
VALUES
  (20, 3, '2026-04-01', '2027-04-01', 'active', 10000.00);

-- Player 7 remains with NO subscription -> shows no_subscription on gated templates

-- ─────────────────────────────────────────────────────────────
-- 3. SLOT OCCURRENCES (next 2 weeks, across all 5 new templates)
-- ─────────────────────────────────────────────────────────────

-- Template 4: Practice Net 1 (Open) — SlotID=2, FacilityID=1
INSERT INTO slot_occurrence
  (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy)
VALUES
  (4, 2, '2026-04-10', 1, 'scheduled', 1),
  (4, 2, '2026-04-11', 1, 'scheduled', 1),
  (4, 2, '2026-04-14', 1, 'scheduled', 1),
  (4, 2, '2026-04-17', 1, 'scheduled', 1),
  (4, 2, '2026-04-20', 1, 'scheduled', 1);

-- Template 5: Bowling Machine — SlotID=3, FacilityID=3, MaxParticipants=1 per row
INSERT INTO slot_occurrence
  (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, GeneratedBy)
VALUES
  (5, 3, '2026-04-10', 3, 'scheduled', 1, 1),  -- will be filled (FULL state demo)
  (5, 3, '2026-04-15', 3, 'scheduled', 1, 1),
  (5, 3, '2026-04-19', 3, 'scheduled', 1, 1);

-- Template 6: Indoor Hall Access — SlotID=4, FacilityID=5
INSERT INTO slot_occurrence
  (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy)
VALUES
  (6, 4, '2026-04-11', 5, 'scheduled', 1),
  (6, 4, '2026-04-16', 5, 'scheduled', 1),
  (6, 4, '2026-04-18', 5, 'scheduled', 1);

-- Template 7: Practice Net 2 (Evening) — SlotID=5, FacilityID=2
INSERT INTO slot_occurrence
  (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, GeneratedBy)
VALUES
  (7, 5, '2026-04-12', 2, 'scheduled', 1),
  (7, 5, '2026-04-15', 2, 'scheduled', 1),
  (7, 5, '2026-04-19', 2, 'scheduled', 1);

-- Template 8: Private 1-on-1 Coaching — SlotID=4, FacilityID=1, MaxParticipants=1
INSERT INTO slot_occurrence
  (TemplateID, SlotID, OccurrenceDate, FacilityID, Status, MaxParticipants, GeneratedBy)
VALUES
  (8, 4, '2026-04-13', 1, 'scheduled', 1, 1),
  (8, 4, '2026-04-20', 1, 'scheduled', 1, 1);

-- ─────────────────────────────────────────────────────────────
-- 4. PRE-FILL BOOKINGS (for UI state testing)
-- ─────────────────────────────────────────────────────────────

-- Player 15 books the Bowling Machine on Apr-10 (max=1) → FULL for everyone else
INSERT INTO slot_booking
  (OccurrenceID, PlayerID, BookingSource, Status, AmountCharged, PaymentStatus, BookedBy)
SELECT OccurrenceID, 15, 'self', 'confirmed', 0.00, 'not_required', 15
FROM slot_occurrence
WHERE TemplateID = 5 AND OccurrenceDate = '2026-04-10';

-- Player 15 also books Practice Net 1 on Apr-10 → shows ALREADY BOOKED when player 15 logs in
INSERT INTO slot_booking
  (OccurrenceID, PlayerID, BookingSource, Status, AmountCharged, PaymentStatus, BookedBy)
SELECT OccurrenceID, 15, 'self', 'confirmed', 0.00, 'not_required', 15
FROM slot_occurrence
WHERE TemplateID = 4 AND OccurrenceDate = '2026-04-10';

-- ─────────────────────────────────────────────────────────────
-- Verification query — run to confirm rows inserted
-- ─────────────────────────────────────────────────────────────
SELECT
  so.OccurrenceDate,
  tb.SlotLabel,
  f.Name        AS Facility,
  st.TemplateName,
  st.SlotType,
  st.RequiredPlanFeature,
  COALESCE(so.MaxParticipants, st.MaxParticipants) AS MaxSlots,
  COUNT(sb.BookingID) AS Booked
FROM slot_occurrence so
JOIN slot_template  st ON st.TemplateID = so.TemplateID
JOIN slot_time_band tb ON tb.SlotID     = so.SlotID
JOIN facility        f ON f.FacilityID  = so.FacilityID
LEFT JOIN slot_booking sb ON sb.OccurrenceID = so.OccurrenceID AND sb.Status != 'cancelled'
WHERE st.SlotType IN ('facility_only', 'private')
  AND so.OccurrenceDate >= CURDATE()
GROUP BY so.OccurrenceID
ORDER BY so.OccurrenceDate, tb.StartTime;
