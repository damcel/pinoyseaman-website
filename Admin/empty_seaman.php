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
$query = "delete from action where action='Seaman Login Failed'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

mysqli_close($link);
mysqli_free_result($result);

$message = "<font color='blue'>deleted...</font>";
$link = "admin_panel.php";
include "./action.php";
exit;







?>