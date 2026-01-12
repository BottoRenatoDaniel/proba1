// Configuration
const ROWS = Array.from({ length: 26 }, (_, i) => String.fromCharCode(65 + i)); // A-Z
const PER_ROW = 50;
const specialRows = { Y: 'handicap', Z: 'ev' };

// State
const state = {
  rows: {},           // { A: { total: 50, occupied: n }, ... }
  announcement: '',
  simIntervalSec: 5,
  volatility: 0.9,
  timer: null
};

// Initialize rows with random occupancy
function initRows() {
  ROWS.forEach(r => {
    const base = r === 'Y' || r === 'Z' ? 0.6 : 0.4; // special rows slightly higher usage
    const rand = Math.random() * 0.3; // variability
    const occRate = Math.min(0.95, Math.max(0.05, base + rand - 0.15));
    state.rows[r] = { total: PER_ROW, occupied: Math.round(PER_ROW * occRate) };
  });
  updateDashboard();
}

function totalSpaces() {
  return ROWS.length * PER_ROW;
}

function totalOccupied() {
  return ROWS.reduce((sum, r) => sum + state.rows[r].occupied, 0);
}

function updateDashboard() {
  document.getElementById('totalSpaces').textContent = totalSpaces();
  document.getElementById('occupiedSpaces').textContent = totalOccupied();

  // Row table
  const tbody = document.querySelector('#rowTable tbody');
  tbody.innerHTML = '';
  ROWS.forEach(r => {
    const data = state.rows[r];
    const vacant = data.total - data.occupied;
    const utilization = Math.round((data.occupied / data.total) * 100);

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r}${specialRows[r] ? ` <span class="badge ${specialRows[r]==='handicap' ? 'warn' : ''}">${specialRows[r]==='handicap' ? 'Handicap' : 'EV'}</span>` : ''}</td>
      <td>${data.total}</td>
      <td>${data.occupied}</td>
      <td>${vacant}</td>
      <td>
        <div class="bar-wrap">
          <div class="bar" style="width:${utilization}%;"></div>
        </div>
        <div class="subtle" style="margin-top:4px;">${utilization}%</div>
      </td>
    `;
    tbody.appendChild(tr);
  });

  // Special rows summary
  const yOcc = state.rows['Y'].occupied;
  const zOcc = state.rows['Z'].occupied;
  document.getElementById('yTotal').textContent = PER_ROW;
  document.getElementById('zTotal').textContent = PER_ROW;
  document.getElementById('yUsed').textContent = yOcc;
  document.getElementById('zUsed').textContent = zOcc;
  document.getElementById('yOcc').textContent = yOcc;
  document.getElementById('zOcc').textContent = zOcc;

  // Announcement in overview subtitle
  const overviewSubtitle = document.querySelector('#dashboard .card .subtle');
  if (overviewSubtitle) {
    overviewSubtitle.textContent = state.announcement
      ? 'Live status of the simulated parking lot — ' + state.announcement
      : 'Live status of the simulated parking lot.';
  }
}

// Simulation tick: random walk on occupied values
function simTick() {
  ROWS.forEach(r => {
    const cur = state.rows[r].occupied;
    const drift = (Math.random() - 0.5) * (PER_ROW * state.volatility * 0.2);
    let next = Math.round(cur + drift);
    next = Math.max(0, Math.min(PER_ROW, next));
    state.rows[r].occupied = next;
  });
  updateDashboard();
}

function startSimulation() {
  if (state.timer) clearInterval(state.timer);
  state.timer = setInterval(simTick, state.simIntervalSec * 1000);
}

// Navigation
function showPanel(id) {
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
  // Nav buttons
  document.getElementById('btnDashboard').addEventListener('click', () => showPanel('dashboard'));
  document.getElementById('btnControlPanel').addEventListener('click', () => showPanel('controlPanel'));

  // Control Panel: Announcement //problems with the announcement message - it doesnt posts anything ==> sollution: make a pop message insted of using javascript ==>
  document.getElementById('saveAnnouncement').addEventListener('click', () => {
    state.announcement = document.getElementById('announcement').value.trim();
    document.getElementById('announceStatus').textContent = 'Announcement saved (local demo).';
    updateDashboard();
    setTimeout(() => document.getElementById('announceStatus').textContent = '', 2000);
  });

  // Control Panel: SQL placeholder
  document.getElementById('runSQL').addEventListener('click', () => {
    const sql = document.getElementById('sqlEditor').value;
    // TODO: POST `sql` to your backend API for validation/execution.
    document.getElementById('sqlStatus').textContent = 'SQL saved (placeholder).';
    setTimeout(() => document.getElementById('sqlStatus').textContent = '', 2000);
  });

  // Control Panel: Simulation settings
  document.getElementById('applySim').addEventListener('click', () => {
    const sec = parseFloat(document.getElementById('simInterval').value);
    const vol = parseFloat(document.getElementById('simVolatility').value);
    if (isNaN(sec) || sec <= 0) {
      document.getElementById('simStatus').textContent = 'Invalid interval.';
      return;
    }
    if (isNaN(vol) || vol < 0 || vol > 1) {
      document.getElementById('simStatus').textContent = 'Volatility must be 0–1.';
      return;
    }
    state.simIntervalSec = sec;
    state.volatility = vol;
    startSimulation();
    document.getElementById('simStatus').textContent = 'Simulation updated.';
    setTimeout(() => document.getElementById('simStatus').textContent = '', 2000);
  });

  // Boot
  initRows();
  startSimulation();
});