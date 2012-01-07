<?php
/*

  toro Ticketing System
  Admin Panel -> Layout -> General (default)

*/


// add in actual php code here to pull data from db
// to handle post, to save data to db, etc.

if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));

$query = ('SELECT * FROM '.THEME_TABLE.' WHERE id=1');

$result = mysql_query($query) or die(mysql_error());

$result = mysql_fetch_assoc($result);
 
 $clientLogo = $result['clientlogo'];
 $clientLogoActive = $result['clientlogoactive'];
 $staffLogo = $result['stafflogo'];
 $staffLogoActive = $result['stafflogoactive'];
 $cilentColorArray = $result['clientcolorarray'];
 $staffColorArray = $result['staffcolorarray'];


?>

<div class="msg">


   </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <form name='generalLayout' action='<?php echo $PHP_SELF; ?>' method='post'>
 <tr><td>
   <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <tr class="header" >
     <td colspan=2><?php print translate("CLIENT");?>: <?php print translate("LABEL_GENERAL_SETTINGS");?></td>
    </tr>
    <tr class="subheader">
     <td colspan=2"><?php print translate("CLIENT_SIDE_SETTINGS_NOTE");?></td>
    </tr>
    <tr>
     <th><b><?php print translate("LOGO");?></b></th>
     <td>
      <input type="hidden" name="dbClientLogo" value="<?php if($clientLogoActive==1) { echo $clientLogo; } else { echo './images/logo2.jpg'; } ?>">
      <input type="text" name="clientLogo" value="<?php if($clientLogoActive==1) { echo $clientLogo; } else { echo './images/logo2.jpg'; } ?>"> &nbsp; <span style="font-weight:normal;">(<?php print translate("LABEL_DEFAULT");?>: ./images/logo2.jpg)</span></td>
    <tr>
   </table>
  </td>
 </tr>
</table>


<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <tr><td>
   <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <tr class="header" >
     <td colspan=2><?php print translate("LABEL_STAFF");?>: <?php print translate("LABEL_GENERAL_SETTINGS");?></td>
    </tr>
    <tr class="subheader">
     <td colspan=2"><?php print translate("STAFF_SIDE_SETTINGS_NOTE");?></td>
    </tr>
    <tr>
     <th><b><?php print translate("LOGO");?></b></th>
     <td>
      <input type="hidden" name="dbStaffLogo" value="<?php if($staffLogoActive==1) { echo $staffLogo; } else { echo '../images/logo1.png'; } ?>">
      <input type="text" name="staffLogo" value="<?php if($staffLogoActive==1) { echo $staffLogo; } else { echo '../images/logo1.png'; } ?>"> &nbsp; <span style="font-weight:normal;">(<?php print translate("LABEL_DEFAULT");?>: ../images/logo1.png)</span></td>
    </tr>
 </table>
  </td>
 </tr>
</table>
<table width="100%" border=0 cellspacing=2 cellpadding=2>
 <tr>
  <td align=center colspan="2">
      <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SAVE");?>'>
      <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
      </form>
  </td>
 </tr>
</table>
</div>
