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
?>
<html>
<head>
<?php
include "./meta.php";
?>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br />
<table width="53%"  border="0" align="center" cellpadding="5" cellspacing="5" bgcolor="#F5F5F5">
  <tr>
    <td width="100%" bgcolor="#000000"><strong>Testimonials</strong></td>
  </tr>
  <tr>
    <td>
	<br />
      <?php 
	  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
	  $query = "SELECT * from testimonials where post = ' ' order by code asc" or die("Error" . mysqli_error($link));
	  $result = mysqli_query($link, $query);
	  while($row = mysqli_fetch_array($result))
	  {
	  ?>
				
      <table width="100%" border="0" cellpadding="3" cellspacing="2">
        <tr align="left" valign="top">
          <td width="15%" class="black1"><div align="right">Name :</div></td>
          <td width="85%" class="black1"><?php echo $row["name"];?></td>
        </tr>
        <tr align="left" valign="top">
          <td height="22" class="black1"><div align="right">Email :</div></td>
          <td class="black1"><?php echo $row["email"];?></strong></td>
        </tr>
        <tr align="left" valign="top">
          <td height="22" class="black1"><div align="right">Message :</div></td>
          <td class="black1"><?php echo $row["message"]; ?></strong></td>
        </tr>
        <tr align="left" valign="top">
          <td height="22" class="black1">&nbsp;</td>
          <td class="black1"><a href="testimonial_post.php?code=<?php echo $row["code"];?>"><strong>Post</strong></a> | <a href="testimonial_delete.php?code=<?php echo $row["code"];?>"><strong>Delete</strong></a> </td>
        </tr>
      </table>
      <br />
	  <hr size="1">
      <?php
	  }
	  mysqli_close($link);
	  mysqli_free_result($result);
	  mysqli_free_result($result2);
	  ?>
	  <br />
	  <br />
	  <a href="admin_panel.php">Back to Admin Page</a>
	  </td>
  </tr>
</table>
<br />
<br />
</body>
</html>
