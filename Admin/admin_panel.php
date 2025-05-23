<?php 
session_start();
include "./connect.php";

if(!isset($_SESSION["admin_account"]) || !isset($_SESSION["admin_pass"])) {
  header("location: admin.php");
  exit;
} 

$link = mysqli_connect($dbhost, $dbusername, $dbuserpassword, $dbname);

if (!$link) {
  die("Connection failed: " . mysqli_connect_error());
}

$query = "DELETE FROM job_applicants WHERE mark='x'";
if (!mysqli_query($link, $query)) {
  die("Error deleting records: " . mysqli_error($link));
}
?>
<html>
<head>
<?php 
include "./meta.php";
?>
<style type="text/css">
.style1 {color: #000000}
.style11 {font-size:small; color: #FF0000;}
.style11 {color: #000000}
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br>
<table width="53%" border="0" align="center" cellpadding="5" cellspacing="2" bgcolor="#F5F5F5">
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Employer</strong></td>
  </tr>
  <tr>
  <td width="38%"><a href="company_list_admin.php">Newly Registered Company</a></td>
  <td width="62%"><div align="right" class="black1"><strong>
    <?php
    $query = "SELECT COUNT(*) as count FROM employer WHERE post = ''";
    $result = mysqli_query($link, $query);
    if ($result) {
      $data = mysqli_fetch_assoc($result);
      echo $data["count"];
    } else {
      echo "Error: " . mysqli_error($link);
    }
    ?>
  </strong></div></td>
  </tr>
  <tr>
  <td colspan="2"><a href="admin_employer_list.php">Company Listing</a></td>
  </tr>
  <!-- <tr>
  <td colspan="2"><a href="admin_employer_list2.php">Company</a></td>
  </tr> -->
  <tr>
  <td colspan="2"><a href="date.php">Company (order by date)</a></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000" class="style18"><strong>Seaman Registration</strong></td>
  </tr>
  <tr>
  <td class="black1"><div align="right">Display Seaman:</div></td>
  <td><a href="seaman_listing2.php">via Date Registered</a></td>
  </tr>
  <tr>
  <td class="black1"><div align="right">Search via Last Name:</div></td>
  <td><form action="search_lastname.php" method="post" name="form1">
    <strong>
    <label>
    <input name="last_name" type="text" id="last_name" size="40">
    </label>
    <label>
    <input type="submit" name="Submit" value="Search">
    </label>
    </strong>
  </form></td>
  </tr>
  <tr>
  <td class="black1"><div align="right">Search via Middle Name:</div></td>
  <td><form action="search_middlename.php" method="post" name="form1">
    <strong>
    <label>
    <input name="middle_name" type="text" id="middle_name" size="40">
    </label>
    <label>
    <input type="submit" name="Submit3" value="Search">
    </label>
    </strong>
  </form></td>
  </tr>
  <tr>
  <td class="black1"><div align="right">Search via First Name:</div></td>
  <td><form action="search_firstname.php" method="post" name="form1">
    <strong>
    <label>
    <input name="first_name" type="text" id="first_name" size="40">
    </label>
    <label>
    <input type="submit" name="Submit32" value="Search">
    </label>
    </strong>
  </form></td>
  </tr>
  <tr>
  <td class="black1"><div align="right">Search Seaman via Email:</div></td>
  <td><form action="search_email.php" method="post" name="form1">
    <strong>
    <label>
    <input name="email" type="text" id="email" size="40" maxlength="200">
    </label>
    <label>
    <input type="submit" name="Submit2" value="Search">
    </label>
    </strong>
  </form></td>
  </tr>
  <tr>
  <td class="black1"><div align="right">Search Special Character:</div></td>
  <td><form action="search_special.php" method="post" name="form1">
    <strong>
    <label>
    <input name="special" type="text" id="special" size="40" maxlength="200">
    </label>
    <label>
    <input type="submit" name="Submit23" value="Search">
    </label>
    </strong>
  </form></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Job Posting</strong></td>
  </tr>
  <tr>
  <td colspan="2"><a href="add_seaman_job.php">Add New Job | Job Category</a></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Others</strong></td>
  </tr>
  <tr>
  <td><a href="testimonials_admin.php">Testimonials</a></td>
  <td><div align="right"><strong>
    <?php
    $query = "SELECT COUNT(*) as count FROM testimonials WHERE post = ''";
    $result = mysqli_query($link, $query);
    if ($result) {
      $data = mysqli_fetch_assoc($result);
      echo $data["count"];
    } else {
      echo "Error: " . mysqli_error($link);
    }
    ?>
  </strong></div></td>
  </tr>
  <tr>
  <td colspan="2"><a href="admin_action_list2.php">Display Action History</a><span class="style7"> (Summary of all Employer and Seaman transaction)</span></td>
  </tr>
  <tr>
  <td colspan="2"><a href="admin_action_list3.php">Display Employer Login</a> <span class="style7">(Display Employer Successful Login by Date)</span></td>
  </tr>
  <tr>
  <td colspan="2"><a href="employer_applicant_date1.php"></a></td>
  </tr>
  <tr>
  <td colspan="2" align="left" valign="top"><span class="style1"><strong>Display Applicants / Company / Day</strong></span><br/><br/>
    <form action="employer_applicant_date2.php" method="post" name="form1">
    <strong>
    <label>
      <input name="date2" type="text" id="date2" size="40" maxlength="200">
    </label>
    </strong>
    <label><span class="style7">(YYYY-MM-DD)</span></label>
    <strong>
    <label><br>
      <input type="submit" name="Submit6" value="Display Now">
    </label>
    </strong><br>
    </form>
<br/><hr><br/>
<span class="style1"><strong>Display Applicants / Date</strong></span><br/><br/>
<form action="employer_applicant_date3.php" method="post" name="form1">
    
    <label>
      <span class="style1"><strong class="style7">Starting Date:</strong><br>
      <input name="date_start" type="text" id="date_start" size="40" maxlength="200">
      <span class="style7">(YYYY-MM-DD)</span><br><br>
      <strong class="style7">End Date:</strong><br> 
    </span></label>
    <span class="style1">
    <input name="date_end" type="text" id="date_end" size="40" maxlength="200">
    <label><span class="style7">(YYYY-MM-DD)</span><br>
    </label>
    <label>
      <input type="submit" name="Submit222" value="Display Now">
    </label>
    </span>
  </form>
<br/><hr><br/>
<span class="style1"><strong>Total Applicants / Company / Date</strong></span><br/><br/>
<form action="employer_applicant_date30.php" method="post" name="form1">
  <label> <span class="style7"><strong>Company Name:</strong></span><span class="style1"><strong><br>
  <select name="company_code" id="company_code">
    <?php 
    $query = "SELECT * FROM employer ORDER BY company ASC";
    $result = mysqli_query($link, $query);
    while ($row = mysqli_fetch_array($result)) {
      echo '<option value="' . $row["company"] . '">' . $row["company"] . '</option>';
    }
    ?>
  </select>
  <br><br>
  </strong></span><span class="style7"><strong>Starting Date:</strong></span><span class="style1"><br>
  <input name="date_start1" type="text" id="date_start1" size="40" maxlength="200">
  <span class="style7">(YYYY-MM-DD)</span><br><br>
  <strong class="style7">End Date:</strong><br>
  </span></label>
  <span class="style1">
  <input name="date_end1" type="text" id="date_end1" size="40" maxlength="200">
  <label><span class="style7">(YYYY-MM-DD)</span><br>
    </label>
  <input type="submit" name="Submit7" value="Display Now">
  </span>
</form>
<br/><hr><br/>
<span class="style1"><strong>Total Applicants / Job Position / Date</strong></span><br/>
<span class="style7"><br/>
</span>
<form action="admin_display_applicant_job_date.php" method="post" name="form1" target="_blank">
    <span class="style7">
    <label><strong>Starting Date:</strong></label>
    </span>
    <label><span class="style11"><br>
      <input name="date_start" type="text" id="date_start" size="40" maxlength="200">
      </span><span class="style7">(YYYY-MM-DD)</span><span class="style11"><br><br>
      <strong class="style7">End Date:</strong><br>
    </span></label>
    <span class="style11">
    <input name="date_end" type="text" id="date_end" size="40" maxlength="200">
    </span>
    <label><span class="style7">(YYYY-MM-DD)</span></label>
    <span class="style11">
    <label><br>
    </label>
    <input type="submit" name="Submit5" value="Display Now">
    </span>
  </form>


  <br/><hr><br/>
  <span class="style1"><strong>Graph of Total Newly Registered Seaman / Date</strong></span><br/>
  <span class="style7"><br/>
  </span>
  <form action="includes/admin_display_new_applicants_graph.php" method="post" name="form1" target="_blank">
    <span class="style7">
    <p><strong>Starting Date: 2025-02-11(Phase 2)</strong></p>
    </span>
    <br>
    
      <strong class="style7">End Date: Present Date</strong><br>
    
    <p><span class="style7">(YYYY-MM-DD)</span></p>
    <span class="style11">
    <label><br>
    </label>
    <input type="submit" name="Submit5" value="Display Now">
    </span>
  </form>
  
  <br/><hr><br/>

  <tr>
  <td colspan="2" align="left" valign="top"><span class="style1"><strong>Display Job Seekers based on Seagoing Work</strong></span><br/><br/>
    <form action="seagoing_work_search.php" method="post" name="form1">
    <strong>
    <label>
      <input name="seagoing_work" type="text" id="seagoing_work" size="40" maxlength="200">
    </label>
    </strong>
    <label><span class="style7">Search</span></label>
    <strong>
    <label><br>
      <input type="submit" name="Submitttt" value="Display Now">
    </label>
    </strong><br>
    </form>


  </td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Applicant Statistics</strong></td>
  </tr>
  <tr>
  <td colspan="2"><a href="admin_applicant_list3.php">Display Applicant List</a> <span class="style7">(Summary of Applicants order by date)</span></td>
  </tr>
  <tr>
  <td colspan="2"><a href="employer_applicant_date1.php">Total Number of Applicants / Day</a></td>
  </tr>
  <tr>
  <td colspan="2"><a href="total_employer_applicant_date.php">Total Applicants / Company</a><span class="style7">(Summary of Applicants per Company)</span></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Clear Database</strong></td>
  </tr>
  <tr>
  <td colspan="2"><a href="empty_seaman.php">Remove Seaman Login Failed</a></td>
  </tr>
  <tr>
  <td colspan="2"><a href="empty_seaman2.php">Remove Seaman Login Successful</a></td>
  </tr>
  <tr>
  <td colspan="2"><a href="empty_seaman3.php">Remove Seaman Update Profile</a></td>
  </tr>
  <tr>
  <td colspan="2"><a href="empty_company.php">Empty Company Login Failed</a></td>
  </tr>
  <tr>
  <td colspan="2"><a href="empty_company2.php">Empty Company Login Successful</a></td>
  </tr>
  <tr>
  <td colspan="2"><a href="empty_company3.php">Empty Modify Job Details</a></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Website Statistics</strong></td>
  </tr>
  <tr>
  <td colspan="2"><a href="side_compute.php">Update Website Statistics</a></td>
  </tr>
  <tr>
  <td><div align="right"><span class="style1">Email Code:</span></div></td>
  <td><form name="form2" method="post" action="edit_emailsw.php">
    <label>
    <?php
    $query = "SELECT * FROM coding";
    $result = mysqli_query($link, $query);
    if ($result) {
      $row = mysqli_fetch_array($result);
    ?>
    <input name="emailcode" type="text" id="emailcode" value="<?php echo $row['code']; ?>">
    <?php
    } else {
      echo "Error: " . mysqli_error($link);
    }
    ?>
    </label>
    <label>
    <input type="submit" name="Submit4" value="Submit">
    </label>
  </form></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#000000"><strong>Log Out</strong></td>
  </tr>
  <tr>
  <td colspan="2"><a href="logoff.php">Log Off</a></td>
  </tr>
</table>
<?php 
mysqli_close($link);
?>
<br>
<br>
</body>
</html>
