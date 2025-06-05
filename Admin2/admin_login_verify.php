<?php 
session_start();
include "./connect.php";  

$username = $_POST["username"];
$username = str_replace("'", "", $username); 
$username = htmlspecialchars($username);
$username = stripslashes($username);
$username = str_replace("=", "", $username);
$username = str_replace(";", "", $username); 

if($username == "")
{ 
$username_error = "Required!";
$sw = 1;
}

// Employer Password
$password = $_POST["password"];
$password = str_replace("'", "", $password); 
$password = htmlspecialchars($password);
$password = stripslashes($password);
$password = str_replace("=", "", $password);
$password = str_replace(";", "", $password);


if($username == "")
{ 
$password_error = "Required!";
$sw = 1;
}


if ($sw == 1)
{
include "./admin.php";
exit;
}



$password = md5($password);



$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "SELECT * from admin where username='$username' and password='$password'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
while($row = mysqli_fetch_array($result))
{
	$_SESSION["admin_account"] = $username;
	$_SESSION["admin_pass"] = $password;
	
	$message = "<font color='blue'>Login successful, please wait while transferring you to Admin Page</font>";
	$link = "admin-panel-verification.php";
	include "./action.php";
	mysqli_close($link);
	mysqli_free_result($result);
	exit;

}
    
	$message = "<font color='red'>Login error!!! your IP address is recorded...</font>";
	$link = "admin-panel-verification.php";
	include "./action.php";
	mysqli_close($link);
	mysqli_free_result($result);
	exit;


?>