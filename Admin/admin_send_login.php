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
$company_email = $_GET["company_email"];
$link = mysqli_connect($dbhost,$dbusername,$dbuserpassword,$dbname) or die("Error " . mysqli_error($link));
$query = "SELECT id,company,post,secret,email FROM employer WHERE email LIKE '%$company_email%'" or die("Error" . mysqli_error($link));
$result = mysqli_query($link, $query);
while($row = mysqli_fetch_array($result))
{
 $newid = $row["id"];
 $secret = $row['secret'];
 $company = $row["company"];
 $email_message = "<font face = 'verdana' size = '2'>Hello $company,
  <br /><br />
  Listed below is your PinoySeaman login details.
  <br /><br />
  Employer ID : $newid
  <br /> 
  Password : $secret
  </font>
  ";

  $email_message = nl2br($email_message);
  $mime = $email_message;


$headers = 'From: PinoySeaman <infopinoyseaman.com>' . "\r\n" .
'Reply-To: PinoySeaman <infopinoyseaman.com>' . "\r\n" .
'MIME-Version: 1.0' . "\r\n" .
'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
'X-Mailer: PHP/' . phpversion(); 

 mail($company_email, "PinoySeaman Account Recovery.", $email_message, $headers);

echo "Login account sent to $company_email </font>";
exit;
}
?>