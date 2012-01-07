<?php defined('OSTSCPINC') or die('Invalid path');?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;/>
<title>toroTS:: SCP Login</title>
<!-- I have no idea why but BOTH of these must be present in order for the css file to load, F.N.Weird.-->
<link rel="stylesheet" href="../styles/staff_login.css" type="text/css" />
<link rel="stylesheet" type="text/css" href="../styles/staff_login.css" />
<meta name="robots" content="noindex" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="pragma" content="no-cache" />
</head>
<body id="loginBody" onload="document.loginForm.username.focus()">

<?php
$getlogo = "SELECT stafflogo FROM ".THEME_TABLE." WHERE stafflogoactive = '1'";
$result = mysql_query($getlogo) or die(mysql_error());
if (mysql_num_rows($result)==0)
 $logopath = "../images/logo1.png";
else
 $logopath = mysql_result($result,0);
?>

<!--<h1 id="logo"><a href="index.php">toroTS Staff Control Panel</a></h1>-->

<!--<h1 id="logo"><a href="index.php"><img src="<?php echo $logopath; ?>" alt="toro Staff Control Panel"></a></h1>-->
<center id="logo"><a href="index.php"><img border=0 src="<?php echo $logopath; ?>" alt="toro Staff Control Panel"></a></center>

<div id="loginBox">
	<h1 style="clear: both;"><?php print$msg?></h1>
	<form action="login.php" method="post" name="loginForm">
	<input type="hidden" name=do value="scplogin" />
    <table border=0 align="center">
        <tr><td width=100px align="right"><b><?php print $labelUsername; ?></b>:</td><td><input type="text" name="username" id="name" value="" /></td></tr>
        <tr><td align="right"><b><?php print $labelPassword; ?></b>:</td><td><input type="password" name="passwd" id="pass" /></td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;&nbsp;<input class="button" type="submit" name="submit" value="Login" /></td></tr>
    </table>
</form>
</div>
<div id="copyRights">Copyright &copy; <a href='http://www.torots.com' target="_blank">torots.com</a></div>
</body>
</html>
