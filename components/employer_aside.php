<aside id="sidebar" <?php echo !$isVerified ? 'style="pointer-events: none; opacity: 0.5;"' : ''; ?>>
    <nav class="sidebar-nav">
        <div class="sidebar-header">
            <div class="logo-container">
                <a href="dashboardjobs.php" class="logo-link">
                    <img src="pinoyseaman-logo/pinoyseaman-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
                </a>
            </div>
            <button onclick="toggleSidebar()" id="toggle-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#0B1C33">
                    <path d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z"/>
                </svg>
            </button>
        </div>
        <ul class="ul-links">
            <div class="company-profile-card">
                <img src="<?php echo $logoPath; ?>" alt="company-logo">
            </div>
            <li>
            <a href="employer-dashboard.php">
                <!-- SVG Icon -->
                <i class="fa-solid fa-briefcase"></i><span>Dashboard</span>
            </a>
            </li>
            <li class="separator">
            <a href="employer-posting.html">
                <!-- SVG Icon -->
                <i class="fa-regular fa-user"></i><span>Job Post</span>
            </a>
            </li>
            <li>
            <a href="employer-analytics.html">
                <!-- SVG Icon -->
                <i class="fa-solid fa-business-time"></i><span>Analytics</span>
            </a>
            </li>
            <li class="separator">
            <a href="account-plan.html">
                <!-- SVG Icon -->
                <i class="fa-solid fa-rocket"></i><span>Premium Plan</span>
            </a>
            </li>
            <div id="progress-main-container" class="progress-main-container">
                <div class="complete-percentage">
                    <p>Complete your profile</p>
                </div>
                <div class="progress-container">
                    <div class="progress-bar" id="progress-bar"></div>
                    <p id="progress-text">0% Completed</p>
                    <div class="incomplete-container">
                        <h3>Incomplete Fields:</h3>
                        <ul id="missing-fields"></ul>
                    </div>
                </div>
            </div>
        </ul>
    </nav>
</aside>