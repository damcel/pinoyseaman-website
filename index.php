<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#007bff">
    <title>Home</title>

</head>
<body>
    <!-- Alert Container -->
    <div id="alert" class="alert"></div>
    <header>
        <nav class="main-nav">
            <div class="nav-left">
                <div class="logo-container">
                    <a href="index.php" class="logo-link">
                        <img src="pinoyseaman-logo/pinoyseaman-logo.png" alt="pinoyseaman-logo" id="sidebar-logo" loading="lazy">
                    </a>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="job_search.php">Jobs</a></li>
                    <li><a href="explore-companies.php">Explore Companies</a></li>
                    <li><a href="contact-us.php">Contact us</a></li>
                    <li><a href="start.php" class="login-btn">Login</a></li>
                    <li><a href="start.php" class="signup-btn">Sign up</a></li>
                </ul>
            </div>

             <!-- Moved Buttons Inside nav-links -->
             <div class="buttons">
                <a href="start.php" class="login-btn">Login</a>
                <a href="start.php" class="signup-btn">Sign up</a>
            </div>
    
            <!-- Burger Menu Button -->
            <div class="burger-menu" onclick="toggleMenu()">
                &#9776; <!-- Unicode for the burger icon -->
            </div>
        </nav>
    </header>
    
<main>

    <section class="bg-container">
        <div class="top-section">
            <section>
                <h1 class="motto">TRABAHONG SEAMAN, ISANG CLICK NALANG</h1>
            </section>
            <section class="container">
                    <div class="header-form">
                        <h1>Registration Form</h1>
                    </div>
                    <div>
                        <form action="includes/seaman_init_reg2.php" method="POST" class="form-container">
                            <div class="user-card">
                                <div class="input-fname">
                                    <label for="firstname">Firstname</label>
                                    <input type="text" id="firstname" name="firstname" placeholder="Firstname">
                                </div>
                                <div class="input-lname">
                                    <label for="lastname">Lastname</label>
                                    <input type="text" id="lastname" name="lastname" placeholder="Lastname">
                                </div>
                            </div>
                            <div class="input-no">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" name="phone" placeholder="11 digits">
                            </div>
    
                            <div class="birth-card">
                                <label for="dob">Date of birth</label>
                                <div class="input-group">
                                    <select id="month" name="month">
                                        <option>January</option>
                                        <option>February</option>
                                        <option>March</option>
                                        <option>April</option>
                                        <option>May</option>
                                        <option>June</option>
                                        <option>July</option>
                                        <option>August</option>
                                        <option>September</option>
                                        <option>October</option>
                                        <option>November</option>
                                        <option>December</option>
                                    </select>
                                    <input type="number" id="day" name="day" placeholder="1" min="1" max="31">
                                    <input type="number" id="year" name="year" placeholder="2025" min="1900">
                                </div>
                            </div>
    
                            <div class="input-email">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="Email">
                            </div>
    
                            <div class="input-pws">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" placeholder="Password">
                            </div>
    
                            <div class="checkbox-container">
                                <input type="checkbox" id="view" name="view">
                                <label for="view">Allow Employer to view my profile and include me on manual job search.</label>
                            </div>
    
                            <div class="btn-create">
                                <button class="create-btn" type="submit">Create Account</button>
                                <p>Already have an account? <a href="#">Log in</a></p>
                            </div>
                        </form>
                    </div>  
            </section>
        </div>
    </section>
    
    <section class="statics-section">
        <div class="statistics">
            <h2>Statistics</h2>
        </div>
        <div class="stats-container">
            <div class="stat-card">
                <div class="icon-title">
                    <i class="fa-solid fa-user"></i>
                    <h3>Registered Job-Seekers</h3>
                </div>
                <p>
                    Total: 
                    <?php
                    // Include the database connection file
                    

                    // Query to count total users in the job_seeker table
                    $sql = "SELECT COUNT(*) AS total_users FROM job_seeker";
                    $result = $conn->query($sql);

                    if ($result && $row = $result->fetch_assoc()) {
                        echo number_format($row['total_users']);
                    } else {
                        echo "0"; // Display 0 if the query fails
                    }
                    ?>
                </p>
            </div>
            <div class="stat-card">
                <div class="icon-title">
                    <i class="fa-solid fa-building"></i>
                    <h3>Registered Company</h3>
                </div>
                <p>
                    Total: 
                    <?php
                    // Include the database connection file
                    

                    // Query to count total users in the job_seeker table
                    $sql = "SELECT COUNT(*) AS total_employers FROM employer WHERE verify = 'y'";
                    $result = $conn->query($sql);

                    if ($result && $row = $result->fetch_assoc()) {
                        echo $row['total_employers'];
                    } else {
                        echo "0"; // Display 0 if the query fails
                    }
                    ?>
                </p>
            </div>
            <div class="stat-card">
                <div class="icon-title">
                    <i class="fa-solid fa-briefcase"></i>
                    <h3>Job Posted</h3>
                </div>
                <p>
                    Total: 
                    <?php
                    // Include the database connection file
                    

                    // Query to count total jobs where expiry is not less than today's date
                    $sql = "SELECT COUNT(*) AS total_jobs FROM jobs WHERE expiry >= CURDATE()";
                    $result = $conn->query($sql);

                    if ($result && $row = $result->fetch_assoc()) {
                        echo $row['total_jobs'];
                    } else {
                        echo "0"; // Display 0 if the query fails
                    }
                    ?>
                </p>
            </div>
        </div>
    </section>

    <section>
        <div class="company-title-card">
            <h2>Companies</h2>
            <p>
                Join us and connect with top talent effortlessly. Post jobs, 
                find skilled crew members, and streamline your hiring process. 
                all in one place!
            </p>
        </div>

        <div class="slider-wrapper">
            <button id="prevBtn" class="slider-btn">&#10094;</button>

            <div class="company-container">
                <div class="company-track">
                    <?php
                    // Include the database connection file
                    

                    // Query to get all featured companies
                    $sql = "SELECT company_code, company, company_profile, logo
                            FROM employer
                            WHERE verify = 'y' AND (member_type = 'Plan1' OR member_type = 'Plan2' OR member_type = 'Plan3' OR member_type = 'Plan4')";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $company_name = $row['company'];
                            $company_profile = $row['company_profile'];
                            $company_code = $row['company_code'];
                            $logo = $row['logo'];
                            $logo_path = "company-logo/" . $logo;

                            // Check if the logo file exists, otherwise use the placeholder
                            if (!file_exists($logo_path) || empty($logo)) {
                                $logo_path = "company-logo/Logo-placeholder.png";
                            }
                            ?>
                            <div class="company-card">
                                <div class="employer-profile-container">
                                    <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Company image" loading="lazy">
                                </div>
                                <div class="company-card-content">
                                    <h3><strong><?php echo htmlspecialchars($company_name); ?></strong></h3>
                                    <p><?php echo htmlspecialchars($company_profile) ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No featured companies available at the moment.</p>";
                    }
                    ?>
                </div>
            </div>
            <button id="nextBtn" class="slider-btn">&#10095;</button>
        </div>
    </section>
    
    <section class="job-section">
        <div class="company-title-card">
            <h2>Urgent Seaman Job Hiring</h2>
        </div>
        <div class="urg-job-main-container">

                <?php

                // Include the database connection file
                

                // Query to get all featured companies
                $sql = "SELECT e.company_code, e.company, e.logo, j.job_title, j.vessel, j.company_code
                    FROM employer e
                    INNER JOIN jobs j ON e.company_code = j.company_code
                    WHERE j.expiry >= CURDATE()
                    AND e.verify = 'y'
                    AND e.member_type != 'FREE'";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $company_name = $row['company'];
                        $company_code = $row['company_code'];
                        $logo = $row['logo'];
                        $job_title = $row['job_title'];
                        $vessel = $row['vessel'];

                        $logo_path = "company-logo/" . $logo;

                        // Check if the logo file exists, otherwise use the placeholder
                        if (!file_exists($logo_path) || empty($logo)) {
                            $logo_path = "company-logo/Logo-placeholder.png";
                        }
                        ?>
                        <section>
                            <div class="urgent-jobs-container">
                                <div class="company-info">
                                    <div class="company-jobs-img">
                                        <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Company image" loading="lazy">
                                    </div>
                                    <div class="company-jobs-content">
                                        <h2><?php echo htmlspecialchars($job_title) ?></h2>
                                        <h3><?php echo htmlspecialchars($company_name); ?></h3>
                                        <div class="tags"><?php echo htmlspecialchars($vessel) ?></div>
                                    </div>
                                </div>
                                <div class="company-jobs-btn">
                                    <button class="jobs-btn">Details & Apply</button>
                                </div>
                            </div>
                        </section>
                        <?php
                    }
                } else {
                    echo "<p>No featured companies available at the moment.</p>";
                }

                ?>
        </div>
        <a href="job_search.php">Search all jobs <i class="fa-solid fa-arrow-right"></i></a>
    </section>

    <section class="ads-section">
    <div class="ads-container">

        <?php
        // Include the database connection file
        

        // Query to get all featured companies
        $sql = "SELECT e.company_code, ca.ads_url
                FROM employer e
                INNER JOIN company_ads ca ON e.company_code = ca.company_code
                WHERE e.verify = 'y' 
                AND e.member_type != 'FREE'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $company_code = $row['company_code'];
                $ads_url = $row['ads_url'];

                $ads_path = "company-ads/" . $ads_url;

                // Check if the logo file exists, otherwise use the placeholder
                if (!file_exists($ads_path) || empty($ads_path)) {
                    $ads_path = "company-ads/ads-placeholder.png";
                }
                ?>
                
                    <div class="ads-card"><a href=""><img src="<?php echo htmlspecialchars($ads_path); ?>" alt="ads" loading="lazy"></a></div>
                
                <?php
            }
        } else {
            echo "<p>No featured companies available at the moment.</p>";
        }

        ?>

        </div>

        <!-- <div class="ads-container">
            <div class="ads-card"><a href=""><img src="company-ads/vship_logo.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/wallem_logo.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/status_big.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/marl.jpg" alt="ads"></a></div>
            
            <div class="ads-card"><a href=""><img src="company-ads/vship_logo.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/wallem_logo.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/status_big.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/marl.jpg" alt="ads"></a></div>
            
            <div class="ads-card"><a href=""><img src="company-ads/vship_logo.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/wallem_logo.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/status_big.jpg" alt="ads"></a></div>
            <div class="ads-card"><a href=""><img src="company-ads/marl.jpg" alt="ads"></a></div>
        </div> -->
    </section>

