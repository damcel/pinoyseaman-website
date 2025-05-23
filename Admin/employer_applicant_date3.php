<?php 
session_start();
include "./connect.php";
$ctr = 0;
$totala = 0;


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

$offset = $_GET["offset"];

$date_start = $_POST["date_start"];
$date_end = $_POST["date_end"];

?>
<html>
<head>
<?php 
include "./meta.php";
?>
<style type="text/css">
<!--
.style2 {color: #000000}
.style4 {color: #000000; font-weight: bold; }
-->
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
  <br>
  <table width="70%" border="0" cellspacing="2" cellpadding="3">
    <tr>
      <td width="14%"><span class="black1"><strong>Start Date :</strong><br />
      </span></td>
      <td width="86%"><span class="black1"><?php echo $date_start; ?></span></td>
    </tr>
    <tr>
      <td><span class="black1"><strong>End Date :</strong></span></td>
      <td><span class="black1"><?php echo $date_end; ?></span></td>
    </tr>
  </table>
  <br />
  <table width="70%" border="0" cellpadding="5" cellspacing="2">
  <tr align="left" valign="top" bgcolor="#FF0000">
    <td width="74%" height="22" bgcolor="#333333"><strong>Company</strong></td>
        <td width="26%" bgcolor="#333333"><strong>Number of Applicants </strong></td>
    </tr>
  </table>
    <?php
	$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
	$query = "SELECT company from job_applicants where  mark='' and company !=''  and date between '$date_start' and '$date_end' group by company" or die("Error" . mysqli_error($link));
	$result = mysqli_query($link, $query);
	
	while($row = mysqli_fetch_array($result))
	{
	$company = $row["company"];
	$query3 = "SELECT COUNT(*) as counted FROM job_applicants where company='$company' and mark='' and date between '$date_start' and '$date_end'" or die("Error" . mysqli_error($link));
	$result3 = mysqli_query($link, $query3);
	$data = mysqli_fetch_assoc($result3);
	$numguest3 = $data["counted"];
	$numguest4 = $numguest4 + $numguest3
	?>
  <table width="70%" border="0" cellpadding="2" cellspacing="2">
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td width="74%" class="black1"><?php
	  $company = str_replace("^", "'", $company);
	  $company = str_replace("*", "&", $company);
	  echo $company;?></td>
        <td width="26%" ><font color="000000"><?php echo $numguest3; ?></font></td>
    </tr>
  </table>
      <?php
	  }
	  mysqli_close($link);
      mysqli_free_result($result);
      mysqli_free_result($result3);
	  ?>
	  </span>
  <br />
  </font>
  <table width="70%" border="0" cellpadding="2" cellspacing="2">
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td width="74%" class="black1"><div align="right"><strong>Total Applicants : </strong></div></td>
      <td width="26%" class="black1"><? echo  $numguest4; ?></font></td>
    </tr>
  </table>
  <br />
  <br />
  <br />
  <a href="admin_panel.php">Back to Admin Page </a><br />
<br />
<br />

</div></body>
</html>
