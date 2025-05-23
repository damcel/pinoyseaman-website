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


?>
<html>
<head>
<?php 
include "./meta.php";
?>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
  <table width="60%" border="0" cellpadding="5" cellspacing="2">
    <tr align="left" valign="top" bgcolor="#FF0000">
      <td width="50%" height="22" bgcolor="#333333"><strong>Date of application</strong></td>
      <td width="50%" bgcolor="#333333"><strong>Applicants per day </strong></td>
    </tr>
  </table>
  <?php  
						if(!isset($offset))
						{
						$offset = 0;
						}
						$rec_per_page = 50;
						$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
						$query2 = "SELECT date from job_applicants where  date != '0000-00-00' and mark = '' and company != '' group by date order by date desc" or die("Error" . mysqli_error($link));
						$query = "SELECT  date from job_applicants where  date != '0000-00-00' and mark = '' and company != '' group by date order by date desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
						$result = mysqli_query($link, $query);
						
						$result2 = mysqli_query($link, $query2);
						$record_count = mysqli_num_rows($result2);
						$page = ceil($record_count / $rec_per_page);
						
						while($row = mysqli_fetch_array($result))
						{
						$date1 = $row["date"];
						$query3 = "SELECT COUNT(*) as counted FROM job_applicants where  date != '0000-00-00' and mark = '' and date='$date1' and company != ''" or die("Error" . mysqli_error($link));
						$result3 = mysqli_query($link, $query3);
						$data = mysqli_fetch_assoc($result3);
						$numguest3 = $data["counted"];
						?>
  <table width="60%" border="0" cellpadding="2" cellspacing="2">
    <tr align="left" valign="top" bgcolor="#F5F5F5">
      <td width="50%" class="style7"><?php echo $date1;?></td>
      <td width="50%" ><font color="000000"><?php echo $numguest3; ?></font></td>
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
    <a href="employer_applicant_date1.php?offset=<?php echo (int)$ctr;?>">
    <?php if($label == $offset+1)
			  {
			  echo '<font size=2 color=red>'.$label.'</font>'; 
			  }
			  else
			  {
			  echo $label; 
			  }
			  ?>
    </a> <span class="style7">|</span>
    <?php $ctr++;
			   }
			   echo "<br /><br /><br />";
			   ?>
    </span><br />
    <br>
    <font color="#000000"><strong>Total number of applicants :</strong>
    <?php
							  $query = "SELECT COUNT(*) as counted1 FROM job_applicants where company != ''" or die("Error" . mysqli_error($link));
						$result = mysqli_query($link, $query); 
						$data = mysqli_fetch_assoc($result);
						echo $data["counted1"];	
						?>
  </font><br>
  <br>
  <br>
  <a href="admin_panel.php">Back to Admin Page </a></p>
</div>
</body>
</html>
