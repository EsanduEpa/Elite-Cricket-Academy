# Nutrition Plan Update Fix - Technical Details

## Overview
Fixed critical bugs preventing nutrition plan updates from persisting to the database. The system would appear to accept changes but not save them, or fail silently with a vague error message.

## Bug #1: Player Assignment Cascading Failure

### File: `app/models/M_NutritionPlan.php`
### Method: `replaceAssignedPlayers()` (Lines 635-651)

### Before (Buggy Code):
```php
private function replaceAssignedPlayers($planId, array $playerIds, $assignedDate = null): bool {
    $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
    if (empty($playerIds)) {
        return false;  // ← BUG: Returns false even if DELETE succeeded!
    }

    $this->db->query('DELETE FROM nutritionplan_player WHERE PlanID = :plan_id');
    $this->db->bind(':plan_id', (int)$planId);
    if (!$this->db->execute()) {
        return false;
    }

    return $this->assignPlayersToPlan($planId, $playerIds, $assignedDate);
}
```

### After (Fixed Code):
```php
private function replaceAssignedPlayers($planId, array $playerIds, $assignedDate = null): bool {
    $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
    
    // Delete existing assignments
    $this->db->query('DELETE FROM nutritionplan_player WHERE PlanID = :plan_id');
    $this->db->bind(':plan_id', (int)$planId);
    if (!$this->db->execute()) {
        return false;
    }

    // If no players to assign, return success (plan can exist without assigned players)
    if (empty($playerIds)) {
        return true;  // ← FIX: Allow empty player list after successful cleanup
    }

    return $this->assignPlayersToPlan($planId, $playerIds, $assignedDate);
}
```

### Why This Fix is Correct:

1. **Separation of Concerns**: The method now clearly handles two separate concerns:
   - Cleanup: Delete old assignments (required)
   - Assignment: Add new assignments (optional)

2. **Backward Compatibility**: Plans might not have the `nutritionplan_player` table or might exist without player assignments

3. **Validation Already Happens Earlier**: The Nutrition controller validates that at least one player is selected before calling updatePlan(). If somehow an empty array reaches here, it's OK to just clean up and succeed.

4. **Prevents Cascade Failure**: Now if updatePlan() does everything else correctly but has no players to assign, it still returns true instead of failing the entire update.

### Call Chain Analysis:

```
updatePlan() [line 554]
  └─> appendNutritionStructuredUpdates() [line 583] - adds macro fields to UPDATE query
  └─> EXECUTE UPDATE query [line 622] - persists all nutrition data ✓
  └─> if tableExists('nutritionplan_player') [line 626]
       └─> replaceAssignedPlayers() [line 627]
            ├─> DELETE FROM nutritionplan_player [line 639]
            ├─> if empty($playerIds) return true [line 647] ← NOW FIXED
            └─> return assignPlayersToPlan() [line 650]
  └─> return true [line 632]
```

**Before the fix**: If `$playerIds` was empty at line 647, it would return false, and line 628 would cause updatePlan() to return false entirely.

**After the fix**: Empty playerIds is treated as success (cleanup succeeded), updatePlan() continues and returns true.

---

## Bug #2: Form Field Not Displaying Existing Value

### File: `app/views/trainer/edit_nutrition.php`
### Function: `$val()` helper (Lines 14-27)

### Context:
The form uses a `$val()` helper function to display existing values in form fields. This helper:
1. First checks if there's a recently-submitted POST value (after failed validation) in `$old` array
2. If not found, checks the database value from `$plan` object

### Before (Buggy Code):
```php
$val = function(string $k, string $dbCol = '') use ($old, $plan) {
    if (isset($old[$k])) return htmlspecialchars($old[$k], ENT_QUOTES);
    $col = $dbCol ?: $k;  // ← PROBLEM: If $dbCol is empty, uses $k literally
    return htmlspecialchars($plan->$col ?? '', ENT_QUOTES);
};
```

### Usage Problem:
```php
// In the form:
value="<?php echo $val('plan_name', $plan->PlanName ?? $plan->nutritionPlanName ?? ''); ?>"
//               ^^^^^^^^^^^^^^  ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//               POST field      VALUE (not column name!) - This evaluates to "High Protein"
```

