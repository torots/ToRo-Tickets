<?php
/*
 toro ticketing system

*/

if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

?>

<div class="msg">

<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <form name='colorLayout' action='<?php echo $PHP_SELF; ?>' method='post'>
 <tr><td>
   <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <tr class="header" >
     <td colspan=2><?php print translate("COLOR_SETTINGS");?></td>
    </tr>
    <tr class="subheader">
     <td colspan=2"><?php print translate("COLOR_SETTINGS_NOTE");?></td>
    </tr>
    <tr>
     <th><b>background-color</b></th>
     <td><input type="text" name="" value=""></td>
    <tr>
    <tr>
     <th><b>button-color</b></th>
     <td><input type="text" name="" value=""></td>
    <tr>
    <tr>
     <th><b>table-color</b></th>
     <td><input type="text" name="" value=""></td>
    <tr>    
    <tr>
     <th><b>div-color</b></th>
     <td><input type="text" name="" value=""></td>
    <tr>

   </table>
  </td>
 </tr>
</table>
