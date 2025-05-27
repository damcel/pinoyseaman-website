<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="Pinoyseaman.ico" type="image/x-icon"> 
    <title>Admin Panel</title>
    <style>

        .statistics-container{
  margin: min(60px, 7%);
	margin-top: min(30px, 7%);
	flex-wrap: wrap; /* Allows wrapping when the screen is too small */
}

        .header-ctn{
            background-color: #FEFEFE;
            display: flex;
            justify-content: flex-end;
            padding: 15px 60px 15px 60px;
            align-items: center;
            position: relative;
            border-bottom: 1px solid #DAE3F8;
        }

        .admin-panel-ctn{
            background-color: #fefefe;
            border-radius: 8px;
            padding: 15px;
            width: 100%;
            height: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .span-style span{
            font-size: 14px;
        }

        .admin-sidebar {
        height: 60px;
        display: flex;
        align-items: center;
        padding: 5px;
        box-sizing: border-box;
        padding-bottom: 10px;
        margin-bottom: 10px;
        }

        .chart-count-ctn {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  margin-top: 30px;
}

.chart-container, .table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
  padding: 20px;
  font-family: sans-serif;
}

.chart-container {
  flex: 2;
  width: 300px;
  height: fit-content;
}

.table-container {
  flex: 1;
  min-width: 300px;
  overflow-x: auto;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 10px;
}

.chart-header h3, .table-title {
  margin: 0;
  font-size: 18px;
}

.chart-select {
  border: none;
  background: transparent;
  font-weight: bold;
  font-size: 14px;
  cursor: pointer;
}

.bar-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 450px;
  padding: 10px 0;
  overflow-x: auto;
}

.bar-group {
  text-align: center;
  flex: 0 0 auto;
  width: 40px;
  margin: 0 2px;
}

.bar {
  margin: 0 auto;
  border-radius: 10px;
  background: linear-gradient(to top, #3b82f6, #60a5fa);
  box-shadow: 0px 2px 10px rgba(59, 130, 246, 0.3);
  transition: height 0.3s ease;
}

.label {
  margin-top: 8px;
  font-size: 11px;
  color: #444;
  white-space: nowrap;
}

/* Table Styles */
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  color: #333;
}

.data-table th,
.data-table td {
  padding: 10px 12px;
  text-align: center;
}

.data-table th {
  background-color: #f0f4ff;
  color: #1e40af;
}

.data-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.header-table{
    display: flex;
    justify-content: space-between;
}

.drop-table {
    border: none;
    background: transparent;
    font-weight: bold;
    font-size: 14px;
    cursor: pointer;
}

#paginationButtons button {
  padding: 6px 12px;
  font-size: 16px;
  background: #eee;
  border: 1px solid #ccc;
  margin: 0 5px;
  cursor: pointer;
}
#paginationButtons button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

    </style>
</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="admin-sidebar">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="pinoyseaman-logo/admin-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
                    </a>
                </div>
                <button onclick="toggleSidebar()" id="toggle-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0B1C33">
                        <path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/>
                    </svg>
                </button>
            </div>
            <ul class="ul-links">
              <li class="menu-title">MENU</li>
              <li class="span-style">
                <a href="admin-panel-verification.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-user-check"></i><span>EMPLOYER VERIFICATION</span>
                </a>
              </li>
              <li  class="separator">
                <a href="admin-panel-seafarer.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-users"></i><span>SEAFARER</span>
                </a>
              </li>
              <li>
                <a href="admin-panel-employer.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-users-line"></i><span>EMPLOYER</span>
                </a>
              </li>
              <li class="separator">
                <a href="admin-panel-statistics.php">
                  <!-- SVG Icon -->
                  <i class="fa-solid fa-chart-simple"></i><span>STATISTICS</span>
                </a>
              </li>
            </ul>
        </nav>
    </aside>
    
    <main class="dashboard-container">
        <section class="header-ctn">
            <div class="dropdown-container">
                <button class="profile-btn" id="dpBtn"><i class="fa-solid fa-user"></i></button>
                <!-- Dropdown Menu -->
                <div class="dropdown" id="dropdownMenu">
                    <a href="userprofile.php" class="prfl">Profile Settings</a>
                    <a href="includes/logout.php">Logout</a>
                </div>
            </div>
        </section>

        <section class="statistics-container">
            <!-- Chart Container --> 
            <div class="chart-count-ctn">
                <div class="chart-container">
                  <div class="chart-header">
                    <h3>New Registered Monthly Graph</h3>
                    <select id="viewSelector" class="chart-select">
                      <option value="month" selected>Month</option>
                      <option value="day">Day</option>
                    </select>
                  </div>
                  <div id="barChart" class="bar-chart">
                    <!-- Bars will be populated here -->
                  </div>
                </div>
              
                <!-- Data Table -->
                <div class="table-container">
                    <div class="header-table">
                        <h3 class="table-title">Newly Registered User</h3>
                        <select id="dayPageSelector" class="drop-table"></select>
                    </div>
                  <table class="data-table" id="dataTable">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Month</th>
                        <th>Applicant</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- JS inserts data here -->
                    </tbody>
                  </table>
                </div>
              </div>
          </section>

    </main>

    <script src="script/sidenav.js"></script>
    <script src="script/profile-dropdown-menu.js"></script>
    <script src="script/chartData.js"></script>
</body>
</html>