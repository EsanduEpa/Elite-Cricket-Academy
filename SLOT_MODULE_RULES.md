# Slot Module Rules

This document lists the current rules enforced by the slot module across the database schema, services, models, controllers, and related views.

The rules below describe the behavior that the code currently enforces. They are written in plain English so they can be used as a business-rules reference during development, testing, and future migrations.

## Scope

The document is based on the current slot-module implementation in these areas:

- `create_slot_tables.sql`
- `add_slot_booking_participant_count.sql`
- `app/libraries/SlotBookingService.php`
- `app/models/M_SlotPlayer.php`
- `app/models/M_SlotStaff.php`
- `app/models/M_SlotAdmin.php`
- `app/controllers/Playerslots.php`
- `app/controllers/Staffslots.php`
- `app/controllers/Adminslots.php`
- `app/controllers/Shop.php`
- slot-related views on the player, staff, admin, and shop sides

## Migration Note

- The slot module is now the live scheduling and booking path used by players, staff, admins, and shop employees.
- The legacy model file `app/models/M_Session.php` is not present in the current `app/models` directory.
- A repository search across `app/**` currently finds no live `M_Session` references, so the rules in this document reflect the slot-based implementation rather than the removed session-table flow.

## Schema And Data Rules

- A time band should not be deleted from the database; it should be disabled by setting `IsActive = 0`.
- The system seeds a fixed Trainer Room facility with capacity `1`, so only one trainer-room occurrence can use that room at a time.
- A slot template must use one of these slot types: `program`, `private`, or `facility_only`.
- A slot template must use one of these staff types: `coach`, `trainer`, or `none`.
- A slot template must use one of these required-plan features: `none`, `sessions`, `private_sessions`, or `facility_access`.
- A template staff assignment is unique per template and user, so the same staff member cannot be assigned to the same template twice.
- A staff override is unique per occurrence and user, so the same user cannot be inserted twice as an override for the same occurrence.
- A facility, slot, and date combination is unique at occurrence level, so two occurrences cannot use the same facility in the same time band on the same day.
- A player and occurrence combination is unique at booking level, so one player cannot have two bookings for the same occurrence.
- Booking source is limited to `self`, `admin`, or `shop_employee`.
- Booking status is limited to `pending`, `confirmed`, `cancelled`, `attended`, or `missed`.
- Payment status is limited to `not_required`, `pending`, `paid`, or `refunded`.
- Occurrence status is limited to `scheduled`, `active`, `cancelled`, or `completed`.
- The audit log is intended to be append-only, so the application should only insert audit records and should not update or delete them.

## Entitlement And Medical Rules

- A booking is blocked if the slot template does not exist.
- A booking is allowed without subscription entitlement only when the template’s required plan feature is `none`.
- A player must have an active subscription when the template requires any plan feature other than `none`.
- A player’s active plan must include the requested feature, so a plan without group sessions cannot book a session that requires `sessions`.
- A plan without private-session entitlement cannot book a slot that requires `private_sessions`.
- A plan without facility access cannot book a slot that requires `facility_access`.
- If entitlement succeeds and no subscription ID was supplied, the entitlement check returns the active subscription ID and the booking flow uses it.
- A booking is blocked if the player has an active medical record whose recovery window still covers the occurrence date.
- The medical block is based on records with `RecoveryStatus` of `ongoing` or `recovering`.
- The medical block is date-sensitive, so the same player may be blocked for one date and allowed for a later date.
- A shop employee cannot override an active medical block during counter booking.
- An admin can clear a medical block on a booking by setting the booking’s `MedicalClearedBy` field.

## Player Booking Rules

- Players only see future or current occurrences in the public booking catalog.
- Players only see occurrences whose occurrence status is `scheduled` or `active`.
- Players do not see cancelled occurrences in the player booking catalog.
- Players do not see inactive templates unless the occurrence is ad hoc and has no template.
- Players only see `private` and `facility_only` offerings in the normal available-occurrences list.
- A player cannot self-book a `program` slot through the normal self-booking path.
- A player cannot book an occurrence they already booked unless the previous booking for that occurrence was cancelled.
- A player’s booking is blocked if the occurrence is already full under the slot-capacity rules.
- Participant count is always clamped to at least `1`.
- For `private` and `facility_only` slots, participant count cannot exceed the allowed group capacity stored on the occurrence or template.
- For `private` and `facility_only` slots, the capacity rule treats the occurrence as one reservable booking, not multiple separate bookings.
- A successful self-booked regular session is stored with source `self`, booking status `confirmed`, and payment status `not_required`.
- A successful self-booked facility booking uses the submitted amount and sets payment status to `paid` when the amount is greater than zero.
- If a self-book attempt has an invalid or missing occurrence ID, the controller rejects it before the model booking logic runs.
- In the facility search flow, an invalid date format is discarded.
- In the facility search flow, a past filter date is reset forward to today.

