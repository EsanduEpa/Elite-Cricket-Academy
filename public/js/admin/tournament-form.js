(function () {
    const form        = document.querySelector('.tourn-form');
    const tdateEl     = document.getElementById('tdate');
    const regEl       = document.getElementById('registration_deadline');
    const hint        = document.getElementById('reg-deadline-hint');
    const isCreate    = form && form.dataset.mode === 'create';
    const regGapDays  = form ? parseInt(form.dataset.regGap || '30', 10) : 30;
    const originalDate = form ? (form.dataset.originalTdate || '') : '';

    if (!form || !tdateEl || !regEl) return;

    /* ── helpers ── */

    function toDateStr(d) {
        return d.toISOString().split('T')[0];
    }

    function today() {
        return toDateStr(new Date());
    }

    /** Returns YYYY-MM-DD that is `days` days before `dateStr` */
    function subtractDays(dateStr, days) {
        const d = new Date(dateStr);
        d.setDate(d.getDate() - days);
        return toDateStr(d);
    }

    /** Returns YYYY-MM-DD that is `days` days after `dateStr` */
    function addDays(dateStr, days) {
        const d = new Date(dateStr);
        d.setDate(d.getDate() + days);
        return toDateStr(d);
    }

    function fmt(dateStr) {
        if (!dateStr) return '';
        const [y, m, day] = dateStr.split('-');
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return `${parseInt(day)} ${months[parseInt(m) - 1]} ${y}`;
    }

    function tdateChanged() {
        return tdateEl.value !== originalDate;
    }

    /* ── constraint updater ── */

    function updateConstraints() {
        const todayStr = today();

        /* Tournament date min:
           - create: cannot be in the past (must be > regGapDays away so a deadline exists)
           - edit: only enforce if admin changes the date */
        if (isCreate) {
            tdateEl.min = addDays(todayStr, regGapDays + 1);
        }

        const tdate = tdateEl.value;

        if (!tdate) {
            regEl.removeAttribute('min');
            regEl.removeAttribute('max');
            if (hint) hint.textContent = 'Set a tournament date first.';
            return;
        }

        const maxReg = subtractDays(tdate, regGapDays);

        /* Registration deadline: today ≤ deadline ≤ tdate − regGapDays */
        regEl.min = todayStr;
        regEl.max = maxReg;

        /* If existing value now exceeds the max, clear it */
        if (regEl.value && regEl.value > maxReg) {
            regEl.value = '';
        }

        /* Hint text */
        if (hint) {
            if (maxReg < todayStr) {
                hint.textContent = `⚠ Tournament date is too soon — must be at least ${regGapDays} days away for a valid registration window.`;
                hint.style.color = '#ef4444';
            } else {
                hint.textContent = `Must be on or before ${fmt(maxReg)} (${regGapDays} days before tournament).`;
                hint.style.color = '#64748b';
            }
        }
    }

    /* ── live validation on reg field ── */

    function validateReg() {
        const tdate   = tdateEl.value;
        const regDate = regEl.value;
        const todayStr = today();

        if (!regDate) {
            regEl.setCustomValidity('');
            return;
        }

        if (!tdate) {
            regEl.setCustomValidity('Please set the tournament date first.');
            return;
        }

        const maxReg = subtractDays(tdate, regGapDays);

        if (regDate < todayStr) {
            regEl.setCustomValidity('Registration deadline cannot be in the past.');
        } else if (regDate > maxReg) {
            regEl.setCustomValidity(`Registration must close by ${fmt(maxReg)} — at least ${regGapDays} days before the tournament.`);
        } else {
            regEl.setCustomValidity('');
        }
    }

    /* ── live validation on tdate field ── */

    function validateTdate() {
        const tdate    = tdateEl.value;
        const todayStr = today();

        if (!tdate) {
            tdateEl.setCustomValidity('');
            return;
        }

        /* On edit, only enforce if the date was changed */
        if (!isCreate && !tdateChanged()) {
            tdateEl.setCustomValidity('');
            return;
        }

        const minAllowed = addDays(todayStr, regGapDays + 1);

        if (tdate <= todayStr) {
            tdateEl.setCustomValidity('Tournament date cannot be in the past.');
        } else if (tdate < minAllowed) {
            tdateEl.setCustomValidity(`Tournament date must be at least ${regGapDays} days from today so a registration window exists.`);
        } else {
            tdateEl.setCustomValidity('');
        }
    }

    /* ── form submit guard ── */

    form.addEventListener('submit', function (e) {
        validateTdate();
        validateReg();

        const tdate    = tdateEl.value;
        const regDate  = regEl.value;
        const todayStr = today();

        /* Block if tdate invalid (create always; edit only when changed) */
        if (tdate && (isCreate || tdateChanged())) {
            const minAllowed = addDays(todayStr, regGapDays + 1);
            if (tdate <= todayStr || tdate < minAllowed) {
                tdateEl.reportValidity();
                e.preventDefault();
                return;
            }
        }

        /* Block if reg deadline out of range */
        if (regDate && tdate) {
            const maxReg = subtractDays(tdate, regGapDays);
            if (regDate < todayStr || regDate > maxReg) {
                regEl.reportValidity();
                e.preventDefault();
            }
        }
    });

    /* ── wire up events ── */

    tdateEl.addEventListener('change', function () {
        updateConstraints();
        validateTdate();
        if (regEl.value) validateReg();
    });

    regEl.addEventListener('change', validateReg);
    regEl.addEventListener('input',  validateReg);

    /* ── init on page load ── */
    updateConstraints();
    if (regEl.value) validateReg();
})();
