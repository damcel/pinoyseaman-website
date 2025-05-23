<? 

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
$connect = mysql_connect($dbhost,$dbusername,$dbuserpassword);
mysql_select_db($dbname, $connect);
mysql_query("delete from action where action='Company Login Successful'");
mysql_close($connect);
$message = "<font color='blue'>Company Login Successful history deleted.....</font>";
$link = "admin_panel.php";
include "./action.php";
exit;
?>