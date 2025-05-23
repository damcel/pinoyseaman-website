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
?>
<html>
<head>
<?php 
include "./meta.php";
echo "ito nga";
?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
  <table width="80%" border="0" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
    <tr>
      <td align="left" valign="top">
	  <?php
	  if(!isset($offset))
						{
						$offset = 0;
						}
						$rec_per_page = 200;
						$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
						$query2 = "SELECT first_name,middle_name,last_name,email,prefer_job,code,date from job_seeker order by code desc" or die("Error" . mysqli_error($link));
						$query = "SELECT first_name,middle_name,last_name,email,prefer_job,code,date from job_seeker order by code desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
						$result = mysqli_query($link, $query);
						
						$result2 = mysqli_query($link, $query2);
						$record_count = mysqli_num_rows($result2);
						$page = ceil($record_count / $rec_per_page);
						
						while($row = mysqli_fetch_array($result))
	  {
	  ?>
        <table width="100%" border="0" cellpadding="4" cellspacing="2">
          <tr align="left" valign="top" bgcolor="#151515">
            <td width="27%" bgcolor="#F5F5F5" class="style7"><a href = "admin_display_applicant_seaman_info2.php?code=<?php echo $row["code"];?>" target="windowName2"
   onclick="window.open(this.href,this.target,'width=800,height=500,scrollbars=yes');
            return false;"><strong>
              <?php $name = strtolower($row["last_name"]) . " " . strtolower($row["first_name"]) . " " . strtolower($row["middle_name"]);$name = ucwords($name);$name  = str_replace("Iii", "III",$name);$name =str_replace("Ii", "II",$name);echo $name;
			?>
              </strong></a></td>
            <td width="22%" bgcolor="#F5F5F5" class="style7"><?php echo $row["email"]; ?></td>
            <td width="22%" bgcolor="#F5F5F5" class="style7"><?php echo $row["prefer_job"]; ?></td>
            <td width="13%" bgcolor="#F5F5F5" class="style7"><?php echo $row["date"]; ?></td>
            <td width="4%" bgcolor="#F5F5F5" class="style7"><?php echo $row["verification"]; ?></td>
            <td width="6%" bgcolor="#F5F5F5" class="style7"><a href="alisin_ito.php?code=<?php echo $row["code"];?>"><strong>Delete</strong></a></td>
            <td width="6%" bgcolor="#F5F5F5" class="style7"><?php echo $row["code"]; ?></td>
          </tr>
        </table>
        <div align="center"> <span class="style7">
          <?php }
			  if($record_count >= 1)
				   {
				   echo "Pages : ";
				   for($i=0;$i<$page;$i++)
				   {
				   $label = $i + 1;
				   ?>
          | <a href="seaman_listing2.php?offset=<?php echo (int)$ctr;?>">
          <?php if($label == $offset+1)
			  {
			  echo '<font color=red size=5>'.$label.'</font>'; 
			  }
			  else
			  {
			  echo $label;
			  }
			  ?>
          </a>
          <?php 
		  $ctr++;
		  }
		  }
		  ?>
          </span><br />
          <br />
          <a href="admin_panel.php">Back to Admin Page</a></div></td>
    </tr>
  </table>
  <?php
  mysqli_close($link);
  mysqli_free_result($result);
  mysqli_free_result($result2);
  ?>
</div>
</body>
</html>
