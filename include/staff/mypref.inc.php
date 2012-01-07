<?php
if(!defined('OSTSCPINC') || !is_object($thisuser) || !$rep) die('Kwaheri');
?>
<div class="msg">&nbsp;<?php echo $LANG['TITLE_MY_PREFERENCES'];?></div>
<table width="100%" border="0" cellspacing=2 cellpadding=3>
 <form action="profile.php" method="post">
 <input type="hidden" name="t" value="pref">
 <input type="hidden" name="id" value="<?=$thisuser->getId()?>">
    <tr>
     <td><?php print translate("LABEL_LANGUAGE");?>:</td><td>
      <select name="myLang" id="myLang">
      <?php

      $sql = "SELECT LANGUAGE_ID,LANGUAGE_NAME FROM ".LANGUAGE_TABLE;                          
      $result = mysql_query($sql)or die(mysql_error());

      $checkLang = "SELECT LANGUAGE_ID FROM ".CONFIG_TABLE;
      $resultLang = mysql_query($checkLang)or die(mysql_error());
      while($thisLang = mysql_fetch_array($resultLang)){
      $systemLang = $thisLang['LANGUAGE_ID'];
      }

      $mySQL = "SELECT LANGUAGE_ID FROM ".STAFF_TABLE." WHERE staff_id=".$thisuser->getId();
      $myResult = mysql_query($mySQL)or die(mysql_error());
      while($staffLang = mysql_fetch_array($myResult)){
      $myLang = $staffLang['LANGUAGE_ID'];
      print "My Language is " .$myLang;
      }

      while($lang = mysql_fetch_array($result)){    
      if(($systemLang == $lang['LANGUAGE_ID']) && ($myLang==0)){ $selected = 'selected'; }else{ $selected = ''; }
      if(($myLang == $lang['LANGUAGE_ID']) && ($myLang!=0)){ $selected = 'selected'; }else{ $selected = ''; }
        print "<option value='".$lang['LANGUAGE_ID']."' ".$selected.">".$lang['LANGUAGE_NAME']."</option>";             
       }
      ?>
      </select>
      </td>
    </tr>
    <tr>
        <td width="145" nowrap><?php print translate("TEXT_MAXIMUM_PAGE_SIZE");?>:</td>        
        <td>
            <select name="max_page_size">
                <?
                $pagelimit=$rep['max_page_size']?$rep['max_page_size']:$cfg->getPageSize();
                for ($i = 5; $i <= 50; $i += 5) {?>
                    <option <?=$pagelimit== $i ? 'SELECTED':''?>><?=$i?></option>
                <?}?>
            </select> <?php print translate("LABEL_RESULTS_PER_PAGE");?>.
        </td>
    </tr>
    <tr>
        <td nowrap><?php print translate("TEXT_AUTO_REFRESH_RATE");?>:</td>
        <td>
            <input type="input" size=3 name="auto_refresh_rate" value="<?=$rep['auto_refresh_rate']?>">
            <?php print translate("TEXT_REFRESH_DESCRIPTION");?>
        </td>
    </tr>
    <tr>
        <td nowrap><?php print translate("TEXT_PREFERRED_TIME_ZONE");?>:</td>
        <td>
            <select name="timezone_offset">
                <?
                $gmoffset  = date("Z") / 3600; //Server's offset.
                $currentoffset = ($rep['timezone_offset']==NULL)?$cfg->getTZOffset():$rep['timezone_offset'];
                echo"<option value=\"$gmoffset\">Server Time (GMT $gmoffset:00)</option>"; //Default if all fails.
                $timezones= db_query('SELECT offset,timezone FROM '.TIMEZONE_TABLE);
                while (list($offset,$tz) = db_fetch_row($timezones)){
                    $selected = ($currentoffset==$offset) ?'SELECTED':'';
                    $tag=($offset)?"GMT $offset ($tz)":" GMT ($tz)"; ?>
                    <option value="<?=$offset?>"<?=$selected?>><?=$tag?></option>
                <?}?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php print translate("TEXT_DAYLIGHT_SAVINGS");?>:</td>
        <td>
            <input type="checkbox" name="daylight_saving" <?=$rep['daylight_saving'] ? 'checked': ''?>><?php print translate("TEXT_OBSERVE_DAYLIGHT_SAVINGS");?>
        </td>
    </tr>
   <tr><td><?php print translate("TEXT_CURRENT_TIME");?>:</td>
        <td><b><i><?=Format::date($cfg->getDateTimeFormat(),Misc::gmtime(),$rep['timezone_offset'],$rep['daylight_saving'])?></i></b></td>
    </tr>  
    <tr>
        <td>&nbsp;</td>
        <td><br>
            <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SUBMIT");?>'>
            <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
            <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="profile.php"'>
        </td>
    </tr>
 </form>
</table>
