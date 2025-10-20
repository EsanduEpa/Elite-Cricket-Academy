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

	// Create sample datasets from PHP data or fallbacks
	function buildPerformanceDataset(playerId){
		// For demo, create synthetic time series
		const days = 12;
		const labels = [];
		const values = [];
		for(let i=days-1;i>=0;i--){
			const d = new Date(); d.setDate(d.getDate()-i);
			labels.push(d.toISOString().slice(0,10));
			values.push(Math.round((Math.random()*2 + 6) * 10)/10);
		}
		return {labels, values};
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
		const values = labels.map(()=> Math.floor(Math.random()*10)+5);
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
		const values = [Math.floor(Math.random()*50)+20, Math.floor(Math.random()*15)+5, Math.floor(Math.random()*8)+2];
		const cfg = {
			type: 'pie',
			data: { labels, datasets:[{ data: values, backgroundColor:['#10b981','#f59e0b','#ef4444'] }] },
			options: { responsive:true }
		};
		if(healthChart){ healthChart.destroy(); }
		healthChart = new Chart(ctx, cfg);
	}

	// UI handlers
	document.getElementById('performanceToggleType').addEventListener('click', function(){
		performanceIsLine = !performanceIsLine; renderPerformance(document.getElementById('performancePlayerSelect').value);
	});

	document.getElementById('performancePlayerSelect').addEventListener('change', function(){
		renderPerformance(this.value);
	});
	document.getElementById('attendanceTeamSelect').addEventListener('change', renderAttendance);
	document.getElementById('attendanceRangeSelect').addEventListener('change', renderAttendance);
	document.getElementById('healthFilterSelect').addEventListener('change', renderHealth);

	// Nav anchors smooth scroll
	document.querySelectorAll('.nav-anchor').forEach(a=>{
		a.addEventListener('click', function(e){
			e.preventDefault(); const target = document.querySelector(this.getAttribute('href'));
			if(target) target.scrollIntoView({behavior:'smooth', block:'center'});
		});
	});

	// Initialize
	populatePlayerSelect();
	renderPerformance('all');
	renderAttendance();
	renderHealth();
});