</main>

    <footer>
        <div class="footer-container">
            <div class="footer-section brand">
                <h2>Pinoy<span>Seaman</span></h2>
                <p>© 2023 pinoyseaman. All rights reserved.</p>
            </div>
            <div class="footer-section contact">
                <h3>Get in Touch</h3>
                <p>Emilia Str, Makati City</p>
                <p>filoseaman@gmail.com</p>
                <p>Phone number: (123) 456 78 90</p>
            </div>
            <div class="footer-section links">
                <h3>Learn More</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Our Story</a></li>
                    <li><a href="#">Terms of Use</a></li>
                </ul>
            </div>
            <div class="footer-section links">
                <ul>
                    <li><a href="contact-us.php">Contact us</a></li>
                    <li><a href="#">Advertise</a></li>
                </ul>
            </div>
        </div>
    </footer>   
    <script src="script/company-arrow.js"></script>
    <script src="script/nav-hover-effect.js"></script>
    <!-- <script src="script/ads-carousel.js"></script> -->
    <script>
        // Function to show alert
        function showAlert(message, type = 'success') {
            const alertBox = document.getElementById('alert');
            alertBox.textContent = message;
            alertBox.className = `alert ${type} show`;

            // Hide the alert after 3 seconds
            setTimeout(() => {
                alertBox.classList.remove('show');
            }, 3000);
        }

        // Check for query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const type = urlParams.get('type'); // 'success' or 'error'

        if (message) {
            showAlert(message, type);
            // Remove query parameters from the URL
            history.replaceState(null, '', window.location.pathname);
        }
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service-worker.js')
            .then(reg => console.log('Service Worker Registered', reg))
            .catch(err => console.error('Service Worker Failed', err));
        }

        // Optional: Listen for the A2HS prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // You can optionally show your own custom "Add to Home" button
            console.log('A2HS prompt available');
            // e.prompt(); // Uncomment to auto prompt (not recommended)
        });
    </script>

</body>
</html>