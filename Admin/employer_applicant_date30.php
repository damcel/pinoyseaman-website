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

$employer_id = $_SESSION["employer_login"];
$employer_password = $_SESSION["employer_pass"];

$date_start = $_POST["date_start1"];
$date_end = $_POST["date_end1"];
$company_code = $_POST["company_code"];

?>
<html>
<head>
<?php 
include "./meta.php";
?>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
<?php
$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));						

$query = "SELECT COUNT(*) as counted FROM job_applicants where company='$company_code' and mark='' and date between '$date_start' and '$date_end'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
$data = mysqli_fetch_assoc($result);
$numguest3 = $data["counted"];
mysqli_close($link);
mysqli_free_result($result);
?>

<br />
  <table width="70%" border="0" cellpadding="2" cellspacing="5">
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td width="30%" class="black1">Company : </td>
      <td width="70%" ><font color="000000"><?php echo $company_code; ?></font></td>
    </tr>
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td class="black1">Start Date : </td>
      <td width="70%" ><font color="000000"><?php echo $date_start; ?></font></td>
    </tr>
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td class="black1">End Date : </td>
      <td ><font color="000000"><?php echo $date_end; ?></font></td>
    </tr>
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td class="black1">Total Applicants : </td>
      <td ><font color="#000000"><?php echo  $numguest3; ?></font></td>
    </tr>
  </table>
  <br>
  <br>
  <br>
  <br>
  <a href="admin_panel.php">Back to Admin Page </a><br>
<br>
<br>

</div>
</body>
</html>
