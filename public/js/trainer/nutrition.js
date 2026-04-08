// Trainer Nutrition page JavaScript
// Extracted from app/views/trainer/nutrition.php

(() => {
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize search functionality
    initializeSearch();

    // Initialize filter functionality
    initializeFilters();

    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('trainerSidebar');
    const mainContent = document.getElementById('mainContent');

    if (sidebarToggle && sidebar && mainContent) {
      sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
      });
    }
  });

  // Search Functionality
  function initializeSearch() {
    const searchInput = document.getElementById('nutritionSearch');
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#nutritionTable tbody .nutrition-row');

        tableRows.forEach((row) => {
          const playerName = row.querySelector('.player-name')?.textContent.toLowerCase() || '';
          const playerMeta = row.querySelector('.player-email')?.textContent.toLowerCase() || '';
          const planText =
            row.querySelector('.nc-plan-name')?.textContent.toLowerCase() ||
            row.querySelector('.plan-text strong')?.textContent.toLowerCase() ||
            '';
          const notesText = row.querySelector('.nc-notes-preview')?.textContent.toLowerCase() || '';

          const matches =
            playerName.includes(searchTerm) ||
            playerMeta.includes(searchTerm) ||
            notesText.includes(searchTerm) ||
            planText.includes(searchTerm);

          row.style.display = matches ? '' : 'none';
        });

        updateEmptyState();
      });
    }
  }

  // Filter Functionality
  function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
      statusFilter.addEventListener('change', function () {
        filterNutritionPlans(this.value);
      });
    }
  }

  function filterNutritionPlans(filter) {
    const tableRows = document.querySelectorAll('#nutritionTable tbody .nutrition-row');

    tableRows.forEach((row) => {
      const status = row.getAttribute('data-status');

      if (filter === 'all') {
        row.style.display = '';
      } else {
        row.style.display = status === filter ? '' : 'none';
      }
    });

    updateEmptyState();
  }

  function updateEmptyState() {
    const visibleRows = document.querySelectorAll(
      '#nutritionTable tbody .nutrition-row:not([style*="display: none"])'
    );
    const emptyState = document.querySelector('.empty-state');

    if (visibleRows.length === 0 && !emptyState) {
      // Show no results message
      const tbody = document.querySelector('#nutritionTable tbody');
      if (!tbody) return;

      const noResultsRow = document.createElement('tr');
      noResultsRow.className = 'no-results';
      noResultsRow.innerHTML = `
          <td colspan="6">
                <div class="empty-content">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>No Plans Found</h3>
                    <p>No nutrition plans match your current search or filter criteria</p>
                </div>
            </td>
        `;
      tbody.appendChild(noResultsRow);
    } else if (visibleRows.length > 0) {
      // Remove no results message if it exists
      const noResults = document.querySelector('.no-results');
      if (noResults) {
        noResults.remove();
      }
    }
  }

})();
