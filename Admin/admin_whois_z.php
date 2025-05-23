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
$company_code = $_GET["company_code"];


?>
<html>
<head>
<?php
include "./meta.php";
?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="2" bgcolor="#FFFFFF">
  <tr>
    <td><br />
      <table width="80%" border="0" align="center" cellpadding="3" cellspacing="5" bgcolor="#000000">
        <tr align="left" valign="top" bgcolor="#FF0000">
          <td width="24%" bgcolor="#000000"><strong><font color="#FFFFFF">IP Address </font></strong></td>
          <td width="50%" bgcolor="#000000"><strong><font color="#FFFFFF">Action</font></strong></td>
          <td width="26%" bgcolor="#000000"><strong><font color="#FFFFFF">Date</font></strong></td>
        </tr>
      </table>
      <?php if(!isset($offset))
				{
				$offset = 0;
				}
				$rec_per_page = 50;
				
				$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
				$query2 = "SELECT * FROM action WHERE company='$company_code' order by code desc" or die("Error" . mysqli_error($link));
				$query = "SELECT * FROM action WHERE company='$company_code' order by code desc limit " . (int)($offset * $rec_per_page) . ",$rec_per_page" or die("Error" . mysqli_error($link));
				$result = mysqli_query($link, $query);
				$result2 = mysqli_query($link, $query2);
				$record_count = mysqli_num_rows($result2);
				$page = ceil($record_count / $rec_per_page);
				
				while($row = mysqli_fetch_array($result)) 
				{
				?>
      <table width="80%" border="0" align="center" cellpadding="2" cellspacing="2">
        <tr align="left" valign="top" bgcolor="#F5F5F5">
          <td width="24%" class="style7"><strong> <a href="admin_whois.php?company=<?php echo $row["company"];?>" target="windowName"
   onclick="window.open(this.href,this.target,'width=800,height=500,scrollbars=yes');
            return false;"></a></strong><font color="#666666"><?php echo $row["ip"];?></font></td>
          <td width="50%" class="style7"><?php echo $row["action"];?></td>
          <td width="26%" class="style7"><?php echo $row["date"];?> | <?php echo $row["time"];?></td>
        </tr>
      </table>
      <div align="center">
        <span class="style7">
        <?php 
		}
		if($record_count >= 1)
		{;
		for($i=0;$i<$page;$i++)
		{
		$label = $i + 1;
		?>
        | <a href="admin_whois_z.php?company_code=<?php echo $company_code;?>&offset=<?php echo (int)$ctr;?>">
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
        </span>
		<br />
		<br />
		<br />
		<br />
		<a href="javascript:window.close();">close this page</a>
		<br />
		<br />
		<br />
		<br />
        <br />
      </div></td>
  </tr>
</table>
</body>
</html>
