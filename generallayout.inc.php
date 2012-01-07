<?php
/*

  toro Ticketing System
  Admin Panel -> Layout -> General (default)

*/


// add in actual php code here to pull data from db
// to handle post, to save data to db, etc.

if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

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
     <td colspan=2>Layout:General Settings</td>
    </tr>
    <tr class="subheader">
     <td colspan=2">Client Side General</td>
    </tr>
    <tr>
     <th><b>Logo</b></th>
     <td>
      <input type="hidden" name="dbClientLogo" value="<?php if($clientLogoActive==1) { echo $clientLogo; } else { echo './images/logo2.jpg'; } ?>">
      <input type="text" name="clientLogo" value="<?php if($clientLogoActive==1) { echo $clientLogo; } else { echo './images/logo2.jpg'; } ?>"> &nbsp; <span style="font-weight:normal;">(default: ./images/logo2.jpg)</span></td>
    <tr>


   </table>
  </td>
 </tr>
</table>


<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <tr><td>
   <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <tr class="header" >
     <td colspan=2>Layout:General Settings</td>
    </tr>
    <tr class="subheader">
     <td colspan=2">Staff Side General</td>
    </tr>
    <tr>
     <th><b>Logo</b></th>
     <td>
      <input type="hidden" name="dbStaffLogo" value="<?php if($staffLogoActive==1) { echo $staffLogo; } else { echo '../images/logo1.png'; } ?>">
      <input type="text" name="staffLogo" value="<?php if($staffLogoActive==1) { echo $staffLogo; } else { echo '../images/logo1.png'; } ?>"> &nbsp; <span style="font-weight:normal;">(default: ../images/logo1.png)</span></td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<table width="100%" border=0 cellspacing=2 cellpadding=2>
 <tr>
  <td align=center colspan="2">
      <input class="button" type="submit" name="submit" value="Save Changes">
      <input class="button" type="reset" name="reset" value="Reset Changes">
      </form>
  </td>
 </tr>
</table>
</div>
