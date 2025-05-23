<?php  
session_start();
include "./connect.php";

if (!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
    header("location: admin.php");
    exit;
}
?>
<html>
<head>
<?php include "./meta.php"; ?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div align="center"><br /><br />
  <?php
  $link = mysqli_connect($dbhost, $dbusername, $dbuserpassword, $dbname) or die("Error connecting to database: " . mysqli_error($link));
  $query = "SELECT * FROM employer WHERE verify = '' ORDER BY company ASC";
  $result = mysqli_query($link, $query) or die("Error executing query: " . mysqli_error($link));

  while ($row = mysqli_fetch_array($result)) {
  ?>
    <a><strong><?php echo $row["company"]; ?></strong></a>
    <table width="80%" border="0" cellpadding="1" cellspacing="3" bgcolor="#FFFFFF">
      <tr>
        <td width="26%" align="left" valign="top" class="black1"><div align="right"><strong>Company:</strong></div></td>
        <td width="74%" align="left" valign="top" class="black1"><?php echo $row["company"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Address:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["address"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Phone:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["phone"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Fax:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["fax"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Contact Person:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["contact"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Primary Email:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["email"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Additional Email 1:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["email2"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Additional Email 2:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["email3"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Website:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["website"]; ?></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Company Profile:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["company_profile"]; ?><br /></td>
      </tr>
      <tr>
        <td align="left" valign="top" class="black1"><div align="right"><strong>Date Registered:</strong></div></td>
        <td align="left" valign="top" class="black1"><?php echo $row["date_registered"]; ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><span class="black1"><br />
          <a href="post_company.php?code=<?php echo $row['code']; ?>">Add to Database</a> | 
          <a href="delete_post_company.php?code=<?php echo $row['code']; ?>">Delete this Employer</a></span><br /><br />
        </td>
      </tr>
      <tr>
        <td colspan="2"><hr size="1"></td>
      </tr>
    </table>
  <?php
  }
  mysqli_free_result($result);
  mysqli_close($link);
  ?>
  <br /><br />
  <a href="admin_panel.php">Back to Admin Page</a>
  <br /><br /><br /><br /><br />
</div>
</body>
</html>