# Macro Percentage Validation Enhancement

## Overview
Enhanced the nutrition plan system with comprehensive macro percentage validation at three levels:
1. **Client-side JavaScript validation** - Real-time feedback as user types
2. **Server-side PHP validation** - Prevents invalid data submission
3. **Database-level constraints** - Final safeguard against invalid data

## Validation Requirements

### Macro Percentages
- **Individual percentages**: Must be 0-100%
- **Total percentage**: Protein + Carbohydrate + Fat must equal exactly 100% (within 0.01 tolerance for floating-point rounding)
- **All fields required**: If any macro is provided, all three must be valid

### Recommended Calories
- **Range**: Must be between 500-10,000 calories
- **Optional**: Can be NULL for backward compatibility

## Implementation Details

### Level 1: Client-Side Validation (JavaScript)

**Files Modified:**
- `app/views/trainer/create_nutrition.php` (line 656)
- `app/views/trainer/edit_nutrition.php` (line 717)

**Enhanced Error Message:**
Before: "Protein, carbohydrate, and fat percentages must total 100%."
After: "Protein (40%) + Carbohydrate (35%) + Fat (25%) = 100.00%. They must total exactly 100%."

**Behavior:**
- Validates as user types
- Shows calculated total in real-time
- Prevents form submission if validation fails
- Displays on the Fat % field (where the calculation should be corrected)

### Level 2: Server-Side Validation (PHP)

**File:** `app/controllers/Nutrition.php`

**New Validation Method:**
- `_validateMacroPercentages($protein, $carbs, $fat): array`
  - Validates individual percentage ranges (0-100)
  - Validates total equals 100 (within tolerance)
  - Returns array of error messages

**Enhanced Error Message:**
Includes the actual values entered so trainer can see what needs correcting.

Example:
```
Protein (40%) + Carbohydrate (35%) + Fat (25%) = 100.00%. They must total exactly 100%.
```

**When Triggered:**
- Form submission (create or edit)
- Both fresh submissions and re-renders after validation failure
- Always validates before updating database

### Level 3: Database Constraints (SQL)

**File:** `add_macro_validation.sql` (new migration script)

**Constraints Added:**

1. **Template-Level Constraints** (nutrition_plan_templates table):
   ```sql
   -- Macro percentages must total 100%
   CHECK (ABS((ProteinPercentage + CarbohydratePercentage + FatPercentage) - 100) <= 0.01)
   
   -- Individual percentages must be 0-100
   CHECK (ProteinPercentage >= 0 AND ProteinPercentage <= 100
          AND CarbohydratePercentage >= 0 AND CarbohydratePercentage <= 100
          AND FatPercentage >= 0 AND FatPercentage <= 100)
   
   -- Calories must be 500-10000
   CHECK (RecommendedCalories >= 500 AND RecommendedCalories <= 10000)
   ```

2. **Plan-Level Constraints** (nutritionplan table):
   ```sql
   -- Allow NULL (backward compatibility) OR all three must be valid and total 100%
   CHECK ((ProteinPercentage IS NULL AND CarbohydratePercentage IS NULL AND FatPercentage IS NULL)
          OR (ProteinPercentage IS NOT NULL AND CarbohydratePercentage IS NOT NULL AND FatPercentage IS NOT NULL
              AND ABS((ProteinPercentage + CarbohydratePercentage + FatPercentage) - 100) <= 0.01))
   
   -- Individual percentages must be 0-100 or NULL
   CHECK ((ProteinPercentage IS NULL OR (ProteinPercentage >= 0 AND ProteinPercentage <= 100))
          AND (CarbohydratePercentage IS NULL OR (CarbohydratePercentage >= 0 AND CarbohydratePercentage <= 100))
          AND (FatPercentage IS NULL OR (FatPercentage >= 0 AND FatPercentage <= 100)))
   
   -- Calories must be 500-10000 or NULL
   CHECK (RecommendedCalories IS NULL OR (RecommendedCalories >= 500 AND RecommendedCalories <= 10000))
   ```

**Benefits:**
- Prevents invalid data insertion via direct SQL
- Protects against API bypasses
- Ensures data integrity at source
- Clear error messages from database

## How It Works

