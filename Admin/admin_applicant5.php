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
$company_code = $_GET["company_code"];

?>
<html>
<head>
<?php 
include "./meta.php";
?>
<style type="text/css">
<!--
.style1 {color: #FF0000}
.style6 {color: #FFFFFF;
	font-weight: bold;
}
.style71 {font-size: 9px; color: #000000; }
.style21 {color: #000000}
.style11 {color: #000000}
-->
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="50%" border="0" align="center" cellpadding="5" cellspacing="5">
  <tbody>
    <tr>
      <td><form action="admin_applicant55.php?company_code=<?php echo $company_code;?>" method="post" name="form1">
        <label> <span class="style11"><strong>Starting Date :</strong> ( yyyy - mm - dd )<br>
          <input name="date_start" type="text" id="date_start" size="40" maxlength="200">
          <br>
          <br>
          <strong>End Date :</strong>( yyyy - mm - dd )<br>
        </span></label>
        <span class="style11">
          <input name="date_end" type="text" id="date_end" size="40" maxlength="200">
          <label><br>
          </label>
          <label>
            <input type="submit" name="Submit222" value="Search">
          </label>
          </span>
        <br><br>
          <br>
          <a href="javascript: self.close()">close this window</a>
        <br>
      </form></td>
    </tr>
  </tbody>
</table>
</body>
</html>