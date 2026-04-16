(function () {
    const endpoint = window.ELITE_REMINDER_HEARTBEAT_URL;

    if (!endpoint || !window.fetch) {
        return;
    }

    const intervalMs = 60 * 60 * 1000;
    let running = false;

    async function runReminderHeartbeat() {
        if (running || document.hidden) {
            return;
        }

        running = true;
        try {
            await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
        } catch (error) {
            // Keep this silent for users; PHP logs server-side failures.
            console.warn('Reminder heartbeat failed', error);
        } finally {
            running = false;
        }
    }

    window.addEventListener('load', function () {
        runReminderHeartbeat();
        window.setInterval(runReminderHeartbeat, intervalMs);
    });
})();
