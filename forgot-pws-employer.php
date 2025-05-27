<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="language" content="en">
    <meta name="keywords" content="seaman jobs, maritime recruitment, maritime employment, container jobs, general cargo jobs, yacht jobs, offshore, tanker jobs, jobs at sea, seaman jobs, seaman, maritime jobs, offshore jobs, pinoyseaman, pinoy seaman jobs, jobs abroad, cruise ship jobs, luxury jobs, oil tanker jobs, drilling ship, vessel jobs, sea career, latest jobs, job openning">
    <meta name="description" content="Philippines Oldest Seafarer's Job Posting Site.">
    <meta name="description" content="Find updated seaman jobs and maritime career listings for Filipino seafarers. Connect with trusted manning agencies in the Philippines.">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="Seaman Jobs | Pinoyseaman">
    <meta property="og:image" content="https://www.pinoyseaman.com/index.php/pinoyseaman-logo/logo.png" />
    <link rel="icon" href="Pinoyseaman.ico" type="image/x-icon"> 
    <link rel="canonical" href="https://www.pinoyseaman.com/index.php">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/emp-forgot-pass.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function enableSubmitButton() {
            document.getElementById('submitBtn').disabled = false;
        }
    </script>
    <title>Seafarer Forgot Password</title>

</head>
<body>
    <div class="page-wrapper">
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
            
            <section class="contact-ctn">
                    <div class="contact-form">
                    <h2>Forgot Password?</h2>
                    <form action="includes/forgotPass_emp_verify.php" method="POST">
                            <div class="form-group">
                                <label>Enter your email</label>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6LfxHEYrAAAAAPLHR4Pke5MUyzAufn34RSAU_I3f" data-callback="enableSubmitButton"></div>
                            </div>
                            <br>
                        <button class="btn btn-primary" disabled="disabled" id="submitBtn" type="submit">Retrieved Password</button>
                    </form>
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
                        <li><a href="contact-us.php">Contact us</a></li>
                        <li><a href="#">Advertise</a></li>
                    </ul>
                </div>
            </div>
        </footer>  
    </div>

    <div id="otpModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:400px;margin:auto;padding:30px;border-radius:8px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.2);">
            <span class="close" id="closeOtpModal" style="float:right;font-size:24px;cursor:pointer;">&times;</span>
            <h3 style="margin-top:0;">Enter OTP</h3>
            <div id="otp-timer" style="font-weight:bold; color:#0d82e7; margin-bottom:15px; text-align:center;">
                Time left: <span id="timer-min">02</span>:<span id="timer-sec">00</span>
            </div>
            <form id="otpForm" action="includes/verify_emp_otp.php" method="POST">
            <input type="hidden" name="email" id="otpEmail">
            <div class="form-group">
                <label for="otp">Enter the 6-digit code sent to your email</label>
                <input type="text" name="otp" id="otp" maxlength="6" pattern="\d{6}" required style="width:100%;padding:8px;margin-top:8px;">
            </div>
            <button class="btn btn-success" id="verifyOtpBtn" type="submit" style="margin-top:15px;width:100%;" disabled>Verify OTP</button>
            </form>
            <div id="otpError" style="color:red;margin-top:10px;display:none;"></div>
        </div>
    </div>
 
    <script src="script/nav-hover-effect.js"></script>
    <script>
        // Show modal after form submit if OTP was sent successfully
        document.querySelector('form[action="includes/forgotPass_emp_verify.php"]').addEventListener('submit', function(e) {
            // Save email for modal
            var email = this.querySelector('input[name="email"]').value;
            localStorage.setItem('otpEmail', email);
        });

        // On page load, check for success message and show modal
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type') === 'success' && urlParams.get('message') && urlParams.get('message').includes('OTP sent')) {
                document.getElementById('otpModal').style.display = 'block';
                document.getElementById('otpEmail').value = localStorage.getItem('otpEmail') || '';
            }
            document.getElementById('closeOtpModal').onclick = function() {
                document.getElementById('otpModal').style.display = 'none';
            };
            // Optional: clear email from storage after use
            document.getElementById('otpForm').onsubmit = function() {
                localStorage.removeItem('otpEmail');
            };
        });

        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type') === 'success' && urlParams.get('message') && urlParams.get('message').includes('OTP sent')) {
                document.getElementById('otpModal').style.display = 'block';
                document.getElementById('otpEmail').value = localStorage.getItem('otpEmail') || '';
                // Reset OTP form and timer
                document.getElementById('otp').disabled = false;
                document.getElementById('otpForm').querySelector('button[type="submit"]').disabled = false;
                document.getElementById('otpError').style.display = 'none';
                startOtpTimer(120); // 2 minutes
            }
            // ...existing code...
        });

    </script>

    <script>
        // Fetch OTP expiry from the server and start timer based on expires_at
        function fetchOtpExpiryAndStartTimer(email) {
            fetch('includes/ajax_getEmp_otp_expiry.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.expires_at) {
                    const expiresAt = new Date(data.expires_at.replace(' ', 'T'));
                    startOtpTimerSmooth(expiresAt);
                } else {
                    // fallback to 2 minutes if not found
                    const fallback = new Date(Date.now() + 120 * 1000);
                    startOtpTimerSmooth(fallback);
                }
            })
            .catch(() => {
                const fallback = new Date(Date.now() + 120 * 1000);
                startOtpTimerSmooth(fallback);
            });
        }

        // Smooth timer logic for OTP modal
        function startOtpTimerSmooth(expiresAt) {
            const minSpan = document.getElementById('timer-min');
            const secSpan = document.getElementById('timer-sec');
            const otpForm = document.getElementById('otpForm');
            const otpInput = document.getElementById('otp');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const otpError = document.getElementById('otpError');
            let expired = false;

            function updateTimer() {
                const now = new Date();
                let msLeft = expiresAt - now;
                if (msLeft < 0) msLeft = 0;
                const totalSeconds = Math.floor(msLeft / 1000);
                const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
                const seconds = String(totalSeconds % 60).padStart(2, '0');
                minSpan.textContent = minutes;
                secSpan.textContent = seconds;

                if (msLeft <= 0 && !expired) {
                    expired = true;
                    otpInput.disabled = true;
                    verifyOtpBtn.disabled = true;
                    otpForm.querySelector('button[type="submit"]').disabled = true;
                    otpError.style.display = 'block';
                    otpError.textContent = "OTP expired. Please request a new code.";
                } else if (msLeft > 0) {
                    requestAnimationFrame(updateTimer);
                }
            }

            // Reset on modal close
            document.getElementById('closeOtpModal').onclick = function() {
                expired = true;
                document.getElementById('otpModal').style.display = 'none';
            };

            // Start the timer
            updateTimer();
        }

        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type') === 'success' && urlParams.get('message') && urlParams.get('message').includes('OTP sent')) {
                document.getElementById('otpModal').style.display = 'block';
                document.getElementById('otpEmail').value = localStorage.getItem('otpEmail') || '';
                // Reset OTP form and timer
                document.getElementById('otp').disabled = false;
                document.getElementById('otpForm').querySelector('button[type="submit"]').disabled = false;
                document.getElementById('otpError').style.display = 'none';
                // Fetch expiry from server and start timer
                fetchOtpExpiryAndStartTimer(document.getElementById('otpEmail').value);
            }
            // ...existing code...
        });
    </script>

    <script>
        // Real-time OTP validation in the modal
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            const otpEmail = document.getElementById('otpEmail');
            const otpError = document.getElementById('otpError');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            let lastOtp = "";

            if (otpInput) {
                otpInput.addEventListener('input', function() {
                    const otp = otpInput.value.trim();
                    otpError.style.display = 'none';
                    otpError.textContent = '';
                    otpInput.classList.remove('is-invalid');
                    verifyOtpBtn.disabled = true;

                    // Only check if 6 digits are entered
                    if (otp.length === 6 && /^\d{6}$/.test(otp) && otp !== lastOtp) {
                        lastOtp = otp;
                        // AJAX request to check OTP
                        fetch('includes/ajax_check_otp.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'email=' + encodeURIComponent(otpEmail.value) + '&otp=' + encodeURIComponent(otp)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.valid) {
                                otpError.style.display = 'block';
                                otpError.textContent = 'Incorrect OTP. Please try again.';
                                otpInput.classList.add('is-invalid');
                                verifyOtpBtn.disabled = true;
                            } else {
                                otpError.style.display = 'none';
                                otpError.textContent = '';
                                otpInput.classList.remove('is-invalid');
                                verifyOtpBtn.disabled = false;
                            }
                        })
                        .catch(() => {
                            otpError.style.display = 'block';
                            otpError.textContent = 'Could not verify OTP. Please try again.';
                            otpInput.classList.add('is-invalid');
                            verifyOtpBtn.disabled = true;
                        });
                    } else {
                        verifyOtpBtn.disabled = true;
                    }
                });
            }
        });
    </script>

    <style>
        /* Simple modal styles */
        .modal { position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center; }
        .modal-content { background:#fff; padding:30px; border-radius:8px; position:relative; }
        .close { position:absolute; right:20px; top:10px; }
    </style>
</body>
</html>