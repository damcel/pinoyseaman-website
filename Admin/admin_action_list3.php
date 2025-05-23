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
$offset = @$_GET["offset"];
?>
<html>
<head>
<?php
include "./meta.php";
?>
<style type="text/css">
<!--
.style7 {font-size: 9px; color: #000000; }
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF">
  <tr>
    <td><br>
    <br>
      <br>
      <?php $offset = @$_GET["offset"];
				if(!isset($offset))
				{
				$offset = 0;
				}
				$rec_per_page = 50;
				
				$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
				$query2 = "SELECT * FROM action WHERE action='Company Login Successful' order by code desc" or die("Error" . mysqli_error($link));
				$query = "SELECT * FROM action WHERE action='Company Login Successful' order by code desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
				$result = mysqli_query($link, $query);
				$result2 = mysqli_query($link, $query2);
				$record_count = mysqli_num_rows($result2);
				$page = ceil($record_count / $rec_per_page);
				
				while($row = mysqli_fetch_array($result)) 
				{
				?>
<table width="75%" border="0" align="center" cellpadding="3" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="81%" bgcolor="#F5F5F5" class="style7">
		  <?
		  $kumpanya = $row["company"];		  
		  
		  $query2 = "SELECT * FROM employer WHERE company_code='$kumpanya'" or die("Error" . mysqli_error($link));
		  $result2 = mysqli_query($link, $query2);
		  $rowa = mysqli_fetch_array($result2);
		  echo $rowa["company"];
		  ?>
		  </td>
          <td width="19%" class="style7"><? echo $row["date"];?> | <? echo $row["time"];?></td>
        </tr>
      </table>
      <div align="center">
        <span class="style7">
<?php }
				echo "You are viewing Page ";
				echo  $offset + 1  . " of $page<br><br>Page &nbsp; ";
				for($i=0;$i<$page;$i++)
				{
				$label = $i+1;
				?><a href="admin_action_list3.php?offset=<?php echo (int)$ctr;?>"><?php echo  $label; ?></a> |
<?php $ctr++;
				}
				mysqli_close($link);
				mysqli_free_result($result);
				mysqli_free_result($result2);
				?>
<br>
        </span><br>
<br>
<br>

  <a href="admin_panel.php">Back to Admin Page</a><br>
  <br>
  <br>
  <br>
        <br>
      </div></td>
  </tr>
</table>
</body>
</html>
