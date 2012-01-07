<?php

require('./include/class.phpextensions.php');
$modules = new moduleCheck();
if($modules->isLoaded('ldap')) { // Test if ldap is loaded
  //echo 'LDAP Loaded<br />';
} else {
  echo 'ERROR: LDAP is not loaded! Please contact your system administrator.';
}

?>

<hr>

<?php phpinfo(); ?>
