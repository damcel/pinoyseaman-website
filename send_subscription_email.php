<?php
session_name("employerSession");
session_start();

if (!isset($_SESSION['employer_email'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

include 'db.php';

$employerEmail = $_SESSION['employer_email'];
$query = "SELECT * FROM employer WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $employerEmail);
$stmt->execute();
$result = $stmt->get_result();
$employer = $result->fetch_assoc();

$company = $employer['company'] ?? 'Unknown';
$phone = $employer['phone'] ?? 'Unknown';
$plan = $_SESSION['selected_plan'] ?? 'none';

$planDetails = [
    'monthly' => 'Monthly Subscription',
    'yearly' => '1 Year Package (50% Discount)',
];
$planName = $planDetails[$plan] ?? 'No Plan Selected';

$apiKey = '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'; // Replace with your Brevo API key

$data = [
    "sender" => ["name" => "PinoySeaman", "email" => "noreply@pinoyseaman.com"],
    "to" => [["email" => "admin@pinoyseaman.com", "name" => "Admin"]],
    "subject" => "New Employer Subscription",
    "htmlContent" => "
        <html>
        <body>
            <h3>New Subscription Notice</h3>
            <p>An employer has just availed a subscription package.</p>
            <ul>
                <li><strong>Company:</strong> {$company}</li>
                <li><strong>Email:</strong> {$employerEmail}</li>
                <li><strong>Phone:</strong> {$phone}</li>
                <li><strong>Subscription Type:</strong> {$planName}</li>
            </ul>
            <p>Please check your email for the receipt sent by the employer.</p>
        </body>
        </html>
    "
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "api-key: $apiKey",
    "Content-Type: application/json",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    echo "success";
} else {
    echo "Failed to send email. Response: $response";
}
