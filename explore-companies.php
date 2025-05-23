<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Explore Companies</title>
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
                    <li><a href="contact-us.php">Contact us</a></li>
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
    
<main>

    <section class="company-header">
        <div class="company-header-container">
            <div class="text-content">
              <h1>Find the right company for you</h1>
              <p>Everything you need to know about a company,<br> all in one place</p>
              <!-- <div class="search-box">
                <input type="text" placeholder="Search by company name" />
                <button><i class="fa-solid fa-magnifying-glass"></i></button>
              </div> -->
            </div>
            <div class="image-content">
              <img src="images/group.png" alt="Team illustration" />
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
                    include 'db.php';

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
                                    <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Company image">
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

    <section class="company-perks">
        <h2>Get to know your future company before you apply</h2>
        <div class="perks-grid">
          <div class="perk-item">
            <p class="perk-subtext">Find out about the company culture</p>
            <img src="images/achievement-img.png" alt="Culture and values icon" />
            <h3>Culture and values</h3>
          </div>
          <div class="perk-item">
            <p class="perk-subtext">Join thousands of Filipino seafarers</p>
            <img src="images/heart-img.png" alt="Ratings and reviews icon" />
            <h3>Trusted by many</h3>
          </div>
          <div class="perk-item">
            <p class="perk-subtext">Find perks that matter to you</p>
            <img src="images/rate-img.png" alt="Perks and benefits icon" />
            <h3>Perks and benefits</h3>
          </div>
        </div>
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
    <script src="script/company-arrow.js"></script>
    <script src="script/nav-hover-effect.js"></script>
</body>
</html>