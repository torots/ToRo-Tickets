<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die($trl->translate("TEXT_ACCESS_DENIED"));

require('../include/class.phpextensions.php');
$modules = new moduleCheck();
if($modules->isLoaded('ldap')) { // Test if ldap is loaded
  //echo 'LDAP Loaded<br />';
} else {
  echo '<center><p id="errormessage"> ERROR: LDAP is not loaded! Please contact your system administrator.</p></center>';
}


//$auths= db_query('SELECT authentication_type,authentication_data FROM '.CONFIG_TABLE.' WHERE id=1');

$sql = "SELECT authentication_type,authentication_server,authentication_ldapport,authentication_ldapdn,authentication_ldapver,authentication_failover FROM ".CONFIG_TABLE." WHERE id=1";
$result = mysql_query($sql) or die (mysql_error());
$auth_type = mysql_result($result,0,"authentication_type");
$auth_data = mysql_result($result,0,"authentication_server");
$auth_port = mysql_result($result,0,"authentication_ldapport");
$auth_dn = mysql_result($result,0,"authentication_ldapdn");
$auth_ver = mysql_result($result,0,"authentication_ldapver");
$auth_fail = mysql_result($result,0,"authentication_failover");

?>
<div><b><?=$showing?></b></div>

<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <tr>
  <td>
   This sub menu controls how OSTicket will authenticate staff.
   <br>
   option(s):<br>
   <br>
   <!--<font color=red><b>note: this is under construction!</b> </font><br><br>-->

  </td></tr>

  <tr><th><?php translate('LABEL_AUTHENTICATION')?></th></tr>
  <tr><td>

   <form name="authentication" action="admin.php?t=auth" method="post">
   <input type="radio" id="auth" name="auth" value="1" <?php if($auth_type==1) echo 'checked'; ?>/><b> OSTicket (Default)</b><br />
   <input type="radio" id="auth" name="auth" value="2" <?php if($auth_type==2) echo 'checked'; ?>/><b> LDAP/Active Directory</b><br>

<table>
 <tr>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;server name:</td>
  <td><input id="ldap" type="text" name="ldapserver" value="<?php if($auth_data) { echo $auth_data; } ?>"/></td>
 </tr>
 <tr>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;server port:</td>
  <td><input id="ldap" type="text" name="ldapport" value="<?php if($auth_port) { echo $auth_port; } ?>"/></td>
 <tr>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Base DN:</td>
  <td><input id="ldap" type="text" name="ldapdn" value="<?php if($auth_dn) { echo $auth_dn; } ?>"/></td>
 </tr>
 <tr>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LDAP Version:</td>
  <td><input id="ldap" type="text" name="ldapver" value="<?php if($auth_ver) { echo $auth_ver; } ?>"/></td>
 </tr>
 <tr>
  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OSTicket Failover?</td>
  <td><input type="checkbox" name="enable_failover" value="1" <?php if($auth_fail==1) echo 'checked'; ?>/> Enable Failover</td>
 </tr>
</table>

   <input type="radio" id="auth" name="auth" value="3" <?php if($auth_type==3) echo 'checked'; ?>/><b> .htaccess (not implemented)</b><br />
   <br>
   
   <input class="button" type="submit" name="submit" value="Save Changes">
   <input class="button" type="reset" name="reset" value="Reset Changes">


  </form> 

  </td></tr>
</table>

