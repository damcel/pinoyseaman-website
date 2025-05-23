<?php  
session_start();
include "./connect.php";
$ctr = 0;

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

$date_start = $_POST["date_start"];
$date_end = $_POST["date_end"];

$offset = $_GET["offset"];
?>
<html>
<head>
<?php 
include "./meta.php";
?>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form action="admin_display_applicant_job_date_excel.php?date_start=<?php echo $date_start; ?>&date_end=<?php echo $date_end;?>" method="post">
<table width="80%" border="0" align="center" cellpadding="10" cellspacing="10">
  <tr>
    <td><table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="20%" bgcolor="#305067" ><strong>Date of Application</strong></td>
          <td width="40%" bgcolor="#305067" ><strong>Job Position</strong></td>
          <td width="20%" bgcolor="#305067" ><strong>Number of Applicant</strong></td>
        </tr>
      </table>
      <?php 
			  if(!isset($offset))
				{
				$offset = 0;
				}
				$rec_per_page = 100;
				$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
				$query2 = "SELECT date,job_hiring from job_applicants where  date between '$date_start' and '$date_end' and job_hiring!='' and mark='' group by date,job_hiring ORDER BY date DESC" or die("Error" . mysqli_error($link));
				$query = "SELECT date,job_hiring from job_applicants where   date between '$date_start' and '$date_end' and job_hiring!='' and mark='' group by date,job_hiring ORDER BY date DESC LIMIT " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
				
				$result = mysqli_query($link, $query);
				$result2 = mysqli_query($link, $query2);
				$record_count = mysqli_num_rows($result2);
				$page = ceil($record_count / $rec_per_page);
				
				while($row = mysqli_fetch_array($result))
				{
					$date1 = $row['date'];
					$job_hiring = $row['job_hiring'];
					
					$query3 = "SELECT COUNT(*) AS count2 FROM job_applicants where  date between '$date_start' and '$date_end' and job_hiring='$job_hiring' and mark='' and date='$date1'" or die("Error" . mysqli_error($link));
					$result5 = mysqli_query($link, $query3);
					$data = mysqli_fetch_assoc($result5);
					?>
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr align="left" valign="top">
          <td width="20%" bgcolor="#F5F3F3"><span class="black1">
            <?php  echo $date1;?>
            </span></td>
          <td width="40%" bgcolor="#F5F3F3"><span class="black1">
            <?php  echo $row['job_hiring']; ?>
            </span></td>
          <td width="20%" bgcolor="#F5F3F3"><span class="black1">
            <?php  echo $data['count2']; ?>
            </span></td>
        </tr>
      </table>
      <?php
	  }
	  echo "<br /><font color=black>You are viewing page ";
	  echo  $offset + 1  . " of $page <br /><br /> Page &nbsp;</font>";
	  for($i=0;$i<$page;$i++)
	  {
		  $label = $i+1;
		  ?>
      <a href="admin_display_applicant_job_date_next.php?offset=<?php  echo (int)$ctr;?>&date_start=<?php echo $date_start; ?>&date_end=<?php echo $date_end;?>">
      <?php
						 if($label == $offset+1)
						 {
						 echo '<font size=2>'.$label.'</font>';
						 }
						 else
						 {
						 echo $label;
						 }
						 ?>
      </a><span class="style7">|</span>
      <?php 
						 $ctr++;
						 }
						 echo "<br />";
						 ?>
      <br />
      <br />
      <br />
      <br />
      <font color="#000000"><strong>Total Applicants :</strong>
      <?php
		$query4 = "SELECT COUNT(*) as counted1 FROM job_applicants where date between '$date_start' and '$date_end' and job_hiring!='' and mark=''" or die("Error" . mysqli_error($link));
		$result = mysqli_query($link, $query4); 
		$data = mysqli_fetch_assoc($result);
		echo $data["counted1"];
		mysqli_close($link);
		mysqli_free_result($result);
		mysqli_free_result($result2);
		?>
      </font><br />
      <br />
      <input type="submit" name="export_excel" class="btn btn-success" value="Export to Excel">
      <br />
      <br />
      <center>
        <a href="javascript:window.close();">close this  page</a>
      </center></td>
  </tr>
</table>
</body>
</html>
