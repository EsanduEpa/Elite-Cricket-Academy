// Reusable filters and pagination for admin table pages.
// Each list declares filters with data-list-filter and rows with data-filter-* attributes.

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-admin-list]').forEach(initializeAdminList);
});

function initializeAdminList(list) {
    const pageSize = parseInt(list.dataset.pageSize || '10', 10);
    const tbody = list.querySelector('[data-list-body]');
    const pagination = list.querySelector('[data-list-pagination]');
    const count = list.querySelector('[data-list-count]');
    const filters = Array.from(list.querySelectorAll('[data-list-filter]'));
    const reset = list.querySelector('[data-list-reset]');
    let currentPage = 1;

    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('[data-list-row]'));
    const emptyRow = list.querySelector('[data-list-empty-row]');

    function normalize(value) {
        return String(value || '').trim().toLowerCase();
    }

    function matchesFilters(row) {
        return filters.every(filter => {
            const key = filter.dataset.listFilter;
            const value = normalize(filter.value);
            if (!value || value === 'all') return true;

            if (key === 'search') {
                return normalize(row.dataset.search).includes(value);
            }

            return normalize(row.dataset[`filter${toDatasetKey(key)}`]) === value;
        });
    }

    function render() {
        const matched = rows.filter(matchesFilters);
        const totalPages = Math.max(1, Math.ceil(matched.length / pageSize));
        currentPage = Math.min(Math.max(currentPage, 1), totalPages);

        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const visible = matched.slice(start, end);

        rows.forEach(row => {
            row.style.display = visible.includes(row) ? '' : 'none';
        });

        if (emptyRow) {
            emptyRow.style.display = matched.length === 0 ? '' : 'none';
        }

        if (count) {
            const from = matched.length ? start + 1 : 0;
            const to = Math.min(end, matched.length);
            count.textContent = `Showing ${from}-${to} of ${matched.length}`;
        }

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!pagination) return;
        pagination.innerHTML = '';

        pagination.appendChild(createPageButton('prev', '<i class="fas fa-chevron-left"></i>', currentPage === 1, () => {
            currentPage--;
            render();
        }));

        for (let page = 1; page <= totalPages; page++) {
            if (page > 1 && page < totalPages && Math.abs(page - currentPage) > 1) {
                const side = page < currentPage ? 'left' : 'right';
                if (!pagination.querySelector(`[data-ellipsis="${side}"]`)) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'admin-list-page-ellipsis';
                    ellipsis.dataset.ellipsis = side;
                    ellipsis.textContent = '...';
                    pagination.appendChild(ellipsis);
                }
                continue;
            }

            pagination.appendChild(createPageButton(page, String(page), false, () => {
                currentPage = page;
                render();
            }, page === currentPage));
        }

        pagination.appendChild(createPageButton('next', '<i class="fas fa-chevron-right"></i>', currentPage === totalPages, () => {
            currentPage++;
            render();
        }));
    }

    function createPageButton(value, html, disabled, onClick, active = false) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = active ? 'admin-list-page-btn active' : 'admin-list-page-btn';
        button.dataset.page = value;
        button.innerHTML = html;
        button.disabled = disabled;
        button.addEventListener('click', onClick);
        return button;
    }

    filters.forEach(filter => {
        filter.addEventListener(filter.tagName === 'INPUT' ? 'input' : 'change', () => {
            currentPage = 1;
            render();
        });
    });

    reset?.addEventListener('click', () => {
        filters.forEach(filter => {
            filter.value = filter.tagName === 'INPUT' ? '' : 'all';
        });
        currentPage = 1;
        render();
    });

    render();
}

function toDatasetKey(key) {
    return String(key || '')
        .replace(/-([a-z])/g, (_, letter) => letter.toUpperCase())
        .replace(/^./, letter => letter.toUpperCase());
}
