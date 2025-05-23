<?php
session_start();
include "connect.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
  header("location: admin.php");
  exit;
}

$link = mysqli_connect($dbhost, $dbusername, $dbuserpassword, $dbname);
if (!$link) {
  die("Error connecting to database: " . mysqli_connect_error());
}

$query = "SELECT * FROM employer ORDER BY company ASC";
$result = mysqli_query($link, $query);
if (!$result) {
  die("Error executing query: " . mysqli_error($link));
}
?>
<html>
<?php include "./meta.php"; ?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br />
<table width="90%" border="0" align="center" cellpadding="5" cellspacing="2" bgcolor="#000000">
  <tr bgcolor="#FF0000">
  <td width="48%" bgcolor="#000000"><strong><font color="#FFFFFF">Company</font></strong></td>
  <td width="19%" bgcolor="#000000"><strong><font color="#FFFFFF">Employer ID</font></strong></td>
  <td width="19%" bgcolor="#000000"><strong><font color="#FFFFFF">Password</font></strong></td>
  <td width="7%" bgcolor="#000000"><strong><font color="#FFFFFF">Login</font></strong></td>
  <td width="3%" bgcolor="#000000"><strong><font color="#FFFFFF">Verify</font></strong></td>
  <td width="4%" bgcolor="#000000">&nbsp;</td>
  </tr>
</table>
<?php
while ($row = mysqli_fetch_array($result)) {
?>
<table width="90%" border="0" align="center" cellpadding="5" cellspacing="2" bgcolor="#F4F4F4">
  <tr bgcolor="#F7F7F7">
  <td width="48%" bgcolor="#FCFCFC" class="black1"><a href="admin_whois.php?company=<?php echo $row["company_code"]; ?>" target="new"><strong>
  <?php 
  $company = str_replace(["^", "*"], ["'", "&"], $row["company"]);
  echo $company;
  ?>
  </strong></a> <span class="style7">(<?php echo $row['date']; ?>)</span></td>
  <td width="19%" bgcolor="#FCFCFC" class="black1"><strong><?php echo $row["id"]; ?></strong></td>
  <td width="19%" bgcolor="#FCFCFC"><strong><?php echo $row["secret"]; ?></strong></td>
  <td width="7%" bgcolor="#FCFCFC" class="black1"><strong><?php echo $row["post"]; ?></strong></td>
  <td width="3%" bgcolor="#FCFCFC" class="black1"><strong><?php echo $row["verify"]; ?></strong></td>
  <td width="4%" bgcolor="#FCFCFC" class="black1"><a href="admin_delete_employer.php?code=<?php echo $row['company_code']; ?>" onClick="return confirm('Are you sure you want to delete this employer?')">delete</a></td>
  </tr>
</table>
<div align="center">
<?php
}
mysqli_free_result($result);
mysqli_close($link);
?>
  <br />
  <br />
  <a href="admin_panel.php">Back to Admin Page</a><br />
  <br />
  <br />
  <br />
  <br />
</div>
</body>
</html>
