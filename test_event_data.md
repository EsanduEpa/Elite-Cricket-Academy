# Event Creation Test Plan

## Database Changes Completed:
✅ Removed OrganizedBy column and its foreign key (event_ibfk_1)
✅ Database now has 16 columns total

## Code Changes Completed:
✅ Controller: Removed 'organized_by' from $eventData array
✅ Controller: Fixed description to return NULL if empty (not empty string)
✅ Controller: Fixed registration_fee to return NULL if empty (not 0.00)
✅ Model: Removed OrganizedBy from INSERT statement
✅ Model: Removed :organized_by bind parameter

## Current Event Table Structure (16 columns):
1. EventID (int, AUTO_INCREMENT, PRIMARY KEY)
2. Name (varchar)
3. Type (enum)
4. Category (enum)
5. Description (text) - **NOW PROPERLY HANDLES NULL**
6. StartDate (datetime)
7. EndDate (datetime)
8. Location (varchar)
9. Status (enum)
10. RegistrationStart (datetime)
11. RegistrationEnd (datetime)
12. PrimaryContact (varchar)
13. ContactEmail (varchar)
14. ContactPhone (varchar)
15. MaxParticipants (int) - **NOW PROPERLY HANDLES NULL**
16. RegistrationFee (decimal) - **NOW PROPERLY HANDLES NULL**

## Test Scenarios:

### Test 1: Event with all fields filled
- Fill description, max participants (e.g., 50), registration fee (e.g., 1500.00)
- Expected: All values saved correctly

### Test 2: Event with optional fields empty
- Leave description empty, max participants empty, registration fee empty
- Expected: Description = NULL, MaxParticipants = NULL, RegistrationFee = NULL

### Test 3: Event with registration fee = 0
- Enter 0 in registration fee field
- Expected: RegistrationFee = 0.00 (NOT NULL)

## Verification Query:
```sql
SELECT EventID, Name, Description, MaxParticipants, RegistrationFee 
FROM Event 
ORDER BY EventID DESC LIMIT 5;
```