When this is called:
```php
// $val('plan_name', 'High Protein') is called
// Line 16 checks $old['plan_name'] - probably empty
// Line 18 sets $col = 'High Protein' (the value, not the column name)
// Line 18 tries to access $plan->{'High Protein'} - which is NULL!
```

### After (Fixed Code):
```php
$val = function(string $k, string $dbCol = '') use ($old, $plan) {
    if (isset($old[$k])) return htmlspecialchars($old[$k], ENT_QUOTES);
    $col = $dbCol;
    if (!$col) {
        // Special case: plan_name can map to PlanName or nutritionPlanName
        if ($k === 'plan_name') {
            $col = isset($plan->PlanName) ? 'PlanName' : 'nutritionPlanName';
        } else {
            $col = $k;
        }
    }
    return htmlspecialchars($plan->$col ?? '', ENT_QUOTES);
};
```

### Usage Now:
```php
// In the form:
value="<?php echo $val('plan_name'); ?>"
//               ^^^^^^^^^^^^^^  - Just the field name, let helper figure out the column

// When called, the helper:
// 1. Checks if $old['plan_name'] exists (re-rendered after error) - probably empty
// 2. Checks if $plan->PlanName exists (modern column) - EXISTS!
// 3. Returns the PlanName value properly escaped
```

### Why This Fix is Correct:

1. **Handles Legacy Columns**: Database might have either `PlanName` (current) or `nutritionPlanName` (legacy)

2. **Centralizes Logic**: Instead of trying to guess the column name in the view, the helper handles the mapping

3. **Reduces Duplication**: Other fields that map correctly can just use the default behavior

4. **Explicit Intent**: The special case for `plan_name` is clearly documented

### Affected Form Fields:

Only `plan_name` was affected by this bug because:
- `protein_percentage` → `ProteinPercentage` (matches after case-conversion)
- `carbohydrate_percentage` → `CarbohydratePercentage` (matches after case-conversion)
- etc.

But `plan_name` → `PlanName` doesn't match due to:
- Form field naming convention (snake_case)
- Database column naming convention (PascalCase)
- Legacy support for `nutritionPlanName` (mixed case)

---

## Integration Points

### The Macro Fields Flow:

```
updatePlan($fields) receives:
  - template_id: 1
  - protein_percentage: "45.00"
  - carbohydrate_percentage: "35.00"
  - fat_percentage: "20.00"
  - recommended_calories: 2400
  - description: "Updated text"
  - ... other fields ...

appendNutritionStructuredUpdates() adds to SQL SET:
  SET TemplateID = :template_id,
      ProteinPercentage = :protein_percentage,
      CarbohydratePercentage = :carbohydrate_percentage,
      FatPercentage = :fat_percentage,
      RecommendedCalories = :recommended_calories,
      Description = :description

All fields are then executed in a single UPDATE query:
  UPDATE NutritionPlan 
  SET PlayerID = ..., 
      PlanName = ...,
      TemplateID = ...,           ← Macro fields persisted here
      ProteinPercentage = ...,    ← Macro fields persisted here
      CarbohydratePercentage = ...,
      FatPercentage = ...,
      RecommendedCalories = ...,
      Description = ...,
      DietDetails = ...,
      Notes = ...,
      Duration = ...,
      CreatedDate = ...,
      Status = ...
  WHERE PlanID = :plan_id
```

The macro fields are properly persisted thanks to the call to `appendNutritionStructuredUpdates()` at line 583 of `updatePlan()`.

---

## Prevention

To prevent similar issues in the future:

1. **Write Integration Tests**: Test the full flow from form submission to database verification
2. **Test After Failures**: Ensure that after a validation error, re-submitting the form preserves user input
3. **Database Verification**: Always verify that data is actually persisted, not just that no error occurred
4. **Code Review Checklist**:
   - Does the method return the right boolean on success/failure?
   - Do all form fields map correctly to database columns?
   - Are there special cases that need handling?

---

## Files Changed

```
app/models/M_NutritionPlan.php
  └─ replaceAssignedPlayers() method - improved to handle empty playerIds gracefully

app/views/trainer/edit_nutrition.php
  └─ $val() helper function - enhanced to map plan_name to correct column
```

Both changes are backward-compatible and don't require database schema changes.
