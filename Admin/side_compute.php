<?php
session_start();

include "./connect.php";

if(!isset($_SESSION["admin_account"]))
{
header("location: admin.php");
exit;
} 

if(!isset($_SESSION["admin_pass"]))
{
header("location: admin.php");
exit;
}

$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));

$query = "SELECT count(*) as seaman_total from  job_seeker" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
$data = mysqli_fetch_assoc($result);
$seaman_total = $data["seaman_total"];

$query = "SELECT count(*) as employer_total from  employer" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
$data = mysqli_fetch_assoc($result);
$employer_total = $data["employer_total"];

$query = "SELECT count(*) as job_total from  jobs" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
$data = mysqli_fetch_assoc($result);
$job_total = $data["job_total"];

$query = "update pinoystats set seaman='$seaman_total',employer='$employer_total',jobs='$job_total'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

mysqli_close($link);
mysqli_free_result($result);

$link = "admin_panel.php";
$message =  "<font color='blue'>updated...</font>";
include "./action.php"; 
exit;
?>