### Create Flow:
1. User fills in nutrition form with macros
2. **JavaScript validates** - Shows real-time feedback
3. User submits form
4. **PHP validates** - Returns friendly errors if invalid
5. Form re-renders with error messages
6. User corrects and resubmits
7. **PHP validates** again - If OK, proceeds
8. **SQL constraints validate** - Final safeguard
9. Plan is created

### Edit Flow:
Same as create flow, plus:
- Existing plan values are displayed
- Can modify any fields
- All three validation layers apply

### Backward Compatibility:
- Old plans with NULL macro fields are allowed
- New plans MUST have all macros (validated)
- Old plans CAN be updated with new macros
- If updating old plan, all macros must be provided

## Error Scenarios & Responses

### Scenario 1: User enters 40, 35, 30 (totals 105%)
**Client-side:** "Protein (40%) + Carbohydrate (35%) + Fat (30%) = 105.00%. They must total exactly 100%."
**Server-side:** Same message
**Database:** Would reject if somehow bypassed

### Scenario 2: User enters 40, 35, 25 (totals 100%) ✓
**Client-side:** Allows form submission
**Server-side:** Accepts and proceeds
**Database:** Accepts and persists

### Scenario 3: User enters 40, 35, 24.99 (totals 99.99%) ✓
**Client-side:** Allows (tolerance of 0.01)
**Server-side:** Accepts (within tolerance)
**Database:** Accepts (within tolerance)

### Scenario 4: User enters only some macros (40, 35, NULL)
**Client-side:** "Enter a valid percentage between 0 and 100."
**Server-side:** Same message
**Database:** Rejected by constraint

## Installation

### Step 1: Apply Database Migration
```bash
mysql -u root -p cricket_academy < add_macro_validation.sql
```

Or manually in phpMyAdmin:
1. Copy contents of `add_macro_validation.sql`
2. Paste into SQL query box
3. Execute

### Step 2: Verify Installation
Check that constraints exist:
```sql
-- View constraints on templates
SELECT CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
WHERE TABLE_NAME = 'nutrition_plan_templates'
AND CONSTRAINT_TYPE = 'CHECK';

-- View constraints on plans
SELECT CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
WHERE TABLE_NAME = 'nutritionplan'
AND CONSTRAINT_TYPE = 'CHECK';
```

## Testing

### Test Case 1: Valid Macros
1. Create/Edit nutrition plan
2. Enter: Protein 40%, Carbs 35%, Fat 25%
3. Expected: ✓ Saves successfully

### Test Case 2: Invalid Total (High)
1. Create/Edit nutrition plan
2. Enter: Protein 40%, Carbs 35%, Fat 30%
3. Expected: ✗ Error message showing total 105%

### Test Case 3: Invalid Total (Low)
1. Create/Edit nutrition plan
2. Enter: Protein 40%, Carbs 30%, Fat 25%
3. Expected: ✗ Error message showing total 95%

### Test Case 4: Out of Range
1. Create/Edit nutrition plan
2. Enter: Protein 150%, Carbs 35%, Fat 25%
3. Expected: ✗ Error "must be between 0 and 100"

### Test Case 5: Missing Field
1. Create/Edit nutrition plan
2. Enter: Protein 40%, Carbs 35%, Fat (empty)
3. Expected: ✗ Error "Please enter a valid percentage"

## Benefits

✅ **User Feedback:** Real-time validation as they type  
✅ **Error Prevention:** Catches issues before database attempt  
✅ **Data Integrity:** Database constraints prevent invalid data  
✅ **Clear Messages:** Shows actual vs required values  
✅ **Backward Compatible:** Old plans continue to work  
✅ **Multi-Layer:** Protection at all levels  

## Files Modified

- `app/controllers/Nutrition.php` - Enhanced error messages
- `app/views/trainer/edit_nutrition.php` - Better client-side feedback
- `app/views/trainer/create_nutrition.php` - Better client-side feedback
- `add_macro_validation.sql` - NEW: Database constraints

## Tolerance Level

The system uses a tolerance of `0.01` for floating-point comparison to handle rounding issues:
- 99.99% is acceptable (within tolerance of 100)
- 100.01% is acceptable (within tolerance of 100)
- 99.98% would be rejected
- 100.02% would be rejected

This tolerance is consistent across JavaScript, PHP, and SQL validation.
