<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // $recaptchaSecret = '6LcsKjIrAAAAAKLKhlob34wEVJxNK2nf9fZ8Fqam';
    // $recaptchaToken = $_POST['recaptcha_token'];

    // $recaptchaResponse = file_get_contents(
    //     "https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaToken"
    // );
    // $responseData = json_decode($recaptchaResponse);

    // if (!$responseData->success || $responseData->score < 0.5) {
    //     // Score too low or failed
    //     header("Location: ../user-login-signup.php?type=error&message=reCAPTCHA verification failed. Please try again.");
    //     exit;
    // }

    // Retrieving form data
    $first_name = trim($_POST["firstname"]);
    $last_name = trim($_POST["lastname"]);
    $cellphone = trim($_POST["phone"]);
    $month = trim($_POST["month"]);
    $day = trim($_POST["day"]);
    $year = trim($_POST["year"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $view = isset($_POST["view"]) ? 'y' : 'n'; // Checkbox value

    // Combine month, day, and year into a valid date format
    $birthday = date("Y-m-d", strtotime("$year-$month-$day"));

    // Normalize names
    $first_name = ucwords(strtolower($first_name));
    $last_name = ucwords(strtolower($last_name));

    // Generate unique ID and hashed password
    function generateID($length) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    $newid = generateID(8);

    // Validate password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        header("Location: ../index.php?type=error&message=Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.");
        exit;
    }

    $newpassword = md5($password); 

    try {
        require_once "../db.php";

        // Check if email already exists
        $checkQuery = "SELECT COUNT(*) FROM job_seeker WHERE email = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->bind_result($recordExists);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($recordExists > 0) {
            header("Location: ../index.php?type=error&message=This email is already registered.");
            exit;
        }

        // Insert data into the database
        $query = "INSERT INTO job_seeker (id, first_name, last_name, birthday, email, cellphone, password, view, date) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $newid, $first_name, $last_name, $birthday, $email, $cellphone, $newpassword, $view);
        $stmt->execute();
        $stmt->close();

        // Insert action into the action table
        $actionQuery = "INSERT INTO action (date, action, seaman, time) VALUES (CURDATE(), 'Seaman Initial Registration', ?, CURTIME())";
        $actionStmt = $conn->prepare($actionQuery);
        $actionStmt->bind_param("s", $email);
        $actionStmt->execute();
        $actionStmt->close();

        $pdo = null;
        $stmt = null;

        
        // Send email using PHPMailer
        // $mail = new PHPMailer(true);

        // try {
            
        //     // $mail->isMail();

        //     $mail->isSMTP();
        //     $mail->Host = 'smtp-relay.brevo.com';
        //     $mail->SMTPAuth = true;
        //     $mail->Username = '88d0c6002@smtp-brevo.com';
        //     $mail->Password = 'ARnazdkms0EcBHVC';
        //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //     $mail->Port = 587;


        //     // Sender and recipient settings
        //     $mail->setFrom('noreply@pinoyseaman.com', 'PinoySeaman');
            
        //     $mail->addAddress($email, $first_name);

        //     // Email content
        //     $mail->isHTML(true);
        //     $mail->Subject = 'Welcome to PinoySeaman!';
        //     $mail->Body = "
        //         <p>Hello Seafarer!</p>
        //         <p>Welcome to PinoySeaman! Your account has been created successfully.</p>
        //         <p>Your Name: <strong>$first_name $last_name</strong></p>
        //         <p>Your email: <strong>$email</strong></p>
        //         <p>Please login to your account and complete your information right away, Thank you!.</p>
        //         <br>
        //         <p>Note: PinoySeaman does not collect any fees for registration or job applications.</p>
        //         <p>If you have any questions, feel free to reach out to us at filoseaman@gmail.com.</p>";

        //     $mail->send();

        //     // Notify admin
        //     $mail->clearAddresses();
        //     $mail->addAddress('admin@pinoyseaman.com');
        //     $mail->Subject = 'New Seaman Registration';
        //     $mail->Body = "
        //         <p>A new seaman has registered on PinoySeaman:</p>
        //         <p>Name : $first_name $last_name</p>
        //         <p>Birthdate : $birthday</p>
        //         <p>Email : $email</p>
        //         <p>PinoySeaman ID : $newid</p>";

        //     $mail->send();

        //     header("Location: ../index.php?type=success&message=Registration successful! A confirmation email has been sent.");
        //     exit;
        // } catch (Exception $e) {
        //     header("Location: ../index.php?type=error&message=Registration successful, but email sending failed: {$mail->ErrorInfo}");
        //     exit;
        // }

        header("Location: ../user-login-signup.php?type=success&message=Registration successful!");
        exit;
    } catch (PDOException $e) {
        header("Location: ../user-login-signup.php?type=error&message=Error: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: ../user-login-signup.php?type=error&message=Invalid request method.");
    exit;
}