## Player Cancellation Rules

- A player can only cancel by POST.
- A player can only cancel their own booking.
- A player cannot cancel a booking that does not exist.
- A player cannot cancel a booking that is already cancelled.
- A player cannot cancel within the closed cancellation window.
- The cancellation window is enforced as at least 24 hours before the session start.
- When a player cancellation succeeds, the system records the cancellation actor, time, and reason.

## Staff Visibility And Assignment Rules

- A coach or trainer can only see occurrences they are assigned to through the template-staff table or the occurrence-override table.
- A head coach can bypass the normal assignment restriction and see all occurrences in range.
- A staff member can only open an occurrence detail page if they are assigned to that occurrence, unless they are a head coach.
- A staff member cannot cancel an occurrence they are not assigned to.
- A staff member cannot cancel an occurrence that is already cancelled.
- A staff member must give a cancellation reason when cancelling an occurrence.
- A staff member cannot create a private session in the past because the controller blocks past dates.
- A staff member cannot create a private session without a date, time band, and facility.
- A staff member cannot create a private session if they are already assigned to another occurrence in the same time band on the same day.
- A private session created by staff is automatically self-assigned to that same staff member as the lead override.
- A private session creation attempt fails if the facility and slot are already taken on that date.
- The same occurrence can carry multiple staff assignments only when they are distinct users.

## Staff Attendance And Booking Status Rules

- Staff can only update booking outcomes for bookings that belong to occurrences they are assigned to, unless they are a head coach.
- Staff booking-status updates accept `confirmed`, `attended`, `missed`, `completed`, and `not_attended`.
- Staff-friendly status `completed` is normalized to the stored booking status `attended`.
- Staff-friendly status `not_attended` is normalized to the stored booking status `missed`.
- A staff attendance update fails if the booking does not exist.
- A staff attendance update fails if the status value is outside the allowed set.
- The staff bulk attendance handler skips values outside the allowed set.
- The staff occurrence page does not auto-mark a booking completed just because time passed; the status changes only when staff submit it.
- Cancelled bookings are shown as locked in the staff occurrence page and are not meant to be edited from that inline control.

## Admin Template And Occurrence Rules

- Admin-created templates define recurring slot offerings rather than direct one-off bookings.
- Template generation only creates occurrences on dates that match the template’s day-of-week rule when a weekday is set.
- Template generation skips collisions that violate the unique facility-slot-date rule.
- Program occurrences can auto-enroll players assigned to that template.
- Staff assigned to a template must have a role compatible with the declared staff type.
- Admin can assign lead and assistant roles to template staff.
- Admin can substitute staff for a single occurrence without changing the base template assignment.
- Admin occurrence cancellation requires a reason.
- Admin can create ad hoc occurrences outside the recurring template flow.
- Admin can toggle time bands active or inactive instead of deleting them.

## Shop Employee Rules

- Shop employees can only counter-book `facility_only` and `private` occurrences.
- Shop employees must search with at least two characters before the player search returns results.
- Shop employee search only returns active users whose role is `Player`.
- A counter booking without a valid occurrence and player is rejected.
- Counter booking still runs the medical check and capacity check before inserting the booking.
- Counter booking stores source `shop_employee`, sets payment method to `cash`, and sets payment status to `paid`.
- Shop employees can only update the status of `facility_only` bookings in the dedicated shop status-update flow.
- Shop employees cannot use that flow to update `private` or `program` bookings.
- Shop employee status updates accept `confirmed`, `completed`, and `not_attended`.
- Shop-friendly status `completed` is normalized to stored status `attended`.
- Shop-friendly status `not_attended` is normalized to stored status `missed`.
- Shop employees cannot update cancelled facility bookings or facility bookings whose occurrence is cancelled.
- The shop counter page intentionally exposes only the dropdown control for facility booking status changes, not direct action buttons.

