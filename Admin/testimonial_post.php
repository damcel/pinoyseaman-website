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

$postcode = $_GET["code"];
$modified_by = $_SESSION["admin_account"];


include "./connect.php";	
$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "update testimonials set post = 'y' , modified_by = '$modified_by' where code= '$postcode'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);

include "./testimonials_admin.php";
mysqli_close($link);
mysqli_free_result($result);
exit;
?>