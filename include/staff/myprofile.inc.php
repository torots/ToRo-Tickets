<?php
if(!defined('OSTSCPINC') || !is_object($thisuser)) die('Kwaheri');

?>
<div class="msg"><?php print translate("TEXT_MY_PROFILE_INFO");?></div>
<table width="100%" border="0" cellspacing=0 cellpadding=2>
 <form action="profile.php" method="post">
 <input type="hidden" name="t" value="info">
 <input type="hidden" name="id" value="<?=$thisuser->getId()?>">
    <tr>
        <td width="110"><b><?=translate('LABEL_USERNAME') ?>:</b></td>
        <td>&nbsp;<?=$thisuser->getUserName()?></td>
    </tr>
    <tr>
        <td><?php print translate("LABEL_FIRSTNAME");?>:</td>
        <td><input type="text" name="firstname" value="<?=$rep['firstname']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['firstname']?></font></td>
    </tr>
    <tr>
        <td><?php print translate("LABEL_LASTNAME");?>:</td>
        <td><input type="text" name="lastname" value="<?=$rep['lastname']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['lastname']?></font></td>
    </tr>
    <tr>
        <td><?php print translate("LABEL_EMAIL_ADDRESS");?>:</td>
        <td><input type="text" name="email" size=25 value="<?=$rep['email']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['email']?></font></td>
    </tr>
    <tr>
        <td><?php print translate("LABEL_OFFICE_PHONE");?>:</td>
        <td>
            <input type="text" name="phone" value="<?=$rep['phone']?>" ><font class="error">&nbsp;<?=$errors['phone']?></font>&nbsp;Ext&nbsp;
            <input type="text" name="phone_ext" size=6 value="<?=$rep['phone_ext']?>" >
            <font class="error">&nbsp;<?=$errors['phone_ext']?></font>
        </td>
    </tr>
    <tr>
        <td><?php print translate("LABEL_CELL_PHONE");?>:</td>
        <td><input type="text" name="mobile" value="<?=$rep['mobile']?>" >
            &nbsp;<font class="error">&nbsp;<?=$errors['mobile']?></font></td>
    </tr>
    <tr>
        <td valign="top"><?php print translate("SIGNATURE");?>:</td>
        <td><textarea name="signature" cols="21" rows="5" style="width: 60%;"><?=$rep['signature']?></textarea></td>
    </tr>
    <tr><td>&nbsp;</td>
        <td> <br/>
            <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SAVE");?>'>
            <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
            <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="index.php"'>
        </td>
    </tr>
 </form>
</table> 
