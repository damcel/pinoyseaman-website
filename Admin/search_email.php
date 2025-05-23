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

$email = $_POST["email"];
?>
<html>
<head>
<?php 
include "./meta.php";
?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center">
  <table width="80%" border="0" cellpadding="5" cellspacing="5" bgcolor="#FFFFFF">
    <tr>
      <td><?php
	  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
	  $query = "SELECT * from job_seeker where email= '$email'" or die("Error" . mysqli_error($link));
	  $result = mysqli_query($link, $query);
	  while($row = mysqli_fetch_array($result))
	  {
	  ?>
          <table width="100%" border="0" cellpadding="3" cellspacing="2">
            <tr align="left" valign="top" bgcolor="#151515">
              <td width="30%" bgcolor="#F5F5F5" class="black1"><a href = "admin_display_applicant_seaman_info2.php?code=<? echo $row['code'];?>" target="windowNameb"
   onclick="window.open(this.href,this.target,'width=800,height=500,scrollbars=yes');
            return false;"><strong>
                <? 
				$name = $row["last_name"] . " " . $row["first_name"] . " " . $row["middle_name"];
				$name = ucwords(strtolower($name));
				echo $name;?>
              </strong></a></td>
              <td width="30%" bgcolor="#F5F5F5" class="black1"><? echo $row["email"]; ?></td>
              <td width="20%" bgcolor="#F5F5F5" class="black1"><? echo $row["prefer_job"]; ?></td>
              <td width="15%" bgcolor="#F5F5F5" class="black1"><? echo $row["date"]; ?></td>
              <td width="5%" bgcolor="#F5F5F5" class="black1"><? echo $row["verification"]; ?></td>
            </tr>
        </table>
          <div align="center">
            <?
			}
			mysqli_close($link);
			mysqli_free_result($result);
			?>
            <br />
            <br />
          <a href="admin_panel.php"><strong>Back to Admin Page </strong></a></div></td>
    </tr>
  </table>
</div>
</body>
</html>
