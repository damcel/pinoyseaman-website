
const ctx = document.getElementById('metricsChart').getContext('2d');
const chart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Feb 22', '', '', '', 'Feb 27', '', '', 'Mar 4', '', '', 'Mar 9', '', 'Mar 14', '', '', 'Mar 19'],
    datasets: [
      {
        label: 'Natural',
        data: [5, 0, 10, 30, 250, 130, 60, 80, 130, 100, 40, 30, 60, 55, 30, 45],
        borderColor: '#0072ff',
        backgroundColor: 'transparent',
        borderWidth: 2,
        tension: 0.3
      },
      {
        label: 'Sponsored',
        data: [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
        borderColor: '#a3a3a3',
        backgroundColor: 'transparent',
        borderDash: [5, 5],
        borderWidth: 1.5,
        tension: 0.3
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: false
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});