## Visibility And Display Rules

- Program bookings are labeled differently from facility and private bookings in player-facing lists.
- A booking with source `system` is treated as an assigned program booking in player-facing views.
- Player upcoming lists exclude cancelled bookings.
- Player today and upcoming schedule widgets exclude cancelled bookings and cancelled occurrences.
- Shop counter status labels show friendly text even though the database stores `attended` and `missed`.
- Staff occurrence views show friendly text `Completed` and `Not Attended` even though the database stores `attended` and `missed`.

## Minor And Edge Validation Rules

- The entitlement service returns `template_not_found`, `no_subscription`, or `plan_mismatch` as distinct failure codes.
- The medical service returns remaining rest-day information as part of the failure payload.
- The capacity service returns `not_found` if the occurrence does not exist.
- The capacity service does not enforce a booking-count limit for non-`private` and non-`facility_only` slot types.
- Occurrence-level max participants overrides template-level max participants for capacity checks.
- Audit logging records the actor IP address whenever a booking, occurrence, or status change is logged.
- Activity logging also records the actor IP address.
- Booking creation treats a duplicate-key database exception as the user-facing `duplicate` rule rather than a generic error.
- Staff private-session creation treats facility-slot uniqueness conflicts as the user-facing `duplicate` rule rather than a generic error.

## Source Traceability

The rule groups above come from these implementation points.

- Schema and table-level constraints come from `create_slot_tables.sql`, especially the `slot_template`, `slot_template_staff`, `slot_occurrence`, `slot_occurrence_staff_override`, `slot_booking`, and `slot_audit_log` table definitions.
- Entitlement, medical, and capacity gate rules come from `app/libraries/SlotBookingService.php`, mainly `validateEntitlement()`, `checkMedicalFlag()`, and `checkCapacity()`.
- Player booking insert behavior, duplicate handling, counter-booking status updates, and facility-only status restrictions come from `app/models/M_SlotPlayer.php`, mainly `createBooking()`, `getFacilityOnlyBookingsForCounter()`, `updateFacilityBookingStatus()`, `searchPlayers()`, and the player booking and cancellation query methods.
- Player-side request validation, date sanitization, booking messages, and cancellation-window messaging come from `app/controllers/Playerslots.php`, mainly `book()`, `facilities()`, `bookfacility()`, and `cancel()`.
- Staff assignment checks, occurrence ownership checks, private-session creation, staff status normalization, and attendance audit logging come from `app/models/M_SlotStaff.php`, mainly `getMyOccurrences()`, `getOccurrenceDetail()`, `getBookingsForOccurrence()`, `createPrivateSession()`, `cancelOccurrence()`, and `markAttendance()`.
- Staff-side request validation, cancellation-reason enforcement, allowed attendance values, and private-session form validation come from `app/controllers/Staffslots.php`, mainly `occurrence()`, `attendance()`, and `private_session()`.
- Admin template-generation, staff-assignment, occurrence-management, and time-band management rules come from `app/models/M_SlotAdmin.php` and `app/controllers/Adminslots.php`.
- Shop-employee search, walk-in booking flow, medical-block enforcement, and facility-only status-update rules come from `app/controllers/Shop.php`, mainly `counter()`, `searchplayer()`, `slotbook()`, `facilities()`, and `updateFacilityBookingStatus()`.
- Friendly display labels such as `Completed` and `Not Attended` are enforced at the UI layer in the slot-related staff and shop views, while the stored booking values remain `attended` and `missed` in the database.

## Example Sentences

- A player cannot book the same occurrence twice unless the earlier booking for that same occurrence has already been cancelled.
- A player cannot book a slot if their current membership plan does not include the feature required by the slot template.
- A player cannot book a slot if they have an active injury record whose recovery window still covers the slot date.
- Staff cannot allocate a private session for a slot if they are already assigned to another session in that same time band on the same date.
- Staff cannot cancel an occurrence without entering a cancellation reason.
- Staff cannot update the attendance status of a booking if they are not assigned to the occurrence, unless they are a head coach.
- A shop employee cannot update the status of a private or program booking through the facility booking status flow.
- A shop employee cannot complete a facility booking just because time has passed; the dropdown update must be submitted explicitly.
