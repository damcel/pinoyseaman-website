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

$code = @$_GET["code"];


$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "delete from employer where code='$code' LIMIT 1" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

$message =  "<font color='blue'>company deleted...</font>";
$link = "company_list_admin.php";
include "./action.php";	
mysqli_close($link);
mysqli_free_result($result);
exit;
?>