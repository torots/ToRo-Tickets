<?php

// This is ugly but it works for now

 function translate($TRANSLATION) {

   global $thisuser;

   if($thisuser){
   $thisuserLangID = $thisuser->getStaffLang();
   }

   if($thisuserLangID!='') {
     $sql="SELECT `$TRANSLATION` FROM ".LANGUAGE_TABLE." WHERE LANGUAGE_ID=".$thisuser->getStaffLang();
   }
   else {
     $get_default = "SELECT LANGUAGE_ID FROM ".CONFIG_TABLE;
     $qdefault = mysql_query($get_default);
     $defaultLang=mysql_result($qdefault,0);
     $sql="SELECT `$TRANSLATION` FROM ".LANGUAGE_TABLE." WHERE LANGUAGE_ID=".$defaultLang;
   }
   $query = mysql_query($sql) or trigger_error('MySQL Error: ' . mysql_error() . '<br />Query: '  . $query, E_USER_ERROR );
   $translation = mysql_result($query,0);
   $translation = stripslashes($translation);

   if($translation=='') {
     $get_default = "SELECT LANGUAGE_ID FROM ".CONFIG_TABLE;
     $qdefault = mysql_query($get_default);
     $defaultLang=mysql_result($qdefault,0);
     $sql="SELECT `$TRANSLATION` FROM ".LANGUAGE_TABLE." WHERE LANGUAGE_ID=".$defaultLang;
     $query = mysql_query($sql) or trigger_error('MySQL Error: ' . mysql_error() . '<br />Query: '  . $query, E_USER_ERROR );
     $translation = mysql_result($query,0);
     $translation = stripslashes($translation);
   }
   return $translation;

 }

?>
