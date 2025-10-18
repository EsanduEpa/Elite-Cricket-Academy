<?php
/**
 * Common Calendar Component
 * Reusable calendar component for all dashboards
 * 
 * Usage:
 * include APPROOT . '/views/inc/components/calendar.php';
 * 
 * Optional variables to set before including:
 * $calendarId - ID for the calendar container (default: 'eventCalendar')
 * $calendarTitle - Title for the calendar section (default: 'Calendar')
 * $calendarIcon - Icon for the calendar title (default: 'fas fa-calendar')
 * $showControls - Whether to show navigation controls (default: true)
 * $calendarClass - Additional CSS classes for the calendar section
 */

// Set default values if not provided
$calendarId = isset($calendarId) ? $calendarId : 'eventCalendar';
$calendarTitle = isset($calendarTitle) ? $calendarTitle : 'Calendar';
$calendarIcon = isset($calendarIcon) ? $calendarIcon : 'fas fa-calendar';
$showControls = isset($showControls) ? $showControls : true;
$calendarClass = isset($calendarClass) ? $calendarClass : '';
?>

<!-- Calendar Component -->
<div class="calendar-section <?php echo $calendarClass; ?>">
    <div class="section-header">
        <h2><i class="<?php echo $calendarIcon; ?>"></i> <?php echo $calendarTitle; ?></h2>
        <?php if ($showControls): ?>
        <div class="calendar-controls">
            <button class="btn-icon" onclick="<?php echo $calendarId; ?>.prev()">
                <i class="fas fa-chevron-left"></i>
            </button>
            <span class="current-month" id="currentMonth-<?php echo $calendarId; ?>"></span>
            <button class="btn-icon" onclick="<?php echo $calendarId; ?>.next()">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="btn-icon" onclick="showCalendarView_<?php echo $calendarId; ?>()" title="Calendar View">
                <i class="fas fa-calendar-week"></i>
            </button>
            <button class="btn-icon" onclick="showListView_<?php echo $calendarId; ?>()" title="List View">
                <i class="fas fa-list"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="calendar-container">
        <!-- Loading indicator -->
        <div id="calendarLoading" style="display: none; text-align: center; padding: 2rem;">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading calendar...</span>
            </div>
        </div>
        
        <!-- Calendar will be rendered here -->
        <div id="<?php echo $calendarId; ?>"></div>
    </div>
</div>

<!-- Required CSS and JS (include only once per page) -->
<?php if (!isset($calendar_assets_loaded)): ?>
<?php $calendar_assets_loaded = true; ?>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<!-- Simple Calendar Initialization -->
<script>
// Wait for both DOM and FullCalendar to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure FullCalendar library is loaded
    setTimeout(function() {
        initializeCommonCalendar('<?php echo $calendarId; ?>');
    }, 100);
});

function initializeCommonCalendar(calendarId) {
    console.log('Initializing calendar:', calendarId);
    
    // Check if FullCalendar is available
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar is not loaded');
        return;
    }
    
    const calendarEl = document.getElementById(calendarId);
    if (!calendarEl) {
        console.error('Calendar element not found:', calendarId);
        return;
    }
    
    // Hide loading indicator
    const loading = document.getElementById('calendarLoading');
    if (loading) {
        loading.style.display = 'none';
    }
    
    try {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false, // Disable default header to use custom controls
            height: 'auto',
            aspectRatio: 1.6,
            events: [
                {
                    title: 'Training Session',
                    start: new Date().toISOString().split('T')[0] + 'T10:00:00',
                    backgroundColor: '#4A90E2',
                    borderColor: '#4A90E2'
                },
                {
                    title: 'Team Practice', 
                    start: new Date(Date.now() + 2*24*60*60*1000).toISOString().split('T')[0] + 'T14:00:00',
                    backgroundColor: '#5BA0F2',
                    borderColor: '#5BA0F2'
                },
                {
                    title: 'Match Day',
                    start: new Date(Date.now() + 5*24*60*60*1000).toISOString().split('T')[0] + 'T09:00:00',
                    backgroundColor: '#FF8A50',
                    borderColor: '#FF8A50'
                },
                {
                    title: 'Equipment Check',
                    start: new Date(Date.now() + 7*24*60*60*1000).toISOString().split('T')[0] + 'T16:00:00',
                    backgroundColor: '#4ECDC4',
                    borderColor: '#4ECDC4'
                }
            ],
            eventClick: function(info) {
                showEventDetails(info.event);
            },
            datesSet: function(dateInfo) {
                // Update the current month display
                const monthEl = document.getElementById('currentMonth-<?php echo $calendarId; ?>');
                if (monthEl) {
                    const options = { year: 'numeric', month: 'long' };
                    monthEl.textContent = dateInfo.start.toLocaleDateString('en-US', options);
                }
            }
        });
        
        calendar.render();
        
        // Store calendar reference globally for controls
        window['<?php echo $calendarId; ?>'] = calendar;
        
        // Initialize current month display
        const monthEl = document.getElementById('currentMonth-<?php echo $calendarId; ?>');
        if (monthEl) {
            const currentDate = new Date();
            const options = { year: 'numeric', month: 'long' };
            monthEl.textContent = currentDate.toLocaleDateString('en-US', options);
        }
        
        console.log('Calendar rendered successfully');
        
    } catch (error) {
        console.error('Error initializing calendar:', error);
        calendarEl.innerHTML = '<div style="padding: 2rem; text-align: center; color: #666;">Error loading calendar</div>';
    }
}

// Calendar view functions
function showCalendarView_<?php echo $calendarId; ?>() {
    const calendar = window['<?php echo $calendarId; ?>'];
    if (calendar) {
        calendar.changeView('dayGridMonth');
    }
}

function showListView_<?php echo $calendarId; ?>() {
    const calendar = window['<?php echo $calendarId; ?>'];
    if (calendar) {
        calendar.changeView('listWeek');
    }
}

// Event details function
function showEventDetails(event) {
    const eventDetails = `
        <div style="padding: 1rem;">
            <h3 style="margin: 0 0 1rem 0; color: #2c3e50;">${event.title}</h3>
            <p style="margin: 0.5rem 0; color: #5a6c7d;"><strong>Date:</strong> ${event.start.toLocaleDateString()}</p>
            <p style="margin: 0.5rem 0; color: #5a6c7d;"><strong>Time:</strong> ${event.start.toLocaleTimeString()}</p>
        </div>
    `;
    
    // Create a simple modal or alert
    if (confirm(`Event Details:\n\nTitle: ${event.title}\nDate: ${event.start.toLocaleDateString()}\nTime: ${event.start.toLocaleTimeString()}\n\nClick OK to close.`)) {
        // User clicked OK
    }
}
}
</script>

<?php endif; ?>

<style>
/* Loading spinner styles */
.loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    color: #4A90E2;
    font-weight: 500;
}

.loading-spinner i {
    font-size: 2rem;
    color: #4A90E2;
}

/* Ensure calendar container has proper height */
#<?php echo $calendarId; ?> {
    min-height: 400px;
}
</style>