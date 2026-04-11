document.addEventListener('DOMContentLoaded', function () {
    const calendarElement = document.getElementById('player-calendar');
    const popover = document.getElementById('cal-popover');
    const popoverContent = document.getElementById('cal-popover-content');
    const popoverClose = document.getElementById('cal-popover-close');

    if (!calendarElement || typeof FullCalendar === 'undefined') {
        return;
    }

    let calendarEvents = [];

    try {
        calendarEvents = JSON.parse(calendarElement.dataset.events || '[]');
    } catch (error) {
        console.error('Failed to parse calendar events:', error);
    }

    const closePopover = function () {
        if (popover) {
            popover.style.display = 'none';
        }
    };

    if (popoverClose) {
        popoverClose.addEventListener('click', closePopover);
    }

    const calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: calendarEvents,
        eventClick: function (info) {
            if (!popover || !popoverContent) {
                return;
            }

            const event = info.event;
            const props = event.extendedProps || {};
            const start = event.start ? event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            const end = event.end ? event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

            popoverContent.innerHTML = `
                <h4 class="calendar-popover-title">${event.title}</h4>
                <p class="calendar-popover-time"><i class="fas fa-clock"></i> ${start}${end ? ' - ' + end : ''}</p>
                <p class="calendar-popover-meta"><span class="calendar-popover-type" style="background:${event.backgroundColor};">${props.type || ''}</span><span class="calendar-popover-status">${props.status || ''}</span></p>
            `;

            const rect = info.el.getBoundingClientRect();
            popover.style.display = 'block';
            popover.style.top = Math.min(rect.bottom + 8, window.innerHeight - 160) + 'px';
            popover.style.left = Math.min(rect.left, window.innerWidth - 340) + 'px';
        },
        eventDidMount: function (info) {
            info.el.title = info.event.title;
        },
        height: 'auto',
        nowIndicator: true,
    });

    calendar.render();

    document.addEventListener('click', function (event) {
        if (popover && popover.style.display === 'block' && !popover.contains(event.target)) {
            closePopover();
        }
    });
});
