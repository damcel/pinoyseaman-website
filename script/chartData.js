const dayPageSelector = document.getElementById('dayPageSelector');
const yearPageSelector = document.getElementById('yearPageSelector');
const viewSelector = document.getElementById('viewSelector');
const dataTable = document.querySelector('#dataTable tbody');
const barChart = document.getElementById('barChart');

let currentPage = 1;
const rowsPerPage = 7;

const today = new Date();
let selectedMonthIndex = today.getMonth() + 1; // Jan = 1
let selectedYear = today.getFullYear();
let dailyData = [];

function populateMonthDropdown() {
  dayPageSelector.innerHTML = '';
  for (let i = 0; i < 12; i++) {
    const opt = document.createElement('option');
    opt.value = i + 1;
    opt.textContent = new Date(0, i).toLocaleString('default', { month: 'short' });
    if (i + 1 === selectedMonthIndex) opt.selected = true;
    dayPageSelector.appendChild(opt);
  }
}

function populateYearDropdown() {
  const currentYear = today.getFullYear();
  for (let y = currentYear; y >= currentYear - 5; y--) {
    const opt = document.createElement('option');
    opt.value = y;
    opt.textContent = y;
    if (y === selectedYear) opt.selected = true;
    yearPageSelector.appendChild(opt);
  }
}

function fetchDailyData(month, year) {
  fetch(`get_daily_counts.php?month=${month}&year=${year}`)
    .then(res => res.json())
    .then(data => {
      dailyData = data;
      currentPage = 1;
      renderBarChart();
      renderTable();
    });
}

function renderBarChart() {
  barChart.innerHTML = '';
  const daysInMonth = new Date(selectedYear, selectedMonthIndex, 0).getDate();
  const dataMap = new Map(dailyData.map(item => [item.reg_date, parseInt(item.count)]));

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${selectedYear}-${String(selectedMonthIndex).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const count = dataMap.get(dateStr) || 0;

    if (selectedYear === today.getFullYear() && selectedMonthIndex === today.getMonth() + 1 && d >= today.getDate()) {
      break;
    }

    const barGroup = document.createElement('div');
    barGroup.className = 'bar-group';

    const bar = document.createElement('div');
    bar.className = 'bar';
    bar.style.height = `${(count / 10) * 20}px`; // Adjust as needed
    bar.title = `${count} applicant(s) on Day ${d}`;

    const label = document.createElement('div');
    label.className = 'label';
    label.textContent = d;

    barGroup.appendChild(bar);
    barGroup.appendChild(label);
    barChart.appendChild(barGroup);
  }
}

function renderTable() {
  const dataMap = new Map(dailyData.map(item => [item.reg_date, parseInt(item.count)]));
  const daysInMonth = new Date(selectedYear, selectedMonthIndex, 0).getDate();
  const data = [];

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = `${selectedYear}-${String(selectedMonthIndex).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const count = dataMap.get(dateStr) || 0;

    if (selectedYear === today.getFullYear() && selectedMonthIndex === today.getMonth() + 1 && d >= today.getDate()) {
      break;
    }

    data.push({
      date: dateStr,
      day: new Date(dateStr).toLocaleString('default', { weekday: 'short' }),
      count: count
    });
  }

  // Reverse to show newest first
  data.reverse();

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const paginatedData = data.slice(start, end);

  dataTable.innerHTML = '';
  paginatedData.forEach(row => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.date}</td>
      <td>${row.day}</td>
      <td>${row.count}</td>
    `;
    dataTable.appendChild(tr);
  });

  updatePaginationControls(data.length);
}

function updatePaginationControls(totalRows) {
  const totalPages = Math.ceil(totalRows / rowsPerPage);
  document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
  document.getElementById('prevPage').disabled = currentPage === 1;
  document.getElementById('nextPage').disabled = currentPage === totalPages;
}

document.getElementById('prevPage').addEventListener('click', () => {
  if (currentPage > 1) {
    currentPage--;
    renderTable();
  }
});

document.getElementById('nextPage').addEventListener('click', () => {
  const totalPages = Math.ceil(dailyData.length / rowsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    renderTable();
  }
});


// Initial population and listeners
populateMonthDropdown();
populateYearDropdown();
fetchDailyData(selectedMonthIndex, selectedYear);

dayPageSelector.addEventListener('change', (e) => {
  selectedMonthIndex = parseInt(e.target.value);
  fetchDailyData(selectedMonthIndex, selectedYear);
});
yearPageSelector.addEventListener('change', (e) => {
  selectedYear = parseInt(e.target.value);
  fetchDailyData(selectedMonthIndex, selectedYear);
});
