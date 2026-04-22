# Nutrition Plan Update Fix - Testing Guide

## Prerequisites
- You have already applied the `add_nutrition_plan_templates.sql` migration
- You have existing nutrition plans in the database
- You have at least one player in the system

## Test Case 1: Verify Plan Name Displays in Edit Form

### Steps:
1. Go to Nutrition → Plans list
2. Click "Edit" on any existing plan
3. Look at the "Plan Name" field

### Expected Result:
✓ The Plan Name field should show the current plan name (not be empty)
✓ If empty before, this means the fix is working

### Troubleshooting:
- If still empty, check database for plan name in `PlanName` or `nutritionPlanName` columns

## Test Case 2: Edit and Save Macro Percentages

### Steps:
1. Open a nutrition plan for editing
2. Modify one or more macro percentages:
   - Change Protein % from 40 to 45
   - Change Carbohydrate % from 35 to 30
   - Change Fat % from 25 to 25 (keep valid total of 100)
3. Keep the Recommended Calories the same
4. Click "Save Plan"

### Expected Result:
✓ Page redirects to nutrition plans list
✓ Flash message shows "Plan updated successfully"
✓ When you re-open the plan to edit, the new values should be displayed

### Troubleshooting:
- If you get a validation error about "Protein, carbohydrate, and fat percentages must total 100%": Make sure the three percentages add up to exactly 100
- If you get "Update failed": Check server error logs for database connection issues

## Test Case 3: Edit and Save Calories

### Steps:
1. Open a nutrition plan for editing
2. Change "Recommended Calories" from 2400 to 2500
3. Leave macro percentages unchanged
4. Click "Save Plan"

### Expected Result:
✓ Page redirects to nutrition plans list
✓ Flash message shows "Plan updated successfully"
✓ When you re-open the plan, calories should show 2500

## Test Case 4: Edit Description

### Steps:
1. Open a nutrition plan for editing
2. Add or modify the "Description / Guidelines" field
3. Add some text like: "Updated: Higher carbs for training days"
4. Click "Save Plan"

### Expected Result:
✓ Page redirects and shows success message
✓ Description is saved and visible on re-edit

## Test Case 5: Update Without Changing Player Assignment

### Steps:
1. Open a nutrition plan for editing (already has players assigned)
2. Change ONLY the Plan Name (add " - Updated" to the end)
3. Leave player selection unchanged
4. Click "Save Plan"

### Expected Result:
✓ Plan saves successfully
✓ Same players remain assigned
✓ Plan name change is persisted

## Test Case 6: Update Existing Plan and Modify Player Assignment

### Steps:
1. Open a nutrition plan for editing with players assigned
2. Click "Select players" button
3. Uncheck one player and check another
4. Keep other fields unchanged
5. Click "Save Plan"

### Expected Result:
✓ Plan saves successfully
✓ New player assignment is applied
✓ Old players are removed from assignment

## Quick Smoke Test (All-in-One)

### Steps:
1. Go to Nutrition → Plans
2. Click Edit on any plan
3. Modify ALL of these fields:
   - Plan Name: Add " - Modified" to the end
   - Protein %: Change to 40
   - Carbohydrate %: Change to 40
   - Fat %: Change to 20 (must total 100)
   - Recommended Calories: Change to 2000
   - Description: Add "Smoke test update"
4. Click "Save Plan"

### Expected Result:
✓ Redirect to plans list with success message
✓ All changes are persisted when you re-open the plan

## Rollback Instructions (if needed)

If something goes wrong:
1. Restore your database backup
2. Report the specific error you encountered

## Success Criteria

The fix is working correctly if:
- ✓ Form fields display existing values
- ✓ Form accepts changes
- ✓ Changes persist after save
- ✓ No error messages appear unexpectedly
- ✓ Flash success message appears after save
- ✓ Redirects properly to plans list

## Known Limitations

- Player assignment requires at least one player to be selected (by design for individual mode)
- Group mode automatically expands to matching players
- Macro percentages must total 100% (by design)
- Calories must be between 500-10000 (by design)
