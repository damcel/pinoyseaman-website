<?php
$servername = "localhost"; // Change if your database server is not localhost
$username = "pinoysea_manadmi"; // Replace with your MySQL username
$password = "j03ld3xs@m"; // Replace with your MySQL password
$database = "pinoysea_pinoyseaman"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Connection successful, no action needed
?>