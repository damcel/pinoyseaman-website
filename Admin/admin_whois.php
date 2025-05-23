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
$company = $_GET["company"];
?>
<html>
<head>
<?php
include "./meta.php";
?>
<style type="text/css">
<!--
.style1 {font-weight: bold}
.style2 {font-weight: bold}
.style11 {color: #000000}
-->
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br />
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="left" valign="top"><br />
      <?php 
	  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error connecting database" . mysqli_error($link));
	  $query = "SELECT * FROM employer WHERE company_code='$company'" or die("Error" . mysqli_error($link));
	  $result = mysqli_query($link, $query);
	  
	  while($row = mysqli_fetch_array($result))
	  {
	  $id = $row['id'];
	  ?>
        <table width="100%" border="0" cellpadding="3" cellspacing="3" bgcolor="#FFFFFF">
          <tr align="left" valign="top" bgcolor="#FF9900">
            <td bgcolor="#000000">&nbsp;<span class="style29"><strong>Employer Profile </strong></span></td>
          </tr>
        </table>
      <br />
        <table width="100%" border="0" cellpadding="3">
          <tr align="left" valign="top">
            <td width="21%" class="black1"><div align="right"><strong><span class="style2">Employer : </span></strong></div></td>
            <td class="black1"><?php 
			$company = $row["company"];
			$company = str_replace("^", "'", $company);
			$company = str_replace("*", "&", $company);
			echo $company;
			$company_name = $row["company"];
			?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Date Registered : </span></strong></div></td>
            <td class="black1"><?php echo $row["date_registered"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td height="29" class="black1"><div align="right"><strong><span class="style2">Date Expiry : </span></strong></div></td>
            <td class="black1"><?php echo $row["date"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Member Type : </span></strong></div></td>
            <td class="black1"><?php echo $row["member_type"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Login Status : </span></strong></div></td>
            <td class="black1"><?php echo $row["post"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Email Verify : </span></strong></div></td>
            <td class="black1"><?php echo $row["verify"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Company Code : </span></strong></div></td>
            <td class="black1">
			<?php 
			echo $row["company_code"];
			$company_code = $row["company_code"];
			?>
            </td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Employer ID : </span></strong></div></td>
            <td class="black1"><?php echo $row["id"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Password : </span></strong></div></td>
            <td><?php echo $row["secret"]; ?></td>
          </tr>
          <tr align="left" valign="top">
            <td><div align="right"><strong><span class="style2">Email Address : </span></strong></div></td>
            <td class="black1"><?php $email =  $row["email"]; echo $email;?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1">&nbsp;</td>
            <td class="black1"><?php echo $row["email2"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1">&nbsp;</td>
            <td class="black1"><?php echo $row["email3"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Contact Person : </span></strong></div></td>
            <td class="black1"><?php echo $row["contact"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong><span class="style2">Phone : </span></strong></div></td>
            <td class="black1"><?php echo $row["phone"];?><br />
            <br />
            <br />
            <br /></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Profile : </strong></div></td>
            <td class="black1">
            <form name="form1" method="post" action="update_employa_profile.php">
              <label>
              <textarea name="profile" cols="80" rows="30" id="profile"><?php $company_profile = $row["company_profile"]; 
							$company_profile = @str_replace("^", "'", $company_profile);
							$company_profile = @str_replace("*", "&", $company_profile); 
							echo htmlspecialchars($company_profile);
							?></textarea>
              </label>
              <label> <br />
              <input type="submit" name="Submit2222" value="go">
              </label>
              <input name="comp_code" type="hidden" id="comp_code" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Update Email : </strong></div></td>
            <td class="black1"><form name="form1" method="post" action="update_employa_email.php">
              <label>
              <input name="email" type="text" id="email" value="" size="50">
              </label>
              <label>
              <input type="submit" name="Submit222" value="go">
              </label>
              <input name="comp_code" type="hidden" id="comp_code" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Update Member Type : </strong></div></td>
            <td class="black1"><form name="form1" method="post" action="update_employa_type.php">
                <label>
                <select name="employa_type" id="employa_type">
                  <option value="<?php echo $row["member_type"];?>"><?php echo $row["member_type"];?></option>
                  <option value="Free">Free</option>
                  <option value="Plan1">Plan1</option>
                  <option value="Plan2">Plan2</option>
                  <option value="Plan3">Plan3</option>
                  <option value="Plan4">Plan4</option>
                </select>
                </label>
                <label>
                <input type="submit" name="Submit22" value="go">
                </label>
                <input name="comp_code" type="hidden" id="comp_code" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Update Login Status : </strong></div></td>
            <td class="black1"><form name="form1" method="post" action="update_employa_login.php">
                <label>
                <input name="employa_login" type="text" id="employa_login" value="<?php echo $row["post"];?>" size="5" maxlength="1">
                </label>
                <label>
                <input type="submit" name="Submit2" value="go">
                </label>
                <input name="comp_code2" type="hidden" id="comp_code2" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Update Password : </strong></div></td>
            <td class="black1"><form name="form1" method="post" action="update_employa_pass.php">
                <label>
                <input name="employa_pass" type="text" id="employa_pass">
                </label>
                <label>
                <input type="submit" name="Submit" value="go">
                </label>
                <input name="comp_code3" type="hidden" id="comp_code3" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Update Expiry Date : </strong></div></td>
            <td class="black1"><form name="form1" method="post" action="update_employa_expiry.php">
                <label>
                <input name="employa_expiry" type="text" id="employa_expiry">
                </label>
                <label>
                <input type="submit" name="Submit3" value="go">
                </label>
                <input name="comp_code4" type="hidden" id="comp_code4" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Update Logo : </strong></div></td>
            <td class="black1"><form name="form1" method="post" action="update_employa_logo.php">
                <label>
                <input name="employa_logo" type="text" id="employa_logo" value="<?php echo $row["logo"]; ?>">
                </label>
                <label>
                <input type="submit" name="Submit32" value="go">
                </label>
                <input name="comp_code5" type="hidden" id="comp_code5" value="<?php echo $row["company_code"];?>">
            </form></td>
          </tr>
          <tr align="left" valign="top">
            <td class="style1">&nbsp;</td>
            <td class="black1"><?php if($row["verify"] == '' )
					{
					?>
                <a href="resend_activation.php?id=<?php echo $row["id"];?>&email=<?php echo $row["email"]; ?>&password=<?php echo $row["password"];?>&secret=<?php echo $row["secret"];?>" target="windowName"
   onclick="window.open(this.href,this.target,'width=800,height=500,scrollbars=yes');
            return false;">re-send email activation</a> <br />
                <br />
            <?php }
					?>            </td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Last Modified by : </strong></div></td>
            <td class="black1"><?php echo $row["modified_by"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Action : </strong></div></td>
            <td class="black1"><?php echo $row["action"];?></td>
          </tr>
          <tr align="left" valign="top">
            <td class="black1"><div align="right"><strong>Date Modify : </strong></div></td>
            <td class="black1"><?php echo $row["date_modified"];?></td>
          </tr>
      </table>
      <?php 
	  }
	  ?>
        <br />
        <br />
        <table width="100%" border="0" cellpadding="3" cellspacing="3" bgcolor="#FFFFFF">
          <tr align="left" valign="top" bgcolor="#FF9900">
            <td width="31%" bgcolor="#000000"><span class="style29"><strong>&nbsp;Job Position</strong></span></td>
            <td width="25%" bgcolor="#000000"><span class="style29"><strong>&nbsp;Posted</strong></span></td>
            <td width="25%" bgcolor="#000000"><span class="style29"><strong>&nbsp;Expiry date</strong></span></td>
          </tr>
      </table>
      <br />
        <?php
		
		$query = "SELECT * from jobs where company_name = '$company'" or die("Error connecting database" . mysqli_error($link));
		$result = mysqli_query($link, $query);
		while($row = mysqli_fetch_array($result))
		{
		?>
        <table width="100%" border="0" cellpadding="3" cellspacing="3">
          <tr align="left" valign="top">
            <td width="31%" class="black1">&nbsp;<?php echo $row["job_title"];?></td>
            <td width="25%" class="black1"><?php echo $row["date_posted"];?></td>
            <td width="25%" class="black1"><?php echo $row["expiry"];?></td>
          </tr>
      </table>
	  <?php 
	  }
	  $query2 = "SELECT count(*) as count2 from  jobs WHERE company_name = '$company'" or die("Error connecting database" . mysqli_error($link));
	  $result2 = mysqli_query($link, $query2);
	  $data = mysqli_fetch_assoc($result2);
	  $c = $data[count2];
	  ?>
        <br />
        <br />
        <hr size="1" class="grey">
        <table width="100%" border="0" cellpadding="3" cellspacing="2" >
          <tr align="left" valign="top">
            <td width="17%" class="black1"> Jobs posted : <?php echo $c; ?><br />
              Total Applicants : 
			  <?php 
			  $link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
			  $query = "SELECT COUNT(*) as counted FROM job_applicants where company='$company_name' and mark=''" or die("Error" . mysqli_error($link));
			  $result = mysqli_query($link, $query);
			  $data = mysqli_fetch_assoc($result);
			  $numguest3 = $data["counted"];
			  echo $numguest3;
			  mysqli_close($link);
			  mysqli_free_result($result);
			  mysqli_free_result($result2);
			  ?></td>
          </tr>
        </table>
        <div align="left">
        <br />
          <span class="black1">
          <br />
          <a href="admin_send_login.php?company_email=<?php echo $email; ?>" target="_blank">Send Login details to email ( <?php echo $email;?> )</a>
          <br />
          <a href="admin_whois_z.php?company_code=<?php echo $company_code; ?>" target="_blank">Display Employer (Login / Check Aplicants)</a>
          <br />
          <a href= "admin_applicant1.php?company_code=<?php echo $company_code; ?>"target="_blank">Display applicants</a><br />
          <a href= "admin_applicant2.php?company_code=<?php echo $company_code; ?>"target="_blank">Total applicants per day</a><br />
          <a href= "admin_applicant4.php?company_code=<?php echo $company_code; ?>"target="_blank">Total applicants per day / job position</a><br />
          <a href= "admin_applicant5.php?company_code=<?php echo $company_code; ?>"target="_blank">Total applicants via Date (start/end)</a><br />
          <br />
          <br />
          <br />
          <br />
          <br />
      </div></td>
  </tr>
</table>
<br />
</body>
</html>
