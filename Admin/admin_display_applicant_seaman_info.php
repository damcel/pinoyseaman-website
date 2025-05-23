<?php  
session_start();
$email = @$_GET["email"];

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
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table id="Table_01" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left" valign="top" background="images/index_01.jpg"><img src="images/logo.jpg" height="50" /></td>
	</tr>
	
	<tr>
		<td align="left" valign="top" background="images/index_03.jpg"><table width="100%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="1001" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
                
                <tr>
                  <td align="left" valign="top"><TABLE borderColor=#F5F5F5 height=41 cellSpacing=0 cellPadding=10 width="95%" align=center border=0>
                    <TBODY>
                      <TR>
                        <TD height=39><p align="justify" class="h2">
                            <?php 
							include "./connect.php";
							$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
							$query = "SELECT * from job_seeker where email='$email'" or die("Error" . mysqli_error($link));
							$result = mysqli_query($link, $query);
							while($row = mysqli_fetch_array($result))
							{
							$coding = $row['code'];
							?>
                            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="5">
                              <tr>
                                <td colspan="3" align="left" valign="top" class="black1"><strong>Seaman Information :</strong>
                                    <hr size="1"></td>
                              </tr>
                              <tr>
                                <td width="29%" align="left" valign="top" class="black1"> Name :<br />
                                </td>
                                <td colspan="2" align="left" valign="top" class="black1"><?php  echo $row["first_name"];?> <?php  echo $row["middle_name"];?> <?php  echo $row["last_name"];?></td>
                              </tr>
                              <tr>
                                <td class="black1">Date of Birth :<br />
                                </td>
                                <td colspan="2" class="black1"><?php  echo $row["birthday"];?></td>
                              </tr>
                              <tr>
                                <td class="black1">Gender :<br />
                                </td>
                                <td colspan="2" class="black1"><?php  echo $row["gender"];?></td>
                              </tr>
                              <tr>
                                <td align="left" valign="top" class="black1">Nationality :<br />
                                </td>
                                <td colspan="2" align="left" valign="top" class="black1"><?php  echo $row["nationality"];?></td>
                              </tr>
                              <tr>
                                <td class="black1">Marital Status :<br />
                                </td>
                                <td colspan="2" class="black1"><?php  echo $row["status"];?></td>
                              </tr>
                              <tr>
                                <td class="black1">No. of Children :<br />
                                </td>
                                <td colspan="2" class="black1"><?php  echo $row["children"];?></td>
                              </tr>
                              <tr>
                                <td class="black1">Religion :<br /></td>
                                <td colspan="2" class="black1"><?php  echo $row["religion"];?></td>
                              </tr>
                              <tr>
                                <td align="left" valign="top" class="black1">Address : <br /></td>
                                <td colspan="2" align="left" valign="top" class="black1"><?php  echo $row["address"];?></td>
                              </tr>
                              <tr>
                                <td class="black1">City :<br /></td>
                                <td colspan="2" class="black1"><?php  echo $row["city"];?></td>
                              </tr>
                              <tr>
                                <td align="left" valign="top" class="black1">Phone Number :<br /></td>
                                <td colspan="2" align="left" valign="top" class="black1"><?php  echo $row["phone"];?></td>
                              </tr>
                              <tr>
                                <td align="left" valign="top" class="black1">Cellphone :</td>
                                <td colspan="2" align="left" valign="top" class="black1"><?php  echo $row["cellphone"];?></td>
                              </tr>
                              <tr>
                                <td align="left" valign="top" class="black1">Email Address :<br /></td>
                                <td colspan="2" align="left" valign="top" class="black1">
								<?php
								echo $row["email"];
								$email = $row["email"];
								?></td>
                              </tr>
                              <tr>
                                <td rowspan="4" align="left" valign="top" class="black1">Password : </td>
                                <td colspan="2" align="left" valign="top" class="black1"><?php  echo $row["id"];?></td>
                              </tr>
                              <tr>
                                <td colspan="2" align="left" valign="top" class="black1"><form name="form1" method="post" action="update_seaman_newpass.php">
                                    <label>
                                    <input name="seaman_new_password" type="text" id="seaman_new_password">
                                    </label>
                                    <label>
                                    <input type="submit" name="Submit" value="update password">
                                    </label>
                                    <input name="email" type="hidden" id="email" value="<?php  echo $row["email"];?>">
                                    <input name="code" type="hidden" id="code" value="<?php  echo $row["code"];?>">
                                </form></td>
                              </tr>
                              <tr>
                                <td colspan="2" align="left" valign="top" class="black1"><form name="form1" method="post" action="update_seaman_newemail.php">
                                    <label>
                                    <input name="seaman_new_email" type="text" id="seaman_new_email" size="40">
                                    </label>
                                    <label>
                                    <input type="submit" name="Submit3" value="update email">
                                    </label>
                                    <input name="code2" type="hidden" id="code2" value="<?php  echo $row["code"];?>">
                                    <input name="code3" type="hidden" id="code3" value="<?php  echo $row["email"];?>">
                                </form></td>
                              </tr>
                              <tr>
                                <td width="39%" align="left" valign="top" class="black1"><a href="activate_na_ito.php?email=<?php  echo $row["email"];?>&code=<?php  echo $row["code"];?>"><strong>Activate Account</strong></a><br />
                                  <a href="email_padala.php?email=<?php  echo $row['email'];?>&password=<?php  echo $row['id'];?>" target="_blank"><strong>Send Password to Email</strong></a></td>
                                <td width="32%" align="left" valign="top" class="black1">Verified via Email : <?php  echo $row["verification"];?></td>
                              </tr>
                              <tr>
                                <td height="24" colspan="3" align="left" valign="top" class="black1"><strong>Passport Information : </strong>
                                    <hr size="1">
                                </td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1">Country :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1"><?php  echo $row["passport_country"];?></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1">No. :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1"><?php  echo $row["passport_no"];?></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1">Issued  :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1"><?php  echo $row["passport_issued"];?></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1">Valid  :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1"><?php  echo $row["passport_valid"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><strong>Seaman's Book Information  :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1" >Country  :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1" ><?php  echo $row["sbook_country"];?></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1" >No.  :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1" ><?php  echo $row["sbook_no"];?></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1" >Issued  :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1" ><?php  echo $row["sbook_issued"];?></td>
                              </tr>
                              <tr>
                                <td height="24" align="left" valign="top" class="black1" >Valid  :<br />
                                </td>
                                <td height="24" colspan="2" align="left" valign="top" class="black1" ><?php  echo $row["sbook_valid"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="24" colspan="3" align="left" valign="top" class="black1"><strong>Licenses of Competence and US Visa Information :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><?php  echo $row["competence"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="24" colspan="3" align="left" valign="top" class="black1"><strong>Certificates  :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><?php  echo $row["certificates"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="24" colspan="3" align="left" valign="top" class="black1"><strong>Merits,  Rewards, Titles, Hobbies, Interests :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><?php  echo $row["merits"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="24" colspan="3" align="left" valign="top" class="black1"><strong>Education  and Training :</strong><br />
								 <hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">
								<?php
								$educ_training = $row["educ_training"];
								$educ_training  = nl2br($educ_training);
								$educ_training = str_replace("&#61558", "-", $educ_training);
								echo $educ_training;
								?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="24" colspan="3" align="left" valign="top" class="black1"><strong>Details of your past and present Seagoing Work Experiences : </strong>
								<br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">
								<?php
								$seagoing_work = $row["seagoing_work"];
								$seagoing_work  = nl2br($seagoing_work );
								$seagoing_work = str_replace("&amp", " ", $seagoing_work);
								$seagoing_work = str_replace("#61558", "-", $seagoing_work);
								echo $seagoing_work ;
								?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><strong>Details  of your Non-Seagoing Work Experiences :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">
								<?php
								$non_seagoing_work = $row["non_seagoing_work"];
								$non_seagoing_work  = nl2br($non_seagoing_work );
								$non_seagoing_work = str_replace("&amp", " ", $non_seagoing_work);
								$non_seagoing_work = str_replace("#61558", "-", $non_seagoing_work);
								echo $non_seagoing_work;
								?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><strong>Prefer Job :</strong><br />
								<hr></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><?php  echo $row["prefer_job"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><strong>User verified via Email :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><?php  echo $row["verification"];?></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1">&nbsp;</td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><strong>Allow to view profile :</strong><br />
								<hr size="1"></td>
                              </tr>
                              <tr>
                                <td height="30" colspan="3" align="left" valign="top" class="black1"><?php  echo $row["view"];?></td>
                              </tr>
                            </table>
                          <?php
						  }
						  ?>
                            <br />
                            <br />
                            <br />
                            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="5">
                              <tr>
                                <td align="left" valign="top"><span class="style7"><strong>List of Companies Applied</strong>
                                </span>
                                <hr size="1"></td>
                              </tr>
                            </table>
                          <br />
                            <?php
							$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
							$query = "SELECT * from job_applicants where email = '$email'" or die("Error" . mysqli_error($link));
							$result = mysqli_query($link, $query);
							while($row = mysqli_fetch_array($result))
							{
							?>
                            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="5">
                              <tr>
                                <td width="47%" align="left" valign="top" class="style7"><?php  $company_name =  $row["company"];
							$company_name = @str_replace("^", "'", $company_name);
						  $company_name = @str_replace("*", "&", $company_name);
						  echo $company_name;
							?></td>
                                <td width="23%" align="left" valign="top" class="style7"><?php  echo $row["job_hiring"];?></td>
                                <td width="24%" align="left" valign="top" class="style7"><div align="right"><?php  echo $row["date"];?> </div></td>
                              </tr>
                            </table>
                          <?php
						  }
						  ?>
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="5">
                              <tr>
                                <td align="left" valign="top"><span class="style7"><strong>Transaction History </strong>
                                </span>
                                <hr size="1"></td>
                              </tr>
                            </table>
                          <br />
                            <?php
							$query = "SELECT * from action where seaman = '$email' order by code desc limit 0,20" or die("Error" . mysqli_error($link));
							$result = mysqli_query($link, $query);
							 while($row = mysqli_fetch_array($result))
							 {
							 ?>
                            <table width="95%" border="0" align="center" cellpadding="5" cellspacing="5">
                              <tr>
                                <td width="47%" align="left" valign="top" class="style7"><?php  echo $row["date"];?> - <?php  echo $row["time"];?></td>
                                <td align="left" valign="top" class="style7"><?php  echo $row["action"];?>
                                    <div align="right"></div></td>
                              </tr>
                            </table>
                          <?php 
						  }
						  ?>
                            <br />
                            <br />
                          <a href="alisin_ito.php?code=<?php  echo $coding;?>" onClick="javascript:return�confirm('Are�you�sure�you�want�to�delete this record?')">Delete this Record</a> </TD>
                      </TR>
                    </TBODY>
                  </TABLE></td>
                </tr>
				</table>
				<br />
              <div align="center">
              <a href="javascript:window.close()">Close this Window</a></div>
			  <?
			  mysqli_close($link);
			  mysqli_free_result($result);
			  ?>
			  <br />
			  </td>
          </tr>
        </table>
		</td>
	</tr>
</table>
</body>
</html>