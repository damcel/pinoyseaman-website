<?php
error_reporting(E_ALL);
error_reporting(E_STRICT);

// Define all required DB variables for mysqli_connect()
$dbhost = "127.0.0.1";
$dbusername = "root";
$dbuserpassword = "";
$dbname = "pinoysea_pinoyseaman";

// Also define the DSN if PDO is ever used
$dsn = "mysql:host=$dbhost;dbname=$dbname";

date_default_timezone_set('Asia/Manila');

$datenow = date("Y-m-d");
$today = date("Y-m-d");
$timenow = date("H:i:s");
$admin_message = "You can now delete your Job Posting";
$seaman_message = "";
$admin_email = "admin@pinoyseaman.com";
$meta = "pinoyseaman, seaman jobs, maritime jobs, online jobs, able seaman, sea careers, marine jobs, deck jobs, cruise jobs, online maritime jobs, tanker, vessel, luxury jobs, ship jobs, jobs, marino";
