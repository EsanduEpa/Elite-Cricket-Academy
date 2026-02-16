/* Coach Dashboard JS: charts and interactivity using Chart.js */
document.addEventListener('DOMContentLoaded', function(){
	const data = window.__COACH_DASHBOARD_DATA || {};

	// Helper: populate selects
	function populatePlayerSelect() {
		const sel = document.getElementById('performancePlayerSelect');
		const teamSel = document.getElementById('attendanceTeamSelect');
		const players = (data.playerProfiles || []).map(p => ({id:p.id, name:p.name}));
		sel.innerHTML = '';
		teamSel.innerHTML = '';
		const allOpt = document.createElement('option'); allOpt.value='all'; allOpt.text='All Players'; sel.appendChild(allOpt);
		const teamAll = document.createElement('option'); teamAll.value='all'; teamAll.text='All Teams'; teamSel.appendChild(teamAll);
		players.forEach(p=>{
			const o = document.createElement('option'); o.value = p.id; o.text = p.name; sel.appendChild(o);
			const o2 = o.cloneNode(true); teamSel.appendChild(o2);
		});
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
		const ctx = document.getElementById('attendanceChart').getContext('2d');
		const labels = ['Week 1','Week 2','Week 3','Week 4'];
		const values = window.__COACH_DASHBOARD_DATA?.attendanceData || [0,0,0,0];
		const cfg = {
			type: 'bar',
			data: { labels, datasets:[{ label:'Attendance', data:values, backgroundColor:'rgba(14,165,164,0.7)' }] },
			options: { responsive:true, plugins:{ legend:{display:false} } }
		};
		if(attendanceChart){ attendanceChart.destroy(); }
		attendanceChart = new Chart(ctx, cfg);
	}

	function renderHealth(){
		const ctx = document.getElementById('healthChart').getContext('2d');
		const labels = ['Fit','Under Observation','Injured'];
		const values = window.__COACH_DASHBOARD_DATA?.healthData || [0,0,0];
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

	// Nav anchors smooth scroll
	document.querySelectorAll('.nav-anchor').forEach(a=>{
		a.addEventListener('click', function(e){
			e.preventDefault(); const target = document.querySelector(this.getAttribute('href'));
			if(target) target.scrollIntoView({behavior:'smooth', block:'center'});
		});
	});

	// Initialize
	populatePlayerSelect();
	// Only render performance if the canvas exists
	if (document.getElementById('performanceChart')) {
		renderPerformance('all');
	}
	renderAttendance();
	renderHealth();
});
