<?php
if(!defined('OSTSCPINC') || !is_object($thisuser)) die('Kwaheri');
$rep=Format::htmlchars($rep);
?>
<div class="msg"><?php print translate("LABEL_CHANGE_PASSWORD");?></div>
<table width="100%" border="0" cellspacing=0 cellpadding=2>
    <form action="profile.php" method="post">
    <input type="hidden" name="t" value="passwd">
    <input type="hidden" name="id" value="<?=$thisuser->getId()?>">
    <tr>
        <td width="120"><?php print translate("TEXT_CURRENT_PASSWORD");?>:</td>
        <td>
            <input type="password" name="password" AUTOCOMPLETE=OFF value="<?=$rep['password']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['password']?></font></td>
    </tr>
    <tr>
        <td><?php print translate("TEXT_NEW_PASSWORD");?>:</td>
        <td>
            <input type="password" name="npassword" AUTOCOMPLETE=OFF value="<?=$rep['npassword']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['npassword']?></font></td>
    </tr>
    <tr>
        <td><?php print translate("TEXT_VERIFY_PASSWORD");?>:</td>
        <td>
            <input type="password" name="vpassword" AUTOCOMPLETE=OFF value="<?=$rep['vpassword']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['vpassword']?></font></td>
    </tr>
    <tr><td >&nbsp;</td>
         <td><br/>
            <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SUBMIT");?>'>
            <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
            <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="profile.php"'>
        </td>
    </tr>
    </form>
</table> 
