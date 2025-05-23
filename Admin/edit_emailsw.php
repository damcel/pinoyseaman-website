<?php
session_start();

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


//password
$emailcode= @$_POST["emailcode"];

include "./connect.php";
$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "update coding set  code='$emailcode'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

$message =  "<font color='blue'>updated.. </font>";
$link = "admin_panel.php";
include "./action.php";	
mysqli_close($link);
mysqli_free_result($result);
exit;
?>