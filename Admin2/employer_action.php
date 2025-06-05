<?php
include 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyCode = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (!$companyCode || !$action) {
        http_response_code(400);
        echo "Missing required data";
        exit;
    }

    $companyCode = mysqli_real_escape_string($link, $companyCode);

    if ($action === 'approve') {
        // Update employer to verified
        $query = "UPDATE employer SET verify = 'y' WHERE company_code = '$companyCode'";
        if (mysqli_query($link, $query)) {

            // Fetch email and password for that employer
            $result = mysqli_query($link, "SELECT email, secret FROM employer WHERE company_code = '$companyCode'");
            if ($row = mysqli_fetch_assoc($result)) {
                $email = $row['email'];
                $secret = $row['secret']; // password is plain
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
                $apiKey = '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'; // Replace with your real API key

                $brevoData = [
                    "sender" => [
                        "name" => "PinoySeaman",
                        "email" => "noreply@pinoyseaman.com"
                    ],
                    "to" => [
                        [
                            "email" => $email
                        ]
                    ],
                    "subject" => "Your PinoySeaman.com Account is Verified",
                    "htmlContent" => $email_message,
                    "textContent" => "Congratulations! Your company account has been verified and is now active on PinoySeaman.com.\n\nCompany Email: $email\nPassword: $secret\n\nYou can now log in and start posting jobs: http://www.pinoyseaman.com/employer-login-signup.php"
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

                // Optional: Log or handle response
                if ($httpCode === 201) {
                    echo "approved";
                } else {
                    error_log("Brevo Email Error: $curlError\nResponse: $response");
                    echo "approved (email failed)";
                }
            } else {
                echo "approved (email info not found)";
            }
        } else {
            echo "error";
        }
    } elseif ($action === 'decline') {
        $query = "DELETE FROM employer WHERE company_code = '$companyCode'";
        if (mysqli_query($link, $query)) {
            echo "declined";
        } else {
            echo "error";
        }
    } else {
        http_response_code(400);
        echo "Invalid action";
    }
} else {
    http_response_code(405); 
    echo "Invalid request";
}
