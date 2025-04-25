<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Job Search & List</title>
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-left">
                <div class="logo-container">
                    <a href="index.php" class="logo-link">
                        <img src="pinoyseaman-logo/pinoyseaman-logo.png" alt="pinoyseaman-logo" id="sidebar-logo">
                    </a>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="job_search.php">Jobs</a></li>
                    <li><a href="explore-companies.php">Explore Companies</a></li>
                    <li><a href="#">Contact us</a></li>
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

    <main class="job-page-main">
        <section class="job-nav-section">
            <nav>
                <ul class="job-nav-links">
                    <li class="home">
                        <a href="index.php"><i class="fa-solid fa-house"></i>Home</a>
                    </li>
                    <li>
                        <i class="fas fa-chevron-right"></i>
                        <a href="">Jobs</a>
                    </li>
                </ul>
            </nav>
        </section>
        <section>
            <div class="search-container">
                <select class="search-select">
                    <option selected>Select job</option>
                </select>
                <select class="search-select">
                    <option selected>Select vessel type</option>
                </select>
                <button class="search-button">Find jobs</button>
            </div>
        </section>

        <section>
            <div class="card-title">
                <h3>
                    <?php
                    // Include the database connection file
                    include 'db.php';

                    // Query to count total jobs
                    $jobCountQuery = "SELECT COUNT(*) AS total_jobs FROM jobs WHERE expiry >= CURDATE()";
                    $jobCountResult = $conn->query($jobCountQuery);

                    if ($jobCountResult && $row = $jobCountResult->fetch_assoc()) {
                        echo "Total Jobs: " . $row['total_jobs'];
                    } else {
                        echo "No jobs available.";
                    }
                    ?>
                </h3>
            </div>

            <div class="job-card-container">
                <?php
                // Include the database connection file
                include 'db.php';

                // Pagination variables
                $jobsPerPage = 10; // Number of jobs per page
                $currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($currentPage - 1) * $jobsPerPage;

                // Query to count total jobs
                $totalJobsQuery = "SELECT COUNT(*) AS total_jobs FROM jobs WHERE expiry >= CURDATE()";
                $totalJobsResult = $conn->query($totalJobsQuery);
                $totalJobs = $totalJobsResult->fetch_assoc()['total_jobs'];
                $totalPages = ceil($totalJobs / $jobsPerPage);

                // Query to fetch jobs for the current page
                $jobQuery = "SELECT j.job_title, j.vessel, j.date_posted, e.company, e.logo 
                            FROM jobs j
                            INNER JOIN employer e ON j.company_code = e.company_code
                            WHERE j.expiry >= CURDATE()
                            ORDER BY j.date_posted DESC
                            LIMIT $offset, $jobsPerPage";
                $jobResult = $conn->query($jobQuery);

                if ($jobResult && $jobResult->num_rows > 0) {
                    while ($job = $jobResult->fetch_assoc()) {
                        $jobTitle = htmlspecialchars($job['job_title']);
                        $vessel = htmlspecialchars($job['vessel']);
                        // $salary = htmlspecialchars($job['salary']);
                        // $contract = htmlspecialchars($job['contract']);
                        $datePosted = htmlspecialchars($job['date_posted']);
                        $companyName = htmlspecialchars($job['company']);
                        $logoPath = "company-logo/" . htmlspecialchars($job['logo']);

                        // Check if the logo file exists, otherwise use a placeholder
                        if (!file_exists($logoPath) || empty($job['logo'])) {
                            $logoPath = "company-logo/Logo-placeholder.png";
                        }
                        ?>
                        <section class="job-card">
                            <div class="job-image">
                                <img src="<?php echo $logoPath; ?>" alt="Company Logo">
                            </div>
                            <div class="job-info">
                                <label class="job-title"><?php echo $jobTitle; ?></label>
                                
                                <div class="job-details">
                                    <p class="job-description"><i class="fas fa-ship"></i> <?php echo $vessel; ?></p>
                                    <p class="job-description"><i class="fa-solid fa-money-bill"></i> Salary</p>
                                    <p class="job-description"><i class="fa-solid fa-calendar"></i> Contract in months</p>
                                </div>  
                                
                                <a href="#" class="company-link"><i class="fas fa-briefcase"></i> <?php echo $companyName; ?></a>
                            </div>
                            
                            <div class="apply-container">
                                <p class="date-posted"><?php echo $datePosted; ?></p>
                                <button class="apply-button">Details & Apply</button>
                            </div>
                        </section>
                        <?php
                    }
                } else {
                    echo "<p>No jobs available at the moment.</p>";
                }
                ?>
            </div>
        </section>

        <section>
            <div class="section-pagination"> 
                <ul class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-prev">
                            <a href="?page=<?php echo $currentPage - 1; ?>">&lt;</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-number <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-next">
                            <a href="?page=<?php echo $currentPage + 1; ?>">&gt;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
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
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Advertise</a></li>
                </ul>
            </div>
        </div>
    </footer>  
    <script src="script/nav-hover-effect.js"></script>
    <script src="script/redirect.js"></script>

</body>
</html>