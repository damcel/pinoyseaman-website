<?php
// Include the database connection file
include 'db.php';

// Get the job_id from the query parameter
$jobId = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

// Fetch job details based on the job_id
$jobQuery = "SELECT j.*, e.company, e.logo, e.website 
             FROM jobs j
             INNER JOIN employer e ON j.company_code = e.company_code
             WHERE j.code = ?";
$stmt = $conn->prepare($jobQuery);
$stmt->bind_param("i", $jobId);
$stmt->execute();
$jobResult = $stmt->get_result();
$job = $jobResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title><?php echo htmlspecialchars($job['job_title'] ?? 'Job Details'); ?></title>
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
                    <li><a href="explore-companies.html">Explore Companies</a></li>
                    <li><a href="#">Contact us</a></li>
                    <li><a href="user-login-signup.php" class="login-btn">Join Us</a></li>
                    <li><a href="employer-login-signup.php" class="signup-btn">Employer login</a></li>
                </ul>
            </div>

            <!-- Moved Buttons Inside nav-links -->
            <div class="buttons">
                <a href="user-login-signup.php" class="login-btn">Join Us</a>
                <a href="employer-login-signup.php" class="signup-btn">Employer Login</a>
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
                        <a href="job_search.php">Jobs</a>
                    </li>
                    <li>
                        <i class="fas fa-chevron-right"></i>
                        <a href="jobs.html">Tanker Vessel</a>
                    </li>
                </ul>
            </nav>
        </section>

        <section class="job-layout">
            <!-- Container for all asides -->
            <section class="aside-container">
                <!-- Company Information -->
                <aside class="about-company">
                    <div class="about-company-img">
                        <?php
                        $logoPath = "company-logo/" . htmlspecialchars($job['logo']);
                        if (!file_exists($logoPath) || empty($job['logo'])) {
                            $logoPath = "company-logo/Logo-placeholder.png"; // Placeholder image
                        }
                        ?>
                        <img src="<?php echo $logoPath; ?>" alt="Company Image">
                    </div>
                    <article class="aside-company-info">
                        <h3><?php echo htmlspecialchars($job['company_name']) ?></h3>
                        <p><i class="fa-solid fa-circle-info"></i><?php echo htmlspecialchars($job['company_name']) ?></p>
                        <p><i class="fa-solid fa-envelope"></i><a href="mailto:<?php echo htmlspecialchars($job['email']) ?>"><?php echo htmlspecialchars($job['email']) ?></a></p>
                        <p><i class="fa-solid fa-compass"></i><a href=""><?php echo htmlspecialchars($job['website']) ?></a></p>
                        <?php
                        // Fetch total job posts for the company
                        $totalJobsQuery = "SELECT COUNT(*) as total_jobs FROM jobs WHERE company_code = ? AND expiry >= CURDATE()";
                        $totalJobsStmt = $conn->prepare($totalJobsQuery);
                        $totalJobsStmt->bind_param("s", $job['company_code']);
                        $totalJobsStmt->execute();
                        $totalJobsResult = $totalJobsStmt->get_result();
                        $totalJobs = $totalJobsResult->fetch_assoc();
                        ?>
                        <p><i class="fa-solid fa-suitcase"></i><?php echo htmlspecialchars($totalJobs['total_jobs'] ?? 0); ?> total job post(s)</p>
                    </article>
                </aside>

                <!-- Registration Info -->
                <aside class="register-info">
                    <h2>Why you should register to PinoySeaman</h2>
                    <ul>
                        <li><i class="fa-solid fa-circle-check"></i>It's free (and it always will be!)</li>
                        <li><i class="fa-solid fa-circle-check"></i>We work with professional employers & manning agents</li>
                        <li><i class="fa-solid fa-circle-check"></i>Find a better maritime job — faster!</li>
                        <li><i class="fa-solid fa-circle-check"></i> No ads, scams, or junk mail — we promise</li>
                    </ul>
                    <div class="register-btn">
                        <a href="start.php">Create your account now</a>
                    </div>
                </aside>

                <!-- seafarer image -->
                <aside class="related-jobs">
                    <h2>From our Filipino Seafarer</h2>
                    <div class="seaman-img">
                        <img src="marino/75.jpg" alt="Tanker Vessel">
                    </div>
                </aside>
            </section>

            <!-- Job Details Container -->
            <section class="job-container">
                    <!-- First Job Details -->
                <?php if ($job): ?>     
                <article class="job-details-container">
                    <section class="job-heading-container">
                        <div>
                            <h1 class="job-type"><?php echo htmlspecialchars($job['job_title']); ?></h1>
                            <p class="company-name">
                                <i class="fa-solid fa-building"></i> <?php echo htmlspecialchars($job['company']); ?>
                            </p>
                        </div>
                        <div class="joblist-btn-container">
                            <div>
                                <button class="quick-apply" onclick="redirectToDashboard()">Quick Apply</button>
                            </div>
                            <div>
                                <p class="date-posted">Date Posted: <?php echo htmlspecialchars($job['date_posted']); ?></p>
                                <p class="last-update">Last Update: <?php echo htmlspecialchars($job['date_modified']); ?></p>
                            </div>
                        </div>
                    </section>

                    <section class="job-information">
                        <dl>
                            <div class="job-info-row">
                                <dt><i class="fa-solid fa-user"></i> Rank</dt>
                                <dd>Electrical Technical Officer</dd>
                            </div>
                            <div class="job-info-row">
                                <dt><i class="fa-solid fa-ship"></i> Vessel type</dt>
                                <dd><?php echo htmlspecialchars($job['vessel']); ?></dd>
                            </div>
                            <div class="job-info-row">
                                <dt><i class="fa-solid fa-clock"></i> Contract Length</dt>
                                <dd>4 months</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="jobpage-description">
                        <dd>Requirements</dd>
                        <p><?php echo htmlspecialchars($job['requirements']); ?></p>
                    </section>

                    <section class="jobpage-description">
                        <dd>Job Description</dd>
                        <p><?php echo htmlspecialchars($job['job_description']); ?></p>
                    </section>
                </article>

                <?php
                // Fetch similar jobs based on vessel type or job title
                $similarJobsQuery = "SELECT j.code, j.job_title, j.vessel, j.date_posted, e.company, e.logo, e.member_type
                                    FROM jobs j
                                    INNER JOIN employer e ON j.company_code = e.company_code
                                    WHERE (j.vessel = ? OR j.job_title LIKE ?)
                                    AND j.code != ? 
                                    AND j.expiry >= CURDATE()
                                    ORDER BY 
                                        FIELD(e.member_type, 'Plan4', 'Plan3', 'Plan2', 'Plan1', 'FREE') ASC
                                    LIMIT 5";
                $similarStmt = $conn->prepare($similarJobsQuery);
                $jobTitleLike = '%' . $job['job_title'] . '%';
                $similarStmt->bind_param("ssi", $job['vessel'], $jobTitleLike, $jobId);
                $similarStmt->execute();
                $similarJobsResult = $similarStmt->get_result();
                ?>

                <!-- related job list -->
                <article class="job-details-container">
                    <h1 class="similar-job-header">Similar jobs</h1>

                    <?php if ($similarJobsResult && $similarJobsResult->num_rows > 0): ?>
                        <?php while ($similarJob = $similarJobsResult->fetch_assoc()): ?>

                        <!-- cards for related job list -->
                        <section class="related-job-card">
                            <div class="job-image">
                                <?php
                                $similarLogoPath = "company-logo/" . htmlspecialchars($similarJob['logo']);
                                if (!file_exists($similarLogoPath) || empty($similarJob['logo'])) {
                                    $similarLogoPath = "company-logo/Logo-placeholder.png"; // Placeholder image
                                }
                                ?>
                                
                                <img src="<?php echo $similarLogoPath; ?>" alt="Job Image">
                            </div>
                            <div class="job-info">
                                <label class="job-title"><?php echo htmlspecialchars($similarJob['job_title']); ?></label>
                                
                                <div class="job-details">
                                    <p class="job-description"><i class="fas fa-ship"></i> <?php echo htmlspecialchars($similarJob['vessel']); ?></p>
                                    <p class="job-description"><i class="fa-solid fa-money-bill"></i> Salary</p>
                                    <p class="job-description"><i class="fa-solid fa-calendar"></i> Contract In Months</p>
                                </div>  
                                
                                <a href="#" class="company-link"><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($similarJob['company']); ?></a>
                            </div>
                            
                            
                            <div class="apply-container">
                                <p class="date-posted">Date Posted: <?php echo htmlspecialchars($similarJob['date_posted']); ?></p>
                                <a href="jobs.php?job_id=<?php echo $similarJob['code']; ?>" class="apply-button">Details & Apply</a>
                            </div>
                        </section>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <p>No similar jobs found.</p>
                <?php endif; ?>
                                        
                    <figcaption><a href="job_search.php" class="see-more">See more</a></figcaption>

                </article>
                <?php else: ?>
                <p>Job not found.</p>
            <?php endif; ?>
            </section>        
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section brand">
                <img src="pinoyseaman-logo/alternativeHeaderLogo.png" alt="footer-logo">
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
    <script>
        function redirectToDashboard() {
            window.location.href = 'dashboardjobs.php';
        }
    </script>
</body>
</html>