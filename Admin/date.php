<?php
include "./connect.php";
?>
<html>
<head>
<title>pinoyseaman - trabahong seaman, isang click nalang!</title>
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
.style7 {font-size: 9px; color: #000000; }
-->
</style></head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name=excel.php" method="post">
<table id="Table_01" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left" valign="top" background="images/index_03.jpg"><?php
					$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error connecting database" . mysqli_error($link));
					$query = "SELECT * from employer where date >= '$datenow' and verify='y' order by date,company asc" or die("Error" . mysqli_error($link));
					
					$result = mysqli_query($link, $query);
					  while($row = mysqli_fetch_array($result))
					  {
					  ?>
          <table width="100%" border="0" align="center" cellpadding="2" cellspacing="5">
            <tr align="left" valign="top" >
              <td width="54%" ><font size="3" color="#000000">
                <?php
						  $company =  $row["company"];
						  $company = str_replace("^", "'", $company);
						  $company = str_replace("*", "&", $company);
						  echo $company;
						  ?>
              </font></td>
              <td width="46%" ><font size="3" color="#000000"><? echo $row["date"]; ?></font></td>
            </tr>
          </table>
        <?php 
			  }
			  ?></td>
	</tr>
</table>
</form>
</body>
</html>