<? include "./connect.php";?>
<html>
<head>
<title>Trabahong seaman, isang click nalang!</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
body, td, th {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #FFFFFF;
}
a {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 12px;
    color: #3399FF;
    font-weight: bold;
    text-decoration: none;
}
a:hover {
    color: #FF0000;
    text-decoration: underline;
}
.black1 {
    font-size: small; 
    color: #000000;
}
.style10 {
    color: #000000; 
    font-weight: bold;
}
input[type="text"], input[type="password"] {
    color: #000000; /* Ensure text is black */
}
</style>

<script>
function toggleVisibility(id) {
    var input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
</script>

</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br><br>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="top">
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td>
                  <form name="adminlogin" method="post" action="admin_login_verify.php">
                    <br>
                    <table width="100%"  border="0" cellspacing="5" cellpadding="5">
                      <tr>
                        <td width="32%" class="black1"><div align="right">Username : </div></td>
                        <td width="68%">
                          <input name="username" type="password" id="username2" maxlength="10">
                          <button type="button" onclick="toggleVisibility('username2')">Show/Hide</button>
                          <font color="red"><? echo $username_error; ?></font>
                        </td>
                      </tr>
                      <tr>
                        <td class="black1"><div align="right">Password : </div></td>
                        <td>
                          <input name="password" type="password" id="password2" maxlength="40">
                          <button type="button" onclick="toggleVisibility('password2')">Show/Hide</button>
                          <font color="red"><? echo $password_error; ?></font>
                        </td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>
                          <input type="submit" name="Submit3" value="Login">
                          <input type="reset" name="Submit22" value="Reset">
                        </td>
                      </tr>
                    </table>
                  </form>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</body>
</html>
