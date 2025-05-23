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




include "./connect.php";	



$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "delete from action where action='Seaman Login Successful'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

$message = "<font color='blue'>deleted...</font>";
$link = "admin_panel.php";
include "./action.php";
exit;
?>