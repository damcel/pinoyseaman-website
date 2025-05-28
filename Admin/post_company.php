<?php
session_start();
include "./connect.php";

// Check if admin is logged in
if (!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
    header("location: admin.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Get the company code from the URL
$code = $_GET["code"];
$modified_by = $_SESSION["admin_account"];
$today = date("Y-m-d H:i:s");

// Connect to the database
$link = mysqli_connect($dbhost, $dbusername, $dbuserpassword, $dbname) or die("Error connecting to database: " . mysqli_error($link));

// Fetch employer details using prepared statements
$query = "SELECT * FROM employer WHERE code=? AND verify = ''";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "s", $code);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_array($result);

if (!$row) {
    die("Error: Company not found or already activated.");
}

$id = $row["id"];
$pass = $row["password"];
$email = $row["email"];
$secret = $row["secret"];
$contact = $row["contact"];
$company = $row["company"];
$company_code = $row["company_code"];

// Update employer record: set post='y', verify='y'
$query = "UPDATE employer SET verify='y', date_modified=?, modified_by=?, action='REGISTRATION' WHERE code=? AND post=' '";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "sss", $today, $modified_by, $code);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) === 0) {
    die("Error updating record: " . mysqli_error($link));
}

// Prepare email message
$email_message = "
<font face='verdana' size='2'>
Congratulations! Your company account has been <b>verified</b> and is now active on PinoySeaman.com.<br><br>
Your login details below:<br>
Company Email: $email<br>
Password: $secret<br><br>
You can now log in and start posting jobs.<br>
<a href='http://www.pinoyseaman.com/employer-login-signup.php'>Login here</a><br>
</font>";

// Send email using Brevo API
$apiKey = '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'; // Replace with your Brevo API key

$brevoData = [
    "sender" => [
        "name" => "PinoySeaman",
        "email" => "noreply@pinoyseaman.com"
    ],
    "to" => [
        [
            "email" => $email
            // "name" => $contact
        ]
    ],
    "subject" => "Your PinoySeaman.com Account is Verified",
    "htmlContent" => $email_message,
    "textContent" => "Congratulations! Your company account has been verified and is now active on PinoySeaman.com.\n\nCompany ID: $id\nPassword: $secret\n\nYou can now log in and start posting jobs: http://www.pinoyseaman.com/employer-login.php"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "accept: application/json",
    "api-key: $apiKey",
    "content-type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($brevoData));
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    $message = "<font color='blue'>Done, company verified and email sent via Brevo.</font>";
} else {
    $message = "<font color='red'>Error sending email via Brevo. Response: $response $curlError</font>";
}

// Redirect to company list
$link = "company_list_admin.php";
include "./action.php";

// Close database connection
mysqli_close($link);
mysqli_free_result($result);
exit;
?>