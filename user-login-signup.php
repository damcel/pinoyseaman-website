<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>User login & Signup</title>
    <style>
    :root {
    --primary-color: #4EA685;
    --secondary-color: #57B894;
    --black: #000000;
    --white: #ffffff;
    --gray: #efefef;
    --gray-2: #757575;

    --facebook-color: #4267B2;
    --google-color: #DB4437;
    --twitter-color: #1DA1F2;
    --insta-color: #E1306C;
    }

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap');

    * {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        height: 100vh;
        overflow: hidden;
    }

    .container {
        position: relative;
        min-height: 100vh;
        overflow: hidden;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        height: 100vh;
    }

    .col {
        width: 50%;
    }

    .align-items-center {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .form-wrapper {
        width: 100%;
        max-width: 28rem;
    }

    .form {
        padding: 1rem;
        background-color: var(--white);
        border-radius: 1.5rem;
        width: 100%;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        transform: scale(0);
        transition: .5s ease-in-out;
        transition-delay: 1s;
    }

    .input-group {
        position: relative;
        width: 100%;
        margin: 1rem 0;
    }

    .input-group i {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        font-size: 1.4rem;
        color: var(--gray-2);
    }

    .input-group input {
        width: 100%;
        padding: 1rem 3rem;
        font-size: 1rem;
        background-color: var(--gray);
        border-radius: .5rem;
        border: 0.125rem solid var(--white);
        outline: none;
    }

    .input-group input:focus {
        border: 0.125rem solid var(--primary-color);
    }

    .input-group input:focus,
    .input-group select:focus {
        border-color: var(--input-focus-border);
    }

    .date-container {
        display: flex;
        justify-content: space-between;
    }

    .date-input {
        width: 32%;
        padding: 1rem 3rem;
        font-size: 1rem;
        background-color: var(--gray);
        border-radius: .5rem;
        border: 0.125rem solid var(--input-border);
        outline: none;
        box-sizing: border-box;
    }

    .date-input:focus {
        border-color: var(--input-focus-border);
    }


    .form button {
        cursor: pointer;
        width: 100%;
        padding: .6rem 0;
        border-radius: .5rem;
        border: none;
        background-color: #d1d6d8;
        color: var(--white);
        font-size: 1.2rem;
        outline: none;
    }

    .form p {
        margin: 1rem 0;
        font-size: .7rem;
    }

    .flex-col {
        flex-direction: column;
    }


    .pointer {
        cursor: pointer;
    }

    .container.sign-in .form.sign-in,
    .container.sign-in .social-list.sign-in,
    .container.sign-in .social-list.sign-in>div,
    .container.sign-up .form.sign-up,
    .container.sign-up .social-list.sign-up,
    .container.sign-up .social-list.sign-up>div {
        transform: scale(1);
    }

    .content-row {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: 6;
        width: 100%;
    }

    .text {
        margin: 4rem;
        color: var(--white);
    }

    .text h2 {
        font-size: 3rem;
        font-weight: 800;
        margin: 2rem 0;
        margin-left: 20px;
        transition: 1s ease-in-out;
    }

    .text p {
        font-weight: 600;
        transition: 1s ease-in-out;
        transition-delay: .2s;
    }

    .img img {
        width: 30vw;
        transition: 1s ease-in-out;
        transition-delay: .4s;
    }

    .text.sign-in h2,
    .text.sign-in p,
    .img.sign-in img {
        transform: translateX(-250%);
    }

    .text.sign-up h2,
    .text.sign-up p,
    .img.sign-up img {
        transform: translateX(250%);
    }

    .container.sign-in .text.sign-in h2,
    .container.sign-in .text.sign-in p,
    .container.sign-in .img.sign-in img,
    .container.sign-up .text.sign-up h2,
    .container.sign-up .text.sign-up p,
    .container.sign-up .img.sign-up img {
        transform: translateX(0);
    }

    /* BACKGROUND */

    .container::before {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        height: 100vh;
        width: 300vw;
        transform: translate(35%, 0);
        transition: 1s ease-in-out;
        z-index: 6;
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        border-bottom-right-radius: max(50vw, 50vh);
        border-top-left-radius: max(50vw, 50vh);
    }

    .container.sign-in::before {
        transform: translate(0, 0);
        right: 50%;
        background-image: linear-gradient(-45deg, blue 0%, red 100%);
    }

    .container.sign-up::before {
        transform: translate(100%, 0);
        right: 50%;
        background-image: linear-gradient(-45deg, blue 0%, red 100%);
    }

    /* Change sign-in button to red */
    .container.sign-in .form.sign-in button {
    background-color: red;
    }

    /* Change sign-up button to blue */
    .container.sign-up .form.sign-up button {
    background-color: blue;
    }

    /* RESPONSIVE */

    @media only screen and (max-width: 425px) {

        .container::before,
        .container.sign-in::before,
        .container.sign-up::before {
            height: 100vh;
            border-bottom-right-radius: 0;
            border-top-left-radius: 0;
            z-index: 0;
            transform: none;
            right: 0;
        }

        /* .container.sign-in .col.sign-up {
            transform: translateY(100%);
        } */

        .container.sign-in .col.sign-in,
        .container.sign-up .col.sign-up {
            transform: translateY(0);
        }

        .content-row {
            align-items: flex-start !important;
        }

        .content-row .col {
            transform: translateY(0);
            background-color: unset;
        }

        .col {
            width: 100%;
            position: absolute;
            padding: 2rem;
            background-color: var(--white);
            border-top-left-radius: 2rem;
            border-top-right-radius: 2rem;
            transform: translateY(100%);
            transition: 1s ease-in-out;
        }

        .row {
            align-items: flex-end;
            justify-content: flex-end;
        }

        .form,
        .social-list {
            box-shadow: none;
            margin: 0;
            padding: 0;
        }

        .text {
            margin: 0;
        }

        .text p {
            display: none;
        }

        .text h2 {
            margin: .5rem;
            font-size: 2rem;
        }
    }
        
    </style>
</head>
<body>
    <main>
        <div id="container" class="container">
            <!-- FORM SECTION -->
            <div class="row">
                <!-- SIGN UP -->
                <div class="col align-items-center flex-col sign-up">
                    <div class="form-wrapper align-items-center">
                        <form action="includes/seaman_init_reg.php" method="POST" class="form sign-up">
                            <div class="input-group">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="firstname" placeholder="Firstname" required>
                            </div>
                            <div class="input-group">
                                <i class='bx bxs-user'></i>
                                <input type="text" name="lastname" placeholder="Lastname" required>
                            </div>
                            <div class="input-group">
                                <i class='bx bx-mail-send'></i>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="input-group">
                                <i class='bx bx-calendar'></i>
                                <div class="date-container">
                                    <select name="month" class="date-input" required>
                                        <option value="">Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                    <select name="day" class="date-input" required>
                                        <option value="">Day</option>
                                        <?php for ($i = 1; $i <= 31; $i++): ?>
                                            <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <select name="year" class="date-input" required>
                                        <option value="">Year</option>
                                        <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group">
                                <i class='bx bx-phone'></i>
                                <input type="text" name="phone" placeholder="Phone number" required>
                            </div>
                            <div class="input-group">
                                <i class='bx bxs-lock-alt'></i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="input-group">
                                <input type="checkbox" id="view" name="view">
                                <label for="view">Allow Employer to view my profile and include me on manual job search.</label>
                            </div>
                            <!-- <input type="hidden" name="recaptcha_token" id="recaptchaToken"> -->
                            <button type="submit">
                                Sign up
                            </button>
                            <p>
                                <span>
                                    Already have an account?
                                </span>
                                <b onclick="toggle()" class="pointer">
                                    Sign in here
                                </b>
                            </p>
                        </form>
                    </div>
                </div>
                <!-- END SIGN UP -->
                <!-- SIGN IN -->
                <div class="col align-items-center flex-col sign-in">
                    <div class="form-wrapper align-items-center">
                        <form action="includes/seaman_login_verify.php" method="POST" class="form sign-in">
                            <div class="input-group">
                                <i class='bx bxs-user'></i>
                                <input type="email" name="job_seeker_id" placeholder="Email" required>
                            </div>
                            <div class="input-group">
                                <i class='bx bxs-lock-alt'></i>
                                <input type="password" name="job_seeker_password" placeholder="Password" required>
                            </div>
                            <button type="submit">
                                Sign in
                            </button>
                            <p>
                                <b>
                                    Forgot password?
                                </b>
                            </p>
                            <p>
                                <span>
                                    Don't have an account?
                                </span>
                                <b onclick="toggle()" class="pointer">
                                    Sign up here
                                </b>
                            </p>
                        </form>
                    </div>
                </div>
                <!-- END SIGN IN -->
            </div>
            <!-- END FORM SECTION -->
            <!-- CONTENT SECTION -->
            <div class="row content-row">
                <!-- SIGN IN CONTENT -->
                <div class="col align-items-center flex-col">
                    <div class="text sign-in">
                        <h2>
                            Welcome Marino
                        </h2>
                    </div>
                    <div class="img sign-in">
            
                    </div>
                </div>
                <!-- END SIGN IN CONTENT -->
                <!-- SIGN UP CONTENT -->
                <div class="col align-items-center flex-col">
                    <div class="img sign-up">
                    
                    </div>
                    <div class="text sign-up">
                        <h2>
                            Trabahong Seaman, Isang Click nalang
                        </h2>
        
                    </div>
                </div>
                <!-- END SIGN UP CONTENT -->
            </div>
            <!-- END CONTENT SECTION -->
        </div>
    </main>
    <!-- <script src="https://www.google.com/recaptcha/api.js?render=6LcsKjIrAAAAADu-B6dIIu2PdPHt6VHjqvxVFMmt"></script> -->

    <!-- Alert Modal -->
    <div id="alertModal" class="modal hidden">
        <div class="modal-content">
            <h3 id="alertTitle">Notification</h3>
            <p id="alertMessage"></p>
            <button id="closeModal" class="btn">Close</button>
        </div>
    </div>

    <script>
        let container = document.getElementById('container')

        toggle = () => {
            container.classList.toggle('sign-in')
            container.classList.toggle('sign-up')
        }

        setTimeout(() => {
            container.classList.add('sign-in')
        }, 200)

        document.addEventListener('DOMContentLoaded', function () {
            // Function to show the modal
            function showModal(type, message) {
                const modal = document.getElementById('alertModal');
                const modalTitle = document.getElementById('alertTitle');
                const modalMessage = document.getElementById('alertMessage');

                // Set the modal type and message
                modal.classList.remove('success', 'error');
                modal.classList.add(type);
                modalTitle.textContent = type === 'success' ? 'Success' : 'Error';
                modalMessage.textContent = message;

                // Show the modal
                modal.classList.remove('hidden');
                modal.classList.add('visible');
            }

            // Function to hide the modal
            function hideModal() {
                const modal = document.getElementById('alertModal');
                modal.classList.remove('visible');
                modal.classList.add('hidden');
            }

            // Close modal on button click
            document.getElementById('closeModal').addEventListener('click', hideModal);

            // Check for query parameters in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type');
            const message = urlParams.get('message');

            if (type && message) {
                showModal(type, message);
            }

            const form = document.querySelector('form[action="includes/seaman_init_reg.php"]');
            const passwordInput = document.querySelector('input[name="password"]');
            const errorMessage = document.createElement('p');
            errorMessage.style.color = 'red';
            errorMessage.style.fontSize = '0.9rem';
            passwordInput.parentNode.appendChild(errorMessage);

            form.addEventListener('submit', function (e) {
                const password = passwordInput.value;
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

                if (!passwordRegex.test(password)) {
                    e.preventDefault();
                    errorMessage.textContent = 'Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.';
                } else {
                    errorMessage.textContent = '';
                }
            });

        });

        // grecaptcha.ready(function() {
        //     grecaptcha.execute('6LcsKjIrAAAAADu-B6dIIu2PdPHt6VHjqvxVFMmt', {action: 'submit'}).then(function(token) {
        //         document.getElementById('recaptchaToken').value = token;
        //     });
        // });
</script>

    </script>
</body>
</html>