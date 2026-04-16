/* Coach Dashboard JS: charts and interactivity using Chart.js */
document.addEventListener('DOMContentLoaded', function(){
	const data = window.__COACH_DASHBOARD_DATA || window.__STAFF_SLOTS_DATA || {};
	const endpoints = window.__COACH_DASHBOARD_ENDPOINTS || {};

	// Helper: populate selects
	function populatePlayerSelect() {
		const sel = document.getElementById('performancePlayerSelect');
		const teamSel = document.getElementById('attendanceTeamSelect');
		const players = (data.playerProfiles || []).map(p => ({id:p.id, name:p.name}));
		if (sel) {
			sel.innerHTML = '';
			const allOpt = document.createElement('option'); allOpt.value='all'; allOpt.text='All Players'; sel.appendChild(allOpt);
			players.forEach(p=>{
				const o = document.createElement('option'); o.value = p.id; o.text = p.name; sel.appendChild(o);
			});
		}
		if (teamSel) {
			teamSel.innerHTML = '';
			const attendanceData = data.attendanceData || data.attendanceChartData || {};
			const datasets = attendanceData.datasets || {};
			if (datasets.all) {
				const options = [
					{ value: 'all', text: 'All Sessions' },
					{ value: 'program', text: 'Program Sessions' },
					{ value: 'private', text: 'Private Sessions' },
				];
				options.forEach(({ value, text }) => {
					const option = document.createElement('option');
					option.value = value;
					option.text = text;
					teamSel.appendChild(option);
				});
			}
		}
	}

	// Build performance dataset from server data or fallback
	function buildPerformanceDataset(playerId){
		const perfData = window.__COACH_DASHBOARD_DATA?.performanceData || [];
		if(perfData.length > 0){
			const labels = perfData.map(d => d.label || d.date);
			const values = perfData.map(d => d.value || d.rating || 0);
			return {labels, values};
		}
		// Fallback: empty dataset
		return {labels: [], values: []};
	}

	// Charts
	let performanceChart = null;
	let attendanceChart = null;
	let healthChart = null;
	let performanceIsLine = true;
	let activeAttendanceSessionId = null;

	const attendanceModal = document.getElementById('attendanceModal');
	const attendanceSessionIdInput = document.getElementById('attendanceSessionId');
	const attendanceRosterList = document.getElementById('attendanceRosterList');
	const attendanceModalTitle = document.getElementById('attendanceModalTitle');
	const attendanceModalSubtitle = document.getElementById('attendanceModalSubtitle');
	const saveAttendanceBtn = document.getElementById('saveAttendanceBtn');

	function escapeHtml(value) {
		return String(value ?? '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}

	function openAttendanceModal(sessionId, sessionName) {
		if (!attendanceModal || !attendanceRosterList) {
			return;
		}

		activeAttendanceSessionId = sessionId;
		attendanceSessionIdInput.value = String(sessionId);
		attendanceModalTitle.textContent = sessionName || 'Eligible Players';
		attendanceModalSubtitle.textContent = 'Select the players who attended this session.';
		attendanceRosterList.innerHTML = '<div style="padding:18px;color:#666;font-size:13px;">Loading eligible players...</div>';
		attendanceModal.classList.add('is-open');
		attendanceModal.setAttribute('aria-hidden', 'false');

		fetch((endpoints.roster || '') + encodeURIComponent(sessionId), {
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(response => response.json())
			.then(payload => {
				if (!payload || !payload.success) {
					throw new Error(payload && payload.message ? payload.message : 'Failed to load roster');
				}

				const players = Array.isArray(payload.players) ? payload.players : [];
				if (!players.length) {
					attendanceRosterList.innerHTML = '<div style="padding:18px;color:#666;font-size:13px;">No eligible players found for this session.</div>';
					return;
				}

				attendanceRosterList.innerHTML = players.map(player => {
					const checked = String(player.AttendanceStatus || '').toLowerCase() === 'present' ? 'checked' : '';
					return `
						<label class="attendance-roster-item">
							<div>
								<div class="attendance-roster-name">${escapeHtml(player.PlayerName)}</div>
								<div class="attendance-roster-meta">${player.AttendanceStatus === 'present' ? 'Present' : 'Absent'}</div>
							</div>
							<input class="attendance-roster-toggle" type="checkbox" value="${escapeHtml(player.PlayerID)}" ${checked} />
						</label>
					`;
				}).join('');
			})
			.catch(error => {
				attendanceRosterList.innerHTML = `<div style="padding:18px;color:#b91c1c;font-size:13px;">${error.message}</div>`;
			});
	}

	function closeAttendanceModal() {
		if (!attendanceModal) {
			return;
		}

		attendanceModal.classList.remove('is-open');
		attendanceModal.setAttribute('aria-hidden', 'true');
		activeAttendanceSessionId = null;
		if (attendanceRosterList) {
			attendanceRosterList.innerHTML = '';
		}
	}

	function saveAttendanceRoster() {
		if (!activeAttendanceSessionId || !attendanceRosterList) {
			return;
		}

		const checkedPlayers = Array.from(attendanceRosterList.querySelectorAll('input[type="checkbox"]:checked')).map(input => input.value);
		const payload = new FormData();
		payload.append('session_id', String(activeAttendanceSessionId));
		checkedPlayers.forEach(playerId => payload.append('attendance_present[]', playerId));

		if (saveAttendanceBtn) {
			saveAttendanceBtn.disabled = true;
			saveAttendanceBtn.textContent = 'Saving...';
		}

		fetch(endpoints.saveAttendance || '', {
			method: 'POST',
			body: payload,
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(response => response.json())
			.then(result => {
				if (!result || !result.success) {
					throw new Error(result && result.message ? result.message : 'Failed to save attendance');
				}
				closeAttendanceModal();
				window.location.reload();
			})
			.catch(error => {
				alert(error.message);
			})
			.finally(() => {
				if (saveAttendanceBtn) {
					saveAttendanceBtn.disabled = false;
					saveAttendanceBtn.textContent = 'Save Attendance';
				}
			});
	}

	function renderPerformance(playerId){
		const ctx = document.getElementById('performanceChart').getContext('2d');
		const ds = buildPerformanceDataset(playerId);
		const cfg = {
			type: performanceIsLine ? 'line' : 'bar',
			data: {
				labels: ds.labels,
				datasets: [{
					label: 'Performance Rating',
					data: ds.values,
					backgroundColor: performanceIsLine ? 'rgba(31,111,235,0.08)' : 'rgba(31,111,235,0.6)',
					borderColor: 'rgba(31,111,235,1)',
					borderWidth: 2,
					tension: 0.3,
					fill: performanceIsLine
				}]
			},
			options: {
				responsive:true,
				plugins: { legend:{ display:true } },
				scales: { y:{ beginAtZero:true, suggestedMax:10 } }
			}
		};
		if(performanceChart){ performanceChart.destroy(); }
		performanceChart = new Chart(ctx, cfg);
	}

	function renderAttendance(){
		const canvas = document.getElementById('attendanceChart');
		if (!canvas || typeof Chart === 'undefined') {
			return;
		}
		const ctx = canvas.getContext('2d');
		const attendanceData = data.attendanceData || data.attendanceChartData || {};
		const labels = Array.isArray(attendanceData.labels) ? attendanceData.labels : ['Week 1','Week 2','Week 3','Week 4'];
		const datasets = attendanceData.datasets || {};
		const teamSel = document.getElementById('attendanceTeamSelect');
		const selectedType = teamSel && datasets[teamSel.value] ? teamSel.value : 'all';
		const dataset = datasets[selectedType] || { label: 'Attendance', values: Array.isArray(attendanceData.values) ? attendanceData.values : (Array.isArray(attendanceData) ? attendanceData : [0,0,0,0]) };
		const values = Array.isArray(dataset.values) ? dataset.values : [0,0,0,0];
		const cfg = {
			type: 'bar',
			data: { labels, datasets:[{ label: dataset.label || 'Attendance', data:values, backgroundColor:'rgba(14,165,164,0.7)' }] },
			options: { responsive:true, plugins:{ legend:{display:false} } }
		};
		if(attendanceChart){ attendanceChart.destroy(); }
		attendanceChart = new Chart(ctx, cfg);
	}

	function renderHealth(){
		const canvas = document.getElementById('healthChart');
		if (!canvas || typeof Chart === 'undefined') {
			return;
		}
		const ctx = canvas.getContext('2d');
		const labels = ['Fit','Under Observation','Injured'];
		const values = data.healthData || [0,0,0];
		const cfg = {
			type: 'pie',
			data: { labels, datasets:[{ data: values, backgroundColor:['#10b981','#f59e0b','#ef4444'] }] },
			options: { responsive:true }
		};
		if(healthChart){ healthChart.destroy(); }
		healthChart = new Chart(ctx, cfg);
	}

	// UI handlers (guard existence since performance chart may be removed)
	const perfToggle = document.getElementById('performanceToggleType');
	const perfSelect = document.getElementById('performancePlayerSelect');
	if (perfToggle && perfSelect) {
		perfToggle.addEventListener('click', function(){
			performanceIsLine = !performanceIsLine; renderPerformance(perfSelect.value);
		});

		perfSelect.addEventListener('change', function(){
			renderPerformance(this.value);
		});
	}

	const attendanceTeam = document.getElementById('attendanceTeamSelect');
	const attendanceRange = document.getElementById('attendanceRangeSelect');
	if (attendanceTeam) attendanceTeam.addEventListener('change', renderAttendance);
	if (attendanceRange) attendanceRange.addEventListener('change', renderAttendance);

	const healthFilter = document.getElementById('healthFilterSelect');
	if (healthFilter) healthFilter.addEventListener('change', renderHealth);

	document.querySelectorAll('.attendance-list-btn').forEach(button => {
		button.addEventListener('click', function() {
			openAttendanceModal(this.dataset.sessionId, this.dataset.sessionName);
		});
	});

	if (attendanceModal) {
		attendanceModal.addEventListener('click', function(event) {
			const actionTarget = event.target.closest('[data-action="close-attendance"]');
			if (actionTarget) {
				closeAttendanceModal();
			}
		});
	}

	if (saveAttendanceBtn) {
		saveAttendanceBtn.addEventListener('click', saveAttendanceRoster);
	}

	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			closeAttendanceModal();
		}
	});

	// Initialize
	populatePlayerSelect();
	// Only render performance if the canvas exists
	if (document.getElementById('performanceChart')) {
		renderPerformance('all');
	}
	if (document.getElementById('attendanceChart')) {
		renderAttendance();
	}
	if (document.getElementById('healthChart')) {
		renderHealth();
	}
});
