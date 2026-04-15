# Changes Made to Fix Nutrition Plan Update

## File 1: app/models/M_NutritionPlan.php

### Change Location: Lines 635-651
### Method: `replaceAssignedPlayers()`

#### BEFORE (Broken):
```php
private function replaceAssignedPlayers($planId, array $playerIds, $assignedDate = null): bool {
    $playerIds = array_values(array_unique(array_filter(array_map('intval', $playerIds), static fn($id) => $id > 0)));
    if (empty($playerIds)) {
        return false;
    }

    $this->db->query('DELETE FROM nutritionplan_player WHERE PlanID = :plan_id');
    $this->db->bind(':plan_id', (int)$planId);
    if (!$this->db->execute()) {
        return false;
    }

    return $this->assignPlayersToPlan($planId, $playerIds, $assignedDate);
}
```

#### AFTER (Fixed):
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
        return true;
    }

    return $this->assignPlayersToPlan($planId, $playerIds, $assignedDate);
}
```

#### Changes:
1. Moved DELETE logic before the early return (line 638-643)
2. Changed `return false;` to `return true;` when playerIds is empty (line 647)
3. Added explanatory comments (lines 638, 645)

#### Why:
- Cleanup (DELETE) now happens regardless of whether there are new players to assign
- Empty playerIds is now treated as success (plans can exist without players)
- This prevents cascading failure in updatePlan()

---

## File 2: app/views/trainer/edit_nutrition.php

### Change Location: Lines 14-27
### Function: `$val()` helper function

#### BEFORE (Broken):
```php
// Helper: return old (post) value if available, otherwise DB value
$val = function(string $k, string $dbCol = '') use ($old, $plan) {
    if (isset($old[$k])) return htmlspecialchars($old[$k], ENT_QUOTES);
    $col = $dbCol ?: $k;
    return htmlspecialchars($plan->$col ?? '', ENT_QUOTES);
};
```

#### AFTER (Fixed):
```php
// Helper: return old (post) value if available, otherwise DB value
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

#### Changes:
1. Line 17: Changed from `$col = $dbCol ?: $k;` to `$col = $dbCol;`
2. Lines 18-24: Added conditional logic to handle plan_name mapping

#### Why:
- `plan_name` form field doesn't directly match database column names (PlanName or nutritionPlanName)
- This special case logic ensures the correct column is accessed
- Fallback to nutritionPlanName for backward compatibility with older records

---

## Form Field Usage - No Changes

### This code didn't change (already correct):

```php
// Line 249 - Protein field (already correct)
value="<?php echo $val('protein_percentage', 'ProteinPercentage'); ?>"

// Line 259 - Carbohydrate field (already correct)
value="<?php echo $val('carbohydrate_percentage', 'CarbohydratePercentage'); ?>"

// Line 269 - Fat field (already correct)  
value="<?php echo $val('fat_percentage', 'FatPercentage'); ?>"

// Line 283 - Calories field (already correct)
value="<?php echo $val('recommended_calories', 'RecommendedCalories'); ?>"

// Line 293 - Description field (already correct)
<?php echo $val('description', 'Description'); ?>

// Line 408 - Duration field (already correct)
value="<?php echo $val('duration', 'Duration'); ?>"

// Line 204 - Plan name field (now uses the fixed helper)
value="<?php echo $val('plan_name'); ?>"
```

---

## Testing the Changes

### Verify File 1 Fix:
1. Create or edit a nutrition plan
2. Assign it to one player
3. Re-edit and change macros
4. Save - should work without failing

### Verify File 2 Fix:
1. Edit any existing nutrition plan
2. Look at the "Plan Name" field
3. It should show the current plan name (not empty)

---

## Backward Compatibility

✅ **No breaking changes**
✅ **Supports old and new column naming**
✅ **Works with existing data**
✅ **No database migrations required**

---

## Git Diff (Summary)

```diff
=== File: app/models/M_NutritionPlan.php ===

- if (empty($playerIds)) {
-     return false;
- }

  $this->db->query('DELETE FROM nutritionplan_player WHERE PlanID = :plan_id');
  $this->db->bind(':plan_id', (int)$planId);
  if (!$this->db->execute()) {
      return false;
  }

+ if (empty($playerIds)) {
+     return true;
+ }

=== File: app/views/trainer/edit_nutrition.php ===

- $col = $dbCol ?: $k;
+ $col = $dbCol;
+ if (!$col) {
+     if ($k === 'plan_name') {
+         $col = isset($plan->PlanName) ? 'PlanName' : 'nutritionPlanName';
+     } else {
+         $col = $k;
+     }
+ }
```

---

## Summary

- **2 files changed**
- **~16 lines modified**
- **0 database changes**
- **100% backward compatible**
- **Fixes 2 critical bugs**
