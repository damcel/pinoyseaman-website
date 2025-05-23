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
$ctr= 0;
?>
<html>
<head>
<?php
include "./meta.php";
?>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="5" cellpadding="5">
  <tr>
    <td>
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="38%" bgcolor="#305067" ><strong>Applicant List </strong></td>
          <td width="33%" bgcolor="#305067" ><strong>Position</strong></td>
          <td width="20%" bgcolor="#305067" ><strong>Date apply</strong></td>
        </tr>
      </table>
      <?php
	  if(!isset($offset))
	  {
		  $offset = 0;
		  }
		  $rec_per_page = 100;
		  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
		  $query2 = "SELECT * from job_applicants where company_code='$company_code' and date!='0000-00-00' and mark='' order by code  desc" or die("Error" . mysqli_error($link));
		  $query = "SELECT * from job_applicants where company_code='$company_code' and date!='0000-00-00' and mark='' order by code  desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
		  $result = mysqli_query($link, $query);
		  $result2 = mysqli_query($link, $query2);
		  $record_count = mysqli_num_rows($result2);
		  $page = ceil($record_count / $rec_per_page);
		  
		  while($row = mysqli_fetch_array($result))
		  {
			  ?>
      <table width="100%" border="0" cellpadding="4" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="38%" class="style7"><a href = "admin_applicant_info.php?email=<?php echo $row["email"];?>" target="windowName"
   onclick="window.open(this.href,this.target,'width=800,height=500,scrollbars=yes');
            return false;"><strong>
            <?php $name = $row["name"];$name = ucwords(strtolower($name));$name = str_replace("ñ", "n", $name);$name  = str_replace("Iii", "III",$name);$name=str_replace("Ii", "II",$name);echo $name;?>
          </strong></a></td>
          <td width="33%" ><font color="#000000"><?php echo $row["job_hiring"];?></font></td>
          <td width="20%" ><font color="#000000"><?php echo $row["date"];?></font></td>
        </tr>
      </table>
      <?php
			  }
			  echo "<br /><font color='#000000'>You are viewing page ";
			  echo  $offset + 1  . " of $page <br />Page &nbsp;</font>";
			  for($i=0;$i<$page;$i++)
			  {
			  $label = $i+1;
			  ?>
      <a href="admin_applicant1.php?offset=<?php echo (int)$ctr;?>&company_code=<?php echo $company_code;?>">
        <?php
			  if($label == $offset+1)
			  {
			  echo '<font size=2 color=red>'.$label.'</font>'; 
			  }
			  else
			  {
			  echo $label; 
			  }
			  ?>
      </a> <font color="#000000">|</font>
      <?php
			  $ctr++;
			   }
			   echo "<br />";
				?>
      <br />
      <font color="#000000"><strong>Total Applicants :</strong>
        <?php
		$query = "SELECT COUNT(*) as counted1 FROM job_applicants where  company_code='$company_code'" or die("Error" . mysqli_error($link));
		$result = mysqli_query($link, $query);
		$data = mysqli_fetch_assoc($result);
		echo $data["counted1"];
		
		
		mysqli_close($link);
		mysqli_free_result($result);
		mysqli_free_result($result2);
		?>
      </font>
      <br />
      <br />
      <center>
      <a href="javascript:window.close();">close this  page</a>
      </center>
      <br />
      <br />
      </td>
  </tr>
</table>
</body>
</html>