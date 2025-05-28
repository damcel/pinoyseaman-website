<?php

error_reporting(E_ALL);
error_reporting (E_STRICT);

$dsn = "mysql:host=127.0.0.1;dbname=pinoysea_pinoyseaman";
$dbusername = "pinoysea_manadmi";
$dbpassword = "j03ld3xs@m";
date_default_timezone_set('Asia/Manila');
$datenow = date("Y-m-d");
$today = date("Y-m-d");
$timenow = date("H:i:s");
$admin_message = "You can now delete your Job Posting";
$seaman_message = "";
$admin_email = "admin@pinoyseaman.com";
$meta = "pinoyseaman, seaman jobs, maritime jobs, online jobs, able seaman, sea careers, marine jobs, deck jobs, cruise jobs, online maritime jobs, tanker, vessel, luxury jobs, ship jobs, jobs, marino";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
