<?php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted via POST
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
    $apiKey = 'YOUR_BREVO_API_KEY'; // Replace with your Brevo API key

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