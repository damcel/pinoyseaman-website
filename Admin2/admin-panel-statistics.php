<?php
session_start();
include 'connect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="css/admin-panel-statistics.css">
    <link rel="icon" href="../Pinoyseaman.ico" type="image/x-icon"> 
    <title>Admin Panel</title>

    <style>
      .pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 10px;
  gap: 10px;
}

.pagination-controls button {
  padding: 5px 10px;
}

    </style>

</head>
<body>
    <aside id="sidebar">
        <nav class="sidebar-nav">
            <div class="admin-sidebar">
                <div class="logo-container">
                    <a href="dashboardjobs.php" class="logo-link">
                        <img src="../pinoyseaman-logo/admin-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
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
                        <select id="yearPageSelector" class="drop-table"></select>
                    </div>
                  <table class="data-table" id="dataTable">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Applicant</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- JS inserts data here -->
                    </tbody>
                  </table>
                  <div class="pagination-controls">
                      <button id="prevPage">Previous</button>
                      <span id="pageInfo">Page 1</span>
                      <button id="nextPage">Next</button>
                  </div>
                </div>
              </div>
          </section>

    </main>

    <script src="../script/sidenav.js"></script>
    <script src="../script/profile-dropdown-menu.js"></script>
    <script src="../script/chartData.js"></script>
</body>
</html>