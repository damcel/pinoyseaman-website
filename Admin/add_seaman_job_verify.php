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



$admin = $_SESSION["admin_account"];



$category = @$_POST["category"];

if($category == "Choose Below")
{ 
$link = "add_seaman_job.php";
$message =  "<font color='red'>enter new Job Category!</font>";
include "./action.php"; 
exit;
}

if($category == "-")
{ 
$link = "add_seaman_job.php";
$message =  "<font color='red'>Select Job Category!</font>";
include "./action.php"; 
exit;
}

$job = @$_POST["job"];

if($job == "")
{ 
$link = "add_seaman_job.php";
$message =  "<font color='red'>Enter new Job!</font>";
include "./action.php"; 
exit;
}


$type = @$_POST["type"];

include "./connect.php";

$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "SELECT * from seaman_jobs where category='$category' and job='$job'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result);

if($row == 0)
  {
	  $query = "insert into seaman_jobs (category,job,admin,type) values('$category','$job','$admin','$type')" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
 
  $link = "add_seaman_job.php";
  $message =  "<font color='blue'>New Job Category / Job Title posted...</font>";
  include "./action.php";
  mysqli_close($link);
  mysqli_free_result($result); 
  exit;
  }
  else
  { 
  
  $link = "add_seaman_job.php";
  $message =  "<font color='red'>Duplicate job category / job!</font>";
  include "./action.php";
  mysqli_close($link);
  mysqli_free_result($result); 
  exit;
  }
?>