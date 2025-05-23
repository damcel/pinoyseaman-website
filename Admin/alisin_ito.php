<?php session_start();
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

$code = $_GET['code'];
include "./connect.php";			  
$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "delete from job_seeker where code='$code' LIMIT 1" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
mysqli_close($link);
mysqli_free_result($result);

$link = "seaman_listing.php";
$message =  "<font color='blue'>applicant deleted...</font>";
include "./delete_action.php"; 
exit;
?>