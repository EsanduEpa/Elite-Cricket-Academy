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
                // Today
                {
                    title: 'Youth Training Session',
                    start: new Date().toISOString().split('T')[0] + 'T10:00:00',
                    backgroundColor: '#667eea',
                    borderColor: '#667eea',
                    extendedProps: {
                        description: 'Morning training for youth team',
                        location: 'Ground A'
                    }
                },
                {
                    title: 'Coach Meeting',
                    start: new Date().toISOString().split('T')[0] + 'T15:00:00',
                    backgroundColor: '#764ba2',
                    borderColor: '#764ba2',
                    extendedProps: {
                        description: 'Monthly coaches coordination meeting',
                        location: 'Admin Office'
                    }
                },
                // Tomorrow
                {
                    title: 'New Player Registration',
                    start: new Date(Date.now() + 1*24*60*60*1000).toISOString().split('T')[0] + 'T09:00:00',
                    backgroundColor: '#10b981',
                    borderColor: '#10b981',
                    extendedProps: {
                        description: 'Registration for new academy members',
                        location: 'Reception'
                    }
                },
                // Day 2
                {
                    title: 'Senior Team Practice', 
                    start: new Date(Date.now() + 2*24*60*60*1000).toISOString().split('T')[0] + 'T14:00:00',
                    backgroundColor: '#3b82f6',
                    borderColor: '#3b82f6',
                    extendedProps: {
                        description: 'Regular practice session',
                        location: 'Main Ground'
                    }
                },
                {
                    title: 'Equipment Maintenance',
                    start: new Date(Date.now() + 2*24*60*60*1000).toISOString().split('T')[0] + 'T11:00:00',
                    backgroundColor: '#8b5cf6',
                    borderColor: '#8b5cf6',
                    extendedProps: {
                        description: 'Scheduled equipment check',
                        location: 'Equipment Room'
                    }
                },
                // Day 3
                {
                    title: 'Junior Championship Qualifier',
                    start: new Date(Date.now() + 3*24*60*60*1000).toISOString().split('T')[0] + 'T08:00:00',
                    end: new Date(Date.now() + 3*24*60*60*1000).toISOString().split('T')[0] + 'T17:00:00',
                    backgroundColor: '#ef4444',
                    borderColor: '#ef4444',
                    extendedProps: {
                        description: 'Tournament qualifying round',
                        location: 'Ground A & B'
                    }
                },
                // Day 4
                {
                    title: 'Finance Review Meeting',
                    start: new Date(Date.now() + 4*24*60*60*1000).toISOString().split('T')[0] + 'T10:00:00',
                    backgroundColor: '#f59e0b',
                    borderColor: '#f59e0b',
                    extendedProps: {
                        description: 'Quarterly finance review',
                        location: 'Conference Room'
                    }
                },
                // Day 5
                {
                    title: 'Inter-Academy Match',
                    start: new Date(Date.now() + 5*24*60*60*1000).toISOString().split('T')[0] + 'T09:00:00',
                    backgroundColor: '#dc2626',
                    borderColor: '#dc2626',
                    extendedProps: {
                        description: 'Friendly match with City Academy',
                        location: 'Main Ground'
                    }
                },
                {
                    title: 'Parent-Coach Meeting',
                    start: new Date(Date.now() + 5*24*60*60*1000).toISOString().split('T')[0] + 'T16:00:00',
                    backgroundColor: '#06b6d4',
                    borderColor: '#06b6d4',
                    extendedProps: {
                        description: 'Monthly parent-coach discussion',
                        location: 'Hall'
                    }
                },
                // Day 7
                {
                    title: 'Facility Inspection',
                    start: new Date(Date.now() + 7*24*60*60*1000).toISOString().split('T')[0] + 'T11:00:00',
                    backgroundColor: '#14b8a6',
                    borderColor: '#14b8a6',
                    extendedProps: {
                        description: 'Routine facility safety check',
                        location: 'All Grounds'
                    }
                },
                // Day 8
                {
                    title: 'Skills Development Workshop',
                    start: new Date(Date.now() + 8*24*60*60*1000).toISOString().split('T')[0] + 'T13:00:00',
                    backgroundColor: '#8b5cf6',
                    borderColor: '#8b5cf6',
                    extendedProps: {
                        description: 'Advanced batting techniques',
                        location: 'Indoor Nets'
                    }
                },
                // Day 10
                {
                    title: 'Staff Meeting',
                    start: new Date(Date.now() + 10*24*60*60*1000).toISOString().split('T')[0] + 'T09:00:00',
                    backgroundColor: '#7c3aed',
                    borderColor: '#7c3aed',
                    extendedProps: {
                        description: 'All staff monthly meeting',
                        location: 'Conference Room'
                    }
                },
                // Day 12
                {
                    title: 'Medical Camp',
                    start: new Date(Date.now() + 12*24*60*60*1000).toISOString().split('T')[0] + 'T08:00:00',
                    end: new Date(Date.now() + 12*24*60*60*1000).toISOString().split('T')[0] + 'T14:00:00',
                    backgroundColor: '#ec4899',
                    borderColor: '#ec4899',
                    extendedProps: {
                        description: 'Free medical checkup for all players',
                        location: 'Medical Room'
                    }
                },
                // Day 15
                {
                    title: 'Tournament Finals',
                    start: new Date(Date.now() + 15*24*60*60*1000).toISOString().split('T')[0] + 'T10:00:00',
                    backgroundColor: '#dc2626',
                    borderColor: '#dc2626',
                    extendedProps: {
                        description: 'Junior Championship Finals',
                        location: 'Main Ground'
                    }
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
    const startDate = event.start.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    const startTime = event.start.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    let details = `Event: ${event.title}\n`;
    details += `Date: ${startDate}\n`;
    details += `Time: ${startTime}\n`;
    
    if (event.extendedProps && event.extendedProps.description) {
        details += `\nDescription: ${event.extendedProps.description}\n`;
    }
    
    if (event.extendedProps && event.extendedProps.location) {
        details += `Location: ${event.extendedProps.location}\n`;
    }
    
    if (event.end) {
        const endTime = event.end.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        details += `End Time: ${endTime}\n`;
    }
    
    alert(details);
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