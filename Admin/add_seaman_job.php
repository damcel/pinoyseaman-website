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
//$company = @$_GET["company"];
?>
<html>
<head>
<title>Trabahong seaman, isang click nalang!</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FFFFFF;
}
a {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #3399FF;
	font-weight: bold;
}
a:visited {
	color: #3399FF;
	text-decoration: none;
}
a:hover {
	color: #FF0000;
	text-decoration: underline;
}
a:active {
	color: #3399FF;
	text-decoration: none;
}
a:link {
	text-decoration: none;
}
.black1 { font-size:small; color: #000000}
.style2 {color: #000000}
.style7 {font-size: 9px; color: #000000; }
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br>
<form name="addseamanjob1" method="post" action="add_seaman_job_verify.php">
  <table width="50%" border="0" align="center" cellpadding="3" cellspacing="3" bgcolor="#F7F7F7">
    <tr>
      <td colspan="2" bgcolor="#000000"><strong>Add Seaman Job </strong></td>
    </tr>
    <tr>
      <td width="37%" class="black1"><div align="right">Select Job 
        
        Category : </div></td>
      <td width="63%" class="black1"><label><span class="green">
        <select name="category" id="select">
          <?php
		  
		  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
		  $query = "SELECT * from seaman_job_category order by category asc" or die("Error" . mysqli_error($link));
		  $result = mysqli_query($link, $query);
		  while($row = mysqli_fetch_array($result))
		  {
			  ?>
          <option value="<?php  echo $row["category"]; ?>"selected><?php  echo $row["category"]; ?></option>
          <?php 

				      }

				     ?>
        </select>
      </span></label></td>
    </tr>
    <tr>
      <td class="black1"><div align="right">Job : </div></td>
      <td class="black1"><label>
        <input name="job" type="text" id="job2" size="45">
      </label></td>
    </tr>
    <tr>
      <td class="black1">&nbsp;</td>
      <td class="black1"><input type="submit" name="Submit" value="Submit"></td>
    </tr>
  </table>
</form>
<form name="addseamanjob2" method="post" action="add_seaman_category.php">
  <table width="50%" border="0" align="center" cellpadding="3" cellspacing="3" bgcolor="#F7F7F7">
    <tr>
      <td colspan="2" bgcolor="#000000"><strong>Category not listed above, i want to add a new job category </strong></td>
    </tr>
    <tr>
      <td width="37%" height="30" align="left" valign="top" class="black1"><div align="right">Add new Job Category 
        
      : </div></td>
      <td width="63%" align="left" valign="top" class="black1"><label>
        <input name="new_job_category" type="text" id="new_job_category" size="45">
        <br>
        <span class="style7">* please input in caps lock </span></label></td>
    </tr>
    <tr>
      <td align="left" valign="top" class="black1">&nbsp;</td>
      <td align="left" valign="top" class="black1"><input type="submit" name="Submit2" value="Submit"></td>
    </tr>
  </table>
</form>
<div align="center"><br>
  <br>
      <span class="style2">Existing Job on database<br>
    <select>
      <option value="<?php  echo $prefer_job; ?>" selected="selected"><?php  echo $prefer_job; ?></option>
      <?php
	  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
	  //$query = "SELECT * from seaman_jobs order by category,job asc" or die("Error" . mysqli_error($link));
	  $query = "SELECT category,job from seaman_jobs order by category,job asc" or die("Error" . mysqli_error($link));
	  $result = mysqli_query($link, $query);
	  while($row = mysqli_fetch_array($result))
	  {
		  ?>
      <option value="<?php  echo $row["job"]; ?>"><?php  echo $row["category"]; ?> - <?php  echo $row["job"]; ?></option>
      <?php
	  }
	  mysqli_close($link);
	  mysqli_free_result($result);

	  ?>
    </select>
  </span><br>
    <br>
    <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
    <a href="admin_panel.php">Back to Admin Page</a><br>
  <br>
</div>
</body>
</html>
