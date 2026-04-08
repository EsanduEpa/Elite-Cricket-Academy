// Meal Plan Builder (vanilla JS)
// - Adds/removes meals and supplements
// - Keeps a hidden textarea (diet_details) populated with a readable summary

(() => {
  function qs(root, selector) {
    return root.querySelector(selector);
  }

  function qsa(root, selector) {
    return Array.from(root.querySelectorAll(selector));
  }

  function escapeText(value) {
    return String(value ?? '').replace(/[&<>"']/g, (ch) => {
      switch (ch) {
        case '&':
          return '&amp;';
        case '<':
          return '&lt;';
        case '>':
          return '&gt;';
        case '"':
          return '&quot;';
        case "'":
          return '&#39;';
        default:
          return ch;
      }
    });
  }

  function buildDietSummary(root) {
    const mealCards = qsa(root, '[data-meal-card]');

    const meals = mealCards
      .map((card, idx) => {
        const mealType = qs(card, 'select[name="meal_type[]"]')?.value || '';
        const time = qs(card, 'input[name="meal_time[]"]')?.value || '';
        const items = qs(card, 'textarea[name="food_items[]"]')?.value?.trim() || '';

        const calories = qs(card, 'input[name="calories[]"]')?.value || '';
        const protein = qs(card, 'input[name="protein_g[]"]')?.value || '';
        const carbs = qs(card, 'input[name="carbs_g[]"]')?.value || '';
        const fats = qs(card, 'input[name="fats_g[]"]')?.value || '';
        const notes = qs(card, 'input[name="meal_notes[]"]')?.value?.trim() || '';

        if (!mealType && !time && !items && !calories && !protein && !carbs && !fats && !notes) {
          return null;
        }

        const parts = [];
        parts.push(`${idx + 1}) ${mealType || 'Meal'}${time ? ` (${time})` : ''}`);
        if (items) parts.push(`Items: ${items}`);

        const macroParts = [];
        if (calories) macroParts.push(`${calories} kcal`);
        if (protein) macroParts.push(`P ${protein}g`);
        if (carbs) macroParts.push(`C ${carbs}g`);
        if (fats) macroParts.push(`F ${fats}g`);
        if (macroParts.length) parts.push(`Macros: ${macroParts.join(' | ')}`);

        if (notes) parts.push(`Notes: ${notes}`);

        return parts.join(' — ');
      })
      .filter(Boolean);

    const water = qs(root, 'input[name="water_intake_liters"]')?.value || '';
    const hydrationNotes = qs(root, 'input[name="hydration_notes"]')?.value?.trim() || '';

    const supplementRows = qsa(root, '[data-supp-row]');
    const supplements = supplementRows
      .map((row) => {
        const name = qs(row, 'input[name="supplement_name[]"]')?.value?.trim() || '';
        const dosage = qs(row, 'input[name="supplement_dosage[]"]')?.value?.trim() || '';
        const time = qs(row, 'input[name="supplement_time[]"]')?.value || '';
        if (!name && !dosage && !time) return null;
        return `${name || 'Supplement'}${dosage ? ` (${dosage})` : ''}${time ? ` at ${time}` : ''}`;
      })
      .filter(Boolean);

    const lines = [];

    if (meals.length) {
      lines.push('Meal Plan:');
      meals.forEach((m) => lines.push(`- ${m}`));
    }

    if (water || hydrationNotes) {
      lines.push('');
      lines.push('Hydration:');
      if (water) lines.push(`- Target: ${water} L/day`);
      if (hydrationNotes) lines.push(`- Notes: ${hydrationNotes}`);
    }

    if (supplements.length) {
      lines.push('');
      lines.push('Supplements:');
      supplements.forEach((s) => lines.push(`- ${s}`));
    }

    return lines.join('\n').trim();
  }

  function renumberMeals(root) {
    const cards = qsa(root, '[data-meal-card]');
    cards.forEach((card, i) => {
      const badge = qs(card, '[data-meal-number]');
      if (badge) badge.textContent = String(i + 1);
    });
  }

  function addMeal(root) {
    const tpl = qs(root, 'template[data-meal-template]');
    const container = qs(root, '[data-meals-container]');
    if (!tpl || !container) return;

    const fragment = tpl.content.cloneNode(true);
    container.appendChild(fragment);
    renumberMeals(root);
    syncDietDetails(root);
  }

  function addSupplement(root) {
    const tpl = qs(root, 'template[data-supp-template]');
    const container = qs(root, '[data-supp-container]');
    if (!tpl || !container) return;

    const fragment = tpl.content.cloneNode(true);
    container.appendChild(fragment);
    syncDietDetails(root);
  }

  function clearDietErrorShell(root) {
    const shell = qs(root, '[data-diet-shell]');
    if (!shell) return;
    shell.classList.remove('is-invalid');
  }

  function syncDietDetails(root) {
    const textareaId = root.getAttribute('data-textarea-id');
    if (!textareaId) return;

    const textarea = document.getElementById(textareaId);
    if (!textarea) return;

    const summary = buildDietSummary(root);

    // If this builder hasn't been used yet, don't clobber existing saved text.
    // This keeps backward compatibility with older plans that stored free-text DietDetails.
    const isDirty = root.dataset.mpbDirty === 'true';
    const hasExistingText = textarea.value.trim().length > 0;
    const hasBuilderText = summary.trim().length > 0;

    if (!isDirty && hasExistingText && !hasBuilderText) {
      return;
    }

    textarea.value = summary;

    // If the builder has content, clear any visual error state
    if (summary) {
      clearDietErrorShell(root);
    }
  }

  function initOne(root) {
    const addMealBtn = qs(root, '[data-add-meal]');
    const addSuppBtn = qs(root, '[data-add-supp]');

    root.dataset.mpbDirty = 'false';

    if (addMealBtn) {
      addMealBtn.addEventListener('click', () => {
        root.dataset.mpbDirty = 'true';
        addMeal(root);
      });
    }

    if (addSuppBtn) {
      addSuppBtn.addEventListener('click', () => {
        root.dataset.mpbDirty = 'true';
        addSupplement(root);
      });
    }

    // Remove handlers (event delegation)
    root.addEventListener('click', (e) => {
      const removeMealBtn = e.target.closest('[data-remove-meal]');
      if (removeMealBtn) {
        root.dataset.mpbDirty = 'true';
        const card = removeMealBtn.closest('[data-meal-card]');
        if (card) card.remove();
        renumberMeals(root);
        syncDietDetails(root);
        return;
      }

      const removeSuppBtn = e.target.closest('[data-remove-supp]');
      if (removeSuppBtn) {
        root.dataset.mpbDirty = 'true';
        const row = removeSuppBtn.closest('[data-supp-row]');
        if (row) row.remove();
        syncDietDetails(root);
      }
    });

    // Keep textarea in sync as user types
    root.addEventListener('input', () => {
      root.dataset.mpbDirty = 'true';
      syncDietDetails(root);
    });
    root.addEventListener('change', () => {
      root.dataset.mpbDirty = 'true';
      syncDietDetails(root);
    });

    // Ensure textarea is synced before any other submit handlers read it
    const textareaId = root.getAttribute('data-textarea-id');
    const textarea = textareaId ? document.getElementById(textareaId) : null;
    const form = textarea ? textarea.closest('form') : root.closest('form');
    if (form) {
      form.addEventListener(
        'submit',
        () => {
          syncDietDetails(root);
        },
        true // capture
      );
    }

    // Default meal (only when there's no pre-existing DietDetails text)
    const existingMeals = qsa(root, '[data-meal-card]').length;
    const hasExistingText = textarea ? textarea.value.trim().length > 0 : false;

    if (!hasExistingText) {
      if (existingMeals === 0) {
        addMeal(root);
      } else {
        renumberMeals(root);
        syncDietDetails(root);
      }
    } else {
      // Keep the saved text untouched until the trainer edits the builder.
      renumberMeals(root);
    }
  }

  function initAll() {
    const roots = Array.from(document.querySelectorAll('[data-meal-plan-builder]'));
    roots.forEach(initOne);
  }

  window.MealPlanBuilder = {
    initAll,
    initOne,
    syncDietDetails,
  };

  document.addEventListener('DOMContentLoaded', initAll);
})();
