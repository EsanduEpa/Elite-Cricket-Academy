# Slots Module — Q&A Supplement (Not in Main Design Doc)
**Elite Cricket Academy**
**Date:** April 6, 2026

This file contains the Q&A guidance we discussed in chat that is **not already captured** in `SLOTS_MODULE_DESIGN.md`.

---

## 1) Can we reuse `sessionenrollment` / `sessionattendance` / `sessionpayment` instead of `slot_booking`?

**Answer:** You *can* reuse them only by heavily altering them, but that creates more problems than it solves.

### Why reusing `sessionenrollment` is not a good fit
`sessionenrollment` is bound to the **legacy** `session.SessionID` flow. The new module uses `slot_occurrence.OccurrenceID` as the calendar truth.

To make `sessionenrollment` act like `slot_booking`, you would need to add (at minimum):
- Booking source (`self` / `admin` / `shop_employee`)
- Subscription linkage (`SubscriptionID`)
- Medical override (`MedicalClearedBy`)
- Payment fields or a reliable join to payment
- Proper cancellation attribution (`CancelledBy`, `CancelledAt`, `CancelReason`)

That would also require refactoring existing code that currently does:
- `sessionenrollment.SessionID → session.SessionID`

### Why reusing `sessionattendance` is problematic
Even though `sessionattendance` is structurally similar, it points to `EnrollmentID` (legacy chain). MariaDB cannot enforce one FK that points to two possible parent tables.

### Recommended approach
- Keep `session*` tables **for historical data**.
- For all new scheduling/booking, write to:
  - `slot_occurrence`
  - `slot_booking`
- Keep a migration bridge using `slot_occurrence.LegacySessionID` so old `coachingsession`, `sessionenrollment`, and `sessionpayment` records remain traceable.

---

## 2) Can we reuse `coachappointment` as the private booking table?

**Answer:** Only if you restrict it to 1-to-1 private sessions, and you still need slot occurrences to enforce facility/time conflicts.

### What `coachappointment` already does well
- Represents 1 coach ↔ 1 player
- Has date/time and status

### What it does not cover
- Facility-only booking (no coach)
- Group sessions (many players)
- Recurring templates → occurrences
- Unified booking reporting across coach/trainer/facility
- Subscription + medical gating metadata

### Best practice for reuse
If you keep `coachappointment` for now (because UI depends on it), treat it as **legacy** and gradually migrate to:
- `slot_occurrence` (the time block)
- `slot_booking` (the player taking the time block)

In the slot module, a private coaching session is just:
- One `slot_occurrence` with a private template
- One `slot_booking` (MaxParticipants = 1)

---

## 3) Can we reuse `trainerappointment` similarly?

**Answer:** Same logic as `coachappointment`.

Use it only for legacy 1-to-1 trainer appointments if needed. In the slot module, a trainer private session is also:
- `slot_template` (`SlotType='private'`, `StaffType='trainer'`, FacilityID = Trainer Room)
- `slot_occurrence`
- `slot_booking`

---

## 4) Why is “Trainer Room” added to `facility` instead of giving trainers a separate session table?

Trainers are not “player-bookable facilities”, but they **do occupy a physical location** and can be double-booked.

If trainer sessions used `FacilityID = NULL`, then a UNIQUE constraint like `(FacilityID, SlotID, OccurrenceDate)` would not reliably prevent conflicts.

So the safest pattern is:
- Add one facility row: **Trainer Room (FacilityID = 6)**
- All trainer session occurrences use `FacilityID = 6`
- The same DB uniqueness rule prevents double-booking automatically

---

## 5) “Coach publishes availability” — what does that mean?

It means the coach is **opening time windows** for private sessions *before a player is chosen*.

DB meaning:
- Create `slot_occurrence` rows for future dates/times as “available”
- There is **no player yet**, so no `slot_booking` row yet
- When a player/admin/shop books it, the system inserts `slot_booking`

This supports real operations where:
- coach says “I’m free on Wed 3–5, Fri 5–7”
- players pick and book those slots later

---

## 6) Business flow: Coach allocates himself a private coaching session slot

Two valid interpretations:

### A) Coach opens private availability (recommended)
1. Admin creates a private template once (`slot_template`):
   - `SlotType='private'`, `StaffType='coach'`, `MaxParticipants=1`
2. Admin assigns the coach to it (`slot_template_staff`)
3. Occurrences are created for selected dates (`slot_occurrence`)
4. Player/admin/shop books it later (`slot_booking`)

### B) Coach schedules a private session for a specific player
1. Create one `slot_occurrence`
2. Insert the player’s booking immediately (`slot_booking`)

---

## 7) Can facilities be booked without a coach?

Yes.

In the slot module, this is the purpose of:
- `slot_template` with `SlotType='facility_only'` and `StaffType='none'`
- occurrences (`slot_occurrence`) represent availability blocks
- bookings (`slot_booking`) represent who took the block

Typical booking sources:
- player self-booking: `BookingSource='self'`
- counter booking: `BookingSource='shop_employee'`

---

## 8) Business flow: How does a trainer get a session for him?

Same mechanics as a coach, but with staff type = trainer.

### Trainer group session
1. Admin creates template: `slot_template` (`SlotType='program'`, `StaffType='trainer'`, `FacilityID=6`)
2. Admin assigns trainer: `slot_template_staff`
3. Generate occurrences: `slot_occurrence`

### Trainer 1-to-1 session
1. Admin creates template: `slot_template` (`SlotType='private'`, `StaffType='trainer'`, `FacilityID=6`, `MaxParticipants=1`)
2. Admin assigns trainer: `slot_template_staff`
3. Create occurrences: `slot_occurrence`
4. Book it: `slot_booking`

---

## 9) What should be replaced (stop using for new writes) vs kept?

### Replace (freeze after migration; keep as historical)
- `session`
- `sessionenrollment`
- `sessionpayment`
- `sessiondetails`
- `sessionattendance` (already out of scope “for now”)
- `facilitybooking`

### Keep (still valuable in the new system)
- `facility` (add Trainer Room row)
- `coachprofile`
- `trainerprofile`
- `coachingsession` (post-session coaching notes; optional attendance indicator if needed later)

---

## 10) Attendance for now

You requested **no dedicated attendance table for now**.

Practical interim handling:
- Treat `slot_booking` as the “expected participants” list.
- If needed later, use `coachingsession.AttendanceStatus` as a coaching-side attendance indicator.
- Add a separate attendance table only when the UI/reporting requires it.
