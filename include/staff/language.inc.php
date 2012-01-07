   <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
   <form name='defaultLangForm' action='<?php echo $PHP_SELF; ?>' method='post'>
    <tr class="header" >
     <td colspan=2><?php print translate("SYSTEM_LANGUAGES");?></td>
    </tr>
    <tr class="subheader">
     <td colspan=2"><?php print translate("SELECT_DEFAULT_LANGUAGE");?></td>
    </tr>
    <tr>
     <th><?php print translate("LABEL_LANGUAGE");?>:</th><td>
      <select name="defaultLang" id="defaultLang">
      <?php

      $sql = "SELECT LANGUAGE_ID,ABBREVIATION,LANGUAGE_NAME FROM ".LANGUAGE_TABLE;
      $result = mysql_query($sql)or die(mysql_error());

      $checkLang = "SELECT LANGUAGE_ID FROM ".CONFIG_TABLE;
      $resultLang = mysql_query($checkLang)or die(mysql_error());
      while($thisLang = mysql_fetch_array($resultLang)){
      $systemLang = $thisLang['LANGUAGE_ID'];
      }

      while($lang = mysql_fetch_array($result)){
      if($systemLang == $lang['LANGUAGE_ID']){ $selected = 'selected'; }else{ $selected = ''; }
        print "<option value='".$lang['LANGUAGE_ID']."' ".$selected.">".$lang['LANGUAGE_NAME']."</option>";
       }
      ?>
      </select>
      <input class="button" type="submit" name="submit" value='<?php print translate("UPDATE");?>'></form>

      &nbsp;<p><font class="error"><?php echo $errors['ostlang']; ?></font></p>
     </td>
    </tr>
    <tr class="subheader">
     <td colspan=2><?php print translate("ADD_LANGUAGE");?></td>
    </tr>
    <tr>
     <th><?php print translate("LABEL_LANGUAGE");?>:</th>
     <td><form name='newLangForm' action='<?php echo $PHP_SELF; ?>' method='post'><?php print translate("LABEL_NAME");?> <input type='text' name='newLang' /><input type='submit' class='button' value='<?php print translate("ADD");?>' name='SubmitLang' /></form></td>
    </tr>

    </table>
    
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <form name='addFieldForm' action='<?php echo $PHP_SELF; ?>' method='post'>    
     <tr class="header">
      <td colspan=2><?php print translate("ADD_FIELD");?></td>
     </tr>
     <tr class="subheader">
      <td colspan=2><?php print translate("ADD_FIELD_NOTE");?></td>
     </tr>
     <tr><th><?php print translate("FIELD_NAME");?></th><td><input type='text' name='fieldName' /><input type='submit' class='button' name='addField' value='<?php print translate("ADD");?>' /></td></tr>
	      </form>
    </table>

    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <form name='editLangForm' action='<?php echo $PHP_SELF; ?>' method='post'>
    <tr class="header" >
     <td colspan=2><?php print translate("EDIT_LANGUAGE");?></td>
    </tr>

    <tr class="subheader">
    <td colspan=2><?php print translate("EDIT_LANGUAGE_NOTE");?></td>
    </tr>

    <tr>
    <th>Language:</th>
     <td>
     <select name="editLang" id="editLang">
      <?php

      $sql = "SELECT LANGUAGE_ID,LANGUAGE_NAME FROM ".LANGUAGE_TABLE;
      $result = mysql_query($sql)or die(mysql_error());

      while($lang = mysql_fetch_array($result)){
      if($_POST['editLang'] == $lang['LANGUAGE_ID'] || $_POST['LANGUAGE_ID'] == $lang['LANGUAGE_ID']){ $selected = 'selected'; }else{ $selected = ''; }
        print "<option value='".$lang['LANGUAGE_ID']."' ".$selected.">".$lang['LANGUAGE_NAME']."</option>";
       }
      ?>
      </select><input type='submit' class='button' value='<?php print translate("FETCH");?>' />
      <?php
      print "<input type='hidden' value='".$languageID."' name='SETLANG' />";
      ?>
      </form>
      </td>
     </tr>

     <form name='updateLangForm' action='<?php echo $PHP_SELF; ?>' method='post'>
     <?php

     $sql = "SELECT LANGUAGE_ID FROM ".CONFIG_TABLE;
      $result = mysql_query($sql)or die(mysql_error());
      while($thisLang = mysql_fetch_array($result)){
      $defaultLang = $thisLang['LANGUAGE_ID'];
      

     if((!$_POST['updateLang']) && ($_POST['editLang'])){
       $sql = "SELECT * FROM ".LANGUAGE_TABLE." WHERE LANGUAGE_ID=".$_POST['editLang'];
     }elseif($_POST['updateLang']){
       $sql = "SELECT * FROM ".LANGUAGE_TABLE." WHERE LANGUAGE_ID=".$_POST['LANGUAGE_ID'];
     }else{
       $sql = "SELECT * FROM ".LANGUAGE_TABLE." WHERE LANGUAGE_ID=".$defaultLang;
     }
     } // End System Default Language ID loop  

       $result = mysql_query($sql)or die(mysql_error());

	while ($row = mysql_fetch_assoc($result)) { 

        foreach($row as $key => $value) {
         if($key!='LANGUAGE_ID'){
         print "<tr><th>" .$key. "</th><td><input type='text' size='65' name='".$key."' value=\"" .$value. "\" /></td></tr>";
         }else{
	  print "<input type='hidden' value='".$row['LANGUAGE_ID']."' name='LANGUAGE_ID' />";
	 }
        }
       }


      ?>
      <tr><td colspan='2' align='right'><input type='submit' value='<?php print translate("UPDATE");?>' class='button' name='updateLang' /></td></tr>
      </form>
    </table>

