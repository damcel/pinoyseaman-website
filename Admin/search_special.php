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

$special = $_POST["special"];
if($special=='#')
{
$special1 = "^";
}

if($special=='&')
{
$special1 = "*";
}
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
      <td>
	  
	  <?php 
	  $offset = @$_GET["offset"];
	  if(!isset($offset))
				{
				$offset = 0;
				}
				$rec_per_page = 100;
				
				$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
				$query2 = "SELECT * from job_seeker where seagoing_work like '%$special%' order by last_name,first_name asc" or die("Error" . mysqli_error($link));
				$query = "SELECT * from job_seeker where seagoing_work like '%$special%' order by last_name,first_name asc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
				$result = mysqli_query($link, $query);
				$result2 = mysqli_query($link, $query2);
				$record_count = mysqli_num_rows($result2);
				$page = ceil($record_count / $rec_per_page);
				
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
            <?php 
		}
		if($record_count >= 1)
		{;
		for($i=0;$i<$page;$i++)
		{
		$label = $i + 1;
		?>
        | <a href="search_special.php?offset=<?php echo (int)$ctr;?>&new_special1=<? echo $special1;?>">
        <?php 
		if($label == $offset+1)
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
		 mysqli_close($link);
		 mysqli_free_result($result);
		 mysqli_free_result($result2);
		 ?>
            <br />
            <br />
          <a href="admin_panel.php"><strong>Back to Admin Page </strong></a></div></td>
    </tr>
  </table>
</div>
</body>
</html>
