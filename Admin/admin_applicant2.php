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

$offset = $_GET["offset"];
$company_code = $_GET["company_code"];
echo $company_code;

?>
<html>
<head>
<?php
include "./meta.php";
?>
<style type="text/css">
<!--
.style1 {color: #FF0000}
.style6 {color: #FFFFFF;
	font-weight: bold;
}
.style71 {font-size: 9px; color: #000000; }
-->
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="60%" border="0" align="center" cellpadding="5" cellspacing="5">
  <tr>
    <td><table width="100%" border="0" cellpadding="4" cellspacing="2">
      <tr align="left" valign="top" bgcolor="#F5F5F5">
        <td width="38%" bgcolor="#305067" ><strong>Date of Application</strong></td>
        <td width="33%" bgcolor="#305067" ><strong>Number of Applicant</strong></td>
        </tr>
    </table>
      <span class="style7">
      <?php
	  if(!isset($offset))
	  {
		  $offset = 0;
		  }
		  $rec_per_page = 50;
		  
		  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
		  $query2 = "SELECT date from job_applicants where company_code='$company_code' and date !='0000-00-00' and mark='' group by date order by date desc" or die("Error" . mysqli_error($link));
		  $query = "SELECT date from job_applicants where company_code='$company_code' and date !='0000-00-00' and mark='' group by date order by date desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
		  $result = mysqli_query($link, $query);
		  $result2 = mysqli_query($link, $query2);
		  $record_count = mysqli_num_rows($result2);
		  $page = ceil($record_count / $rec_per_page);
		  
		  while($row = mysqli_fetch_array($result))
		  {
			  $date1 = $row["date"];
			  $query3 = "SELECT COUNT(*) as counted FROM job_applicants where  company_code='$company_code' and date !='0000-00-00' and mark='' and date='$date1'" or die("Error" . mysqli_error($link));
			  $result3 = mysqli_query($link, $query3);
			  $data = mysqli_fetch_assoc($result3);
			  $numguest3 = $data["counted"];
			  ?>
      </span>
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="38%" class="style7"><a href="admin_applicant3.php?date=<?php echo $date1;?>&company_code=<?php echo $company_code;?>" target="windowName"
   onClick="window.open(this.href,this.target,'width=800,height=500,scrollbars=yes');
            return false;"><?php echo $date1;?></a></td>
          <td width="33%" ><font color="000000"><?php echo $numguest3; ?></font></td>
        </tr>
      </table>
      <?php 
			}
			 echo "<br /><font color='#000000'>You are viewing page ";
			  echo  $offset + 1  . " of $page <br /> Page &nbsp; </font>";
			  for($i=0;$i<$page;$i++)
			  {
			  $label = $i+1;
			  ?>
      <a href="admin_applicant2.php?offset=<?php echo (int)$ctr;?>&company_code=<?php echo $company_code;?>">
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
			   echo "<br />";
			   ?>
      </span><br />
      <font color="#000000"><strong>Total number of applicants :</strong>
      <?php
			$query = "SELECT COUNT(*) as counted1 FROM job_applicants where  company_code='$company_code'" or die("Error" . mysqli_error($link));
			$result = mysqli_query($link, $query);
			$data = mysqli_fetch_assoc($result);
			echo $data["counted1"];
			
			mysqli_close($link);
			mysqli_free_result($result);
			mysqli_free_result($result2);
			mysqli_free_result($result3);
			?>
             <br />
             <br />
             <center><a href="javascript:window.close();">close this  page</a></center>
             <br />
             <br />
             </td>
  </tr>
</table>
</body>
</html>