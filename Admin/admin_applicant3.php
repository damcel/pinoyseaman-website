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
$date = $_GET["date"];
$company_code = $_GET["company_code"];
?>
<html>
<head>
<?php
include "./meta.php";
?>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" align="center" cellpadding="5" cellspacing="5">
  <tr>
    <td>
	<?php 
	if(!isset($offset))
	{
		$offset = 0;
		}
		
		$rec_per_page = 50;
		
		$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
		$query2 = "SELECT * from job_applicants where company_code='$company_code' and mark='' and date='$date' order by code  desc" or die("Error" . mysqli_error($link));
		$query = "SELECT * from job_applicants where company_code='$company_code' and mark='' and date='$date' order by code  desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
		$result = mysqli_query($link, $query);
		$result2 = mysqli_query($link, $query2);
		$record_count = mysqli_num_rows($result2);
		$page = ceil($record_count / $rec_per_page);
		
		while($row = mysqli_fetch_array($result))
		{
			?>
      <table width="95%" border="0" align="center" cellpadding="5" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="38%" class="black1">
            <?php $name = $row["name"];$name = ucwords(strtolower($name));$name = str_replace("ñ", "n", $name);$name  = str_replace("Iii", "III",$name);$name=str_replace("Ii", "II",$name);echo $name;?>
          </td>
          <td width="33%" class="black1"><?php echo $row["job_hiring"];?></td>
          <td width="20%" class="black1"><?php echo $row["date"];?></td>
        </tr>
      </table>
      <div align="center" class="black1">
        <?php }
			  echo "<br />You are viewing page ";
			  echo  $offset + 1  . " of $page <br /><br /> Page &nbsp;";
			  for($i=0;$i<$page;$i++)
			  {
			  $label = $i+1;
			  ?>
        [ <a href="admin_applicant3.php?offset=<?php echo (int)$ctr;?>&date=<?php echo $date;?>&company_code=<?php echo $company_code;?>">
  <?php if($label == $offset+1)
			  {
			  echo $label; 
			  }
			  else
			  {
			  echo $label; 
			  }
			  ?>
  </a> ]
  <?php $ctr++;
			   }
			   echo "<br /><br /><br />";
			   mysqli_close($link);
			   mysqli_free_result($result);
			   mysqli_free_result($result2);
			   ?>
               <br />
        <br />
        <br />
        <a href="javascript: self.close()">close this window</a><br />
        <br />
      </div>
      <br />
    <br /></td>
  </tr>
</table>
</body>
</html>