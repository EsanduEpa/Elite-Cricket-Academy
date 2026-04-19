// Admin feedback page behaviour.
// PHP renders the table and stores endpoint URLs in data attributes; this file
// handles browser-only actions such as filtering, opening modals, and AJAX calls.

let currentFeedbackId = null;

function getFeedbackPageConfig() {
    const layout = document.querySelector('.admin-layout');

    return {
        updateUrl: layout?.dataset.feedbackUpdateUrl || '',
        deleteUrl: layout?.dataset.feedbackDeleteUrl || '',
        feedbackData: readFeedbackData()
    };
}

function readFeedbackData() {
    const dataNode = document.getElementById('feedbackData');
    if (!dataNode) return [];

    try {
        return JSON.parse(dataNode.textContent || '[]');
    } catch (error) {
        console.error('Unable to parse feedback data:', error);
        return [];
    }
}

function viewFeedback(feedbackId) {
    currentFeedbackId = feedbackId;
    const row = document.querySelector(`tr[data-feedback-id="${feedbackId}"]`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    const userName = cells[0]?.querySelector('strong')?.textContent || 'Unknown';
    const category = row.querySelector('.category-badge')?.textContent.trim() || 'General';
    const status = row.querySelector('.status-badge')?.textContent.trim() || 'Pending';
    const date = row.querySelector('.date-cell small')?.textContent || '';

    setText('modalUserName', userName);
    setText('modalUserEmail', '');
    setText('modalDate', date);
    setText('modalCategory', category);
    setText('modalPriority', row.getAttribute('data-priority') || 'Normal');
    setText('modalStatus', status);
    setText('modalFeedbackId', `#${feedbackId}`);
    setText('modalSubject', category);

    const feedback = getFeedbackPageConfig().feedbackData.find(item => item.id == feedbackId);
    if (feedback) {
        setText('modalMessage', feedback.message);
    }

    // Responses are not stored in the current database table, so the section is hidden.
    const responseSection = document.getElementById('responseSection');
    if (responseSection) {
        responseSection.style.display = 'none';
    }

    document.getElementById('feedbackDetailModal')?.classList.add('active');
}

function respondFeedback(feedbackId) {
    viewFeedback(feedbackId);
    document.getElementById('responseText')?.focus();
}

function closeFeedbackModal() {
    document.getElementById('feedbackDetailModal')?.classList.remove('active');
    currentFeedbackId = null;
}

function updateFeedbackStatus(status) {
    if (!currentFeedbackId) return;

    const { updateUrl } = getFeedbackPageConfig();
    const response = document.getElementById('responseText')?.value || '';

    if (!updateUrl) {
        alert('Feedback update endpoint is not configured.');
        return;
    }

    if (confirm(`Are you sure you want to mark this feedback as ${status}?`)) {
        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `feedback_id=${encodeURIComponent(currentFeedbackId)}&status=${encodeURIComponent(status)}&response=${encodeURIComponent(response)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Feedback status updated successfully!');
                    closeFeedbackModal();
                    location.reload();
                    return;
                }

                alert('Error updating feedback: ' + (data.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Feedback update failed:', error);
                alert('Network error updating feedback');
            });
    }
}

function deleteFeedback(feedbackId) {
    const { deleteUrl } = getFeedbackPageConfig();

    if (!deleteUrl) {
        alert('Feedback delete endpoint is not configured.');
        return;
    }

    if (confirm('Are you sure you want to delete this feedback? This action cannot be undone.')) {
        fetch(deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `feedback_id=${encodeURIComponent(feedbackId)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Feedback deleted successfully!');
                    location.reload();
                    return;
                }

                alert('Error deleting feedback: ' + (data.message || 'Unknown error'));
            })
            .catch(error => {
                console.error('Feedback delete failed:', error);
                alert('Network error deleting feedback');
            });
    }
}

function deleteFeedbackFromModal() {
    if (currentFeedbackId) {
        deleteFeedback(currentFeedbackId);
    }
}

function filterFeedback(status) {
    const activeTab = document.querySelector(`.filter-tab[data-status="${status}"]`);
    document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
    if (activeTab) activeTab.classList.add('active');
    applyFeedbackFilters();
}

function searchFeedback(query) {
    const searchInput = document.getElementById('feedbackSearch');
    if (searchInput) searchInput.value = query;
    applyFeedbackFilters();
}

function applyFilters() {
    applyFeedbackFilters();
}

function applyFeedbackFilters() {
    const activeStatusTab = document.querySelector('.filter-tab.active');
    const status = activeStatusTab ? activeStatusTab.getAttribute('data-status') : 'all';
    const searchQuery = (document.getElementById('feedbackSearch')?.value || '').trim().toLowerCase();
    const category = document.getElementById('categoryFilter')?.value || 'all';

    document.querySelectorAll('.feedback-row').forEach(row => {
        const rowStatus = row.getAttribute('data-status') || '';
        const rowCategory = row.getAttribute('data-category') || 'general';
        const rowText = row.textContent.toLowerCase();
        const matchesStatus = status === 'all' || rowStatus === status;
        const matchesCategory = category === 'all' || rowCategory === category;
        const matchesSearch = !searchQuery || rowText.includes(searchQuery);

        row.style.display = matchesStatus && matchesCategory && matchesSearch ? '' : 'none';
    });
}

function setText(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const searchInput = document.getElementById('feedbackSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const clearBtn = document.getElementById('clearFiltersBtn');

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(item => item.classList.remove('active'));
            this.classList.add('active');
            applyFeedbackFilters();
        });
    });

    searchInput?.addEventListener('input', applyFeedbackFilters);
    categoryFilter?.addEventListener('change', applyFeedbackFilters);

    clearBtn?.addEventListener('click', function() {
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = 'all';
        filterTabs.forEach(tab => tab.classList.remove('active'));
        filterTabs[0]?.classList.add('active');
        applyFeedbackFilters();
    });
});

window.addEventListener('click', function(event) {
    const modal = document.getElementById('feedbackDetailModal');
    if (event.target === modal) {
        closeFeedbackModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeFeedbackModal();
    }
});
