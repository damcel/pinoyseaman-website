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



$sw = $_GET["sw"];

if($sw <> 1)
{
$offset = $_GET["offset"];
$date = $_POST["date2"];
$date2 = $date;
}
else
{
$date = $_GET["date"];
$offset = $_GET["offset"];
}

?>
<html>
<head>
<?php 
include "./meta.php";
?>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br>
<br>
<div align="center">
  <table width="70%" border="0" cellpadding="5" cellspacing="2">
    <tr align="left" valign="top">
      <td height="22" class="black1"><strong>Date : </strong><?php echo $date;?></td>
    </tr>
  </table>
  <br>
  <table width="70%" border="0" cellpadding="5" cellspacing="2">
    <tr align="left" valign="top" bgcolor="#FF0000">
      <td width="64%" height="22" bgcolor="#333333"><strong>Company</strong></td>
      <td width="36%" bgcolor="#333333"><strong>Total Applicants </strong></td>
    </tr>
  </table>
  <?php
  if(!isset($offset))
  {
	  $offset = 0;
	  }
	  $rec_per_page = 50;
	  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
	  $result = mysqli_query($link, $query);
	  
	  $query2 = "SELECT company FROM job_applicants WHERE  mark='' and company !='' and date='$date' GROUP BY company" or die("Error" . mysqli_error($link));
	  $query = "SELECT company FROM job_applicants WHERE mark='' and company !='' and date='$date' GROUP BY company LIMIT " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
	  $result = mysqli_query($link, $query);
	  $result2 = mysqli_query($link, $query2);
	  
	  $record_count = mysqli_num_rows($result2);
	  $page = ceil($record_count / $rec_per_page);
	  
	  while($row = mysqli_fetch_array($result))
	  {
		  $company = $row["company"];
		  $query3 = "SELECT COUNT(*) as counted FROM job_applicants WHERE company='$company' and mark='' and date='$date'" or die("Error" . mysqli_error($link));
		  $result3 = mysqli_query($link, $query3);
		  $data = mysqli_fetch_assoc($result3);
		  $numguest3 = $data["counted"];
		  $numguest4 = $numguest4 + $numguest3;
		  ?>
  <table width="70%" border="0" cellpadding="2" cellspacing="2">
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td width="64%" class="black1"><?php
	  $company = str_replace("*", "&", $company);
	   echo $company;?></td>
      <td width="36%" ><font color="000000"><?php echo $numguest3; ?></font></td>
    </tr>
  </table>
    <?php 
			}
			 echo "<br /><font color='#000000'>You are viewing page ";
			  echo  $offset + 1  . " of $page <br /><br /> Page &nbsp; </font>";
			  for($i=0;$i<$page;$i++)
			  {
			  $label = $i+1;
			  ?>
    <a href="employer_applicant_date2.php?offset=<?php echo (int)$ctr;?>&sw=1&date=<?php echo $date;?>">
    <?php if($label == $offset+1)
			  {
			  echo '<font size=2 color=red>'.$label.'</font>'; 
			  }
			  else
			  {
			  echo $label; 
			  }
			  ?>
    </a> <font color = "#000000">|</font>
    <?php $ctr++;
			   }
			   echo "<br />";
			   ?>
    </span>
	<br />
    <br />
  <font color="#000000">
  Total Applicants : <?php echo  $numguest4; 
  mysqli_close($link);
  mysqli_free_result($result);
  mysqli_free_result($result2);
  ?>
  </font><br>
  <br>
  <br>
  <br>
  <a href="admin_panel.php">Back to Admin Page </a></p>
</div>
</body>
</html>
