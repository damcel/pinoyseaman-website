<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form inputs
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $senderEmail = trim($_POST['sender_email'] ?? '');
    $senderMessage = trim($_POST['sender_message'] ?? '');

    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($senderEmail) || empty($senderMessage)) {
        header("Location: ../contact-us.php?type=error&message=All fields are required.");
        exit;
    }

    if (!filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../contact-us.php?type=error&message=Invalid email address.");
        exit;
    }

    // Validate reCAPTCHA
    if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
        header("Location: ../contact-us.php?type=error&message=Please complete the reCAPTCHA.");
        exit;
    }

    $secretKey = '6LfxHEYrAAAAAMGdNwguvozZ3Su4uXrXOtvOmef1';
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
    $responseData = json_decode($verifyResponse);

    if (!$responseData || !$responseData->success) {
        header("Location: ../contact-us.php?type=error&message=Recaptcha validation failed. Please try again.");
        exit;
    }

    // Prepare email content
    $subject = "Message from $firstName $lastName";
    $htmlContent = "
        <h2>New Message</h2>
        <p><strong>Name:</strong> {$firstName} {$lastName}</p>
        <p><strong>Email:</strong> {$senderEmail}</p>
        <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($senderMessage)) . "</p>
        <hr>
        <p>This message was sent from the PinoySeaman.com contact us form.</p>
    ";
    $textContent = "New Message\n\n"
        . "Name: {$firstName} {$lastName}\n"
        . "Email: {$senderEmail}\n"
        . "Message:\n{$senderMessage}\n\n"
        . "This message was sent from the PinoySeaman.com contact us form.";

    // Brevo API setup
    $apiKey = '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'; // Replace with your Brevo API key

    $brevoData = [
        "sender" => [
            "name" => "PinoySeaman Contact Form",
            "email" => "noreply@pinoyseaman.com"
        ],
        "to" => [
            [
                "email" => "admin@pinoyseaman.com",
                "name" => "PinoySeaman Admin"
            ]
        ],
        "replyTo" => [
            "email" => $senderEmail,
            "name" => "{$firstName} {$lastName}"
        ],
        "subject" => $subject,
        "htmlContent" => $htmlContent,
        "textContent" => $textContent
    ];

    // Send email via Brevo API
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
        header("Location: ../contact-us.php?type=success&message=Thank you for contacting us! We have received your message.");
        exit;
    } else {
        header("Location: ../contact-us.php?type=error&message=Failed to send your message. Please try again later.");
        exit;
    }
} else {
    header("Location: ../contact-us.php?type=error&message=Invalid request.");
    exit;
}