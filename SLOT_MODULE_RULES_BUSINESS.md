# Slot Module Rules For Business And QA

This document is a shorter non-technical summary of how the slot module is supposed to behave in day-to-day use.

## What The Slot Module Controls

- The slot module is the live system for session scheduling, private sessions, facility bookings, attendance outcomes, and walk-in counter bookings.
- The old session-table flow is not the current business path.

## Booking Rules

- Players can only book valid future or current slot occurrences that are still available.
- Players cannot book the same occurrence twice unless the earlier booking was cancelled.
- Players cannot self-book program slots through the normal self-booking flow.
- Facility-only and private slots behave like a single reservable slot, so once one booking holds the slot, another separate booking cannot take it.
- Participant count can never be less than one.
- Participant count for facility-only and private slots cannot exceed the group capacity defined on the occurrence or template.

## Membership And Medical Rules

- If a slot requires a membership feature, the player must have an active subscription that includes that feature.
- If a slot does not require a membership feature, a subscription is not required.
- A player with an active injury or recovery block cannot be booked into a slot while the blocked recovery period still covers that date.
- Shop employees cannot override a medical block during walk-in booking.
- Admins can clear a medical block on the booking record when they explicitly approve it.

## Cancellation Rules

- Players can only cancel their own bookings.
- Players must cancel at least 24 hours before the session start time.
- Staff and admins must give a cancellation reason when cancelling an occurrence.
- Cancelled bookings and cancelled occurrences are treated as locked for normal status-update actions.

## Staff Rules

- Coaches and trainers can only see and manage occurrences they are assigned to, unless they have the head-coach bypass.
- Coaches and trainers can manually change booking outcomes to Confirmed, Completed, or Not Attended.
- The system does not auto-complete a booking just because the session time has passed.
- Staff can create ad hoc private sessions, but not in the past and not if they already have another assigned occurrence in the same time band on the same date.

## Shop Employee Rules

- Shop employees can create counter bookings only for private and facility-only slots.
- Shop employees must choose a valid player and a valid occurrence before a walk-in booking can be created.
- Shop employees can update booking outcomes only for facility-only bookings in the dedicated shop status-update flow.
- Shop employees cannot use that flow to change private or program bookings.

## Admin Rules

- Admins manage recurring templates, generate occurrences, assign staff, create ad hoc occurrences, and manage time bands.
- Time bands are meant to be disabled instead of deleted.
- Per-date staff overrides affect only that occurrence and do not rewrite the base template.

## Status Rules

- Stored booking statuses remain `pending`, `confirmed`, `cancelled`, `attended`, and `missed`.
- The UI label `Completed` means the stored status `attended`.
- The UI label `Not Attended` means the stored status `missed`.
- Occurrence status and booking status are different concepts and should not be mixed together.

## QA Checks Worth Repeating

- Confirm that a player with no valid plan is blocked from slots that require plan entitlement.
- Confirm that a player with an active medical block is rejected in both self-booking and shop counter booking.
- Confirm that duplicate booking attempts are rejected.
- Confirm that staff can update only their own occurrence bookings unless they are a head coach.
- Confirm that shop employees can update only facility-only booking outcomes.
- Confirm that a past session does not automatically become completed without a manual action.