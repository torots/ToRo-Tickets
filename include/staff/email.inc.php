<?php
if(!defined('OSTADMININC') || basename($_SERVER['SCRIPT_NAME'])==basename(__FILE__)) die('Habari/Jambo rafiki? '); //Say hi to our friend..
if(!$thisuser || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

$info=($_POST && $errors)?$_POST:array(); //Re-use the post info on error...savekeyboards.org
if($email && $_REQUEST['a']!='new'){
    $title='Edit Email'; 
    $action='update';
    if(!$info) {
        $info=$email->getInfo();
        $info['userpass']=$info['userpass']?Misc::decrypt($info['userpass'],SECRET_SALT):'';
    }
    $qstr='?t=email&id='.$email->getId();
}else {
   $title=translate("ADD_NEW_EMAIL");
   $action='create';
   $info['smtp_auth']=isset($info['smtp_auth'])?$info['smtp_auth']:1;
}

$info=Format::htmlchars($info);
//get the goodies.
$depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE);
$priorities= db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
?>
<div class="msg"><?=$title?></div>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
<form action="admin.php<?=$qstr?>" method="post">
 <input type="hidden" name="do" value="<?=$action?>">
 <input type="hidden" name="a" value="<?=Format::htmlchars($_REQUEST['a'])?>">
 <input type="hidden" name="t" value="email">
 <input type="hidden" name="email_id" value="<?=$info['email_id']?>">
 <tr><td>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate("EMAIL_INFO");?></td></tr>
        <tr class="subheader">
            <td colspan=2 ><?php print translate("NEW_EMAIL_NOTE");?></td>
        </tr>
        <tr><th><?php print translate("LABEL_EMAIL_ADDRESS");?>:</th>
            <td>
                <input type="text" name="email" size=30 value="<?=$info['email']?>">&nbsp;<font class="error">*&nbsp;<?=$errors['email']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("EMAIL_NAME");?>:</th>
            <td>
                <input type="text" name="name" size=30 value="<?=$info['name']?>">&nbsp;<font class="error">&nbsp;<?=$errors['name']?></font>
                &nbsp;&nbsp;(<i><?php print translate("EMAIL_NAME_NOTE");?></i>)
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_NEW_TICKET_PRIORITY')?></th>
            <td>
                <select name="priority_id">
                    <option value=0><?php print translate('LABEL_SELECT_PRIORITY')?></option>
                    <?
                    while (list($id,$name) = db_fetch_row($priorities)){
                        $selected = ($info['priority_id']==$id)?'selected':''; ?>
                        <option value="<?=$id?>"<?=$selected?>><?php eval('?>' .$name. '<?php ');?></option>
                    <?
                    }?>
                </select>&nbsp;<font class="error">*&nbsp;<?=$errors['priority_id']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("NEW_TICKET_DEPARTMENT");?></th>
            <td>
                <select name="dept_id">
                    <option value=0><?php print translate("SELECT_ONE");?></option>
                    <?
                    while (list($id,$name) = db_fetch_row($depts)){
                        $selected = ($info['dept_id']==$id)?'selected':''; ?>
                        <option value="<?=$id?>"<?=$selected?>><?=$name?> Dept</option>
                    <?
                    }?>
                </select>&nbsp;<font class="error">&nbsp;<?=$errors['dept_id']?></font>&nbsp;
            </td>
        </tr>
        <tr><th><?php print translate("AUTO_RESPONSE");?></th>
            <td>
                <input type="checkbox" name="noautoresp" value=1 <?=$info['noautoresp']? 'checked': ''?> ><?php print translate("AUTO_RESPONSE_NOTE");?>
            </td>
        </tr>
        <tr class="subheader">
            <td colspan=2 ><?php print translate("NEW_EMAIL_LOGIN_INFO");?></td>
        </tr>
        <tr><th><?=translate('LABEL_USERNAME') ?></th>
            <td><input type="text" name="userid" size=35 value="<?=$info['userid']?>" autocomplete='off' >
                &nbsp;<font class="error">&nbsp;<?=$errors['userid']?></font>
            </td>
        </tr>
        <tr><th><?=translate('LABEL_PASSWORD') ?></th>
            <td>
               <input type="password" name="userpass" size=35 value="<?=$info['userpass']?>" autocomplete='off'>
                &nbsp;<font class="error">&nbsp;<?=$errors['userpass']?></font>
            </td>
        </tr>
        <tr class="header"><td colspan=2><?php print translate("NEW_EMAIL_MAIL_ACCOUNT");?></td></tr>
        <tr class="subheader"><td colspan=2>
	<?php print translate("NEW_EMAIL_MAIL_ACCOUNT_NOTES");?>
            <font class="error">&nbsp;<?=$errors['mail']?></font></td></tr>
        <tr><th><?php print translate("LABEL_STATUS");?></th>
            <td>
                <label><input type="radio" name="mail_active"  value="1"   <?=$info['mail_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?></label>
                <label><input type="radio" name="mail_active"  value="0"   <?=!$info['mail_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?></label>
                &nbsp;<font class="error">&nbsp;<?=$errors['mail_active']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("HOST");?></th>
            <td><input type="text" name="mail_host" size=35 value="<?=$info['mail_host']?>">
                &nbsp;<font class="error">&nbsp;<?=$errors['mail_host']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("PORT");?></th>
            <td><input type="text" name="mail_port" size=6 value="<?=$info['mail_port']?$info['mail_port']:''?>">
                &nbsp;<font class="error">&nbsp;<?=$errors['mail_port']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("PROTOCOL");?></th>
            <td>
                <select name="mail_protocol">
                    <option value='POP'><?php print translate("SELECT_ONE");?></option>
                    <option value='POP' <?=($info['mail_protocol']=='POP')?'selected="selected"':''?> >POP</option>
                    <option value='IMAP' <?=($info['mail_protocol']=='IMAP')?'selected="selected"':''?> >IMAP</option>
                </select>
                <font class="error">&nbsp;<?=$errors['mail_protocol']?></font>
            </td>
        </tr>

        <tr><th><?php print translate("ENCRYPTION");?></th>
            <td>
                 <label><input type="radio" name="mail_encryption"  value="NONE"
                    <?=($info['mail_encryption']!='SSL')?'checked':''?> /><?php print translate("LABEL_NONE");?></label>
                 <label><input type="radio" name="mail_encryption"  value="SSL"
                    <?=($info['mail_encryption']=='SSL')?'checked':''?> />SSL</label>
                <font class="error">&nbsp;<?=$errors['mail_encryption']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("FETCH_FREQUENCY");?></th>
            <td>
                <input type="text" name="mail_fetchfreq" size=4 value="<?=$info['mail_fetchfreq']?$info['mail_fetchfreq']:''?>"> <?php print translate("DELAY_INTERVAL_IN_MINUTES");?>
                &nbsp;<font class="error">&nbsp;<?=$errors['mail_fetchfreq']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("MAXIMUM_EMAILS_PER_FETCH");?></th>
            <td>
                <input type="text" name="mail_fetchmax" size=4 value="<?=$info['mail_fetchmax']?$info['mail_fetchmax']:''?>">
                &nbsp;<font class="error">&nbsp;<?=$errors['mail_fetchmax']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("DELETE_MESSAGES");?></th>
            <td>
                <input type="checkbox" name="mail_delete" value=1 <?=$info['mail_delete']? 'checked': ''?> >
		<?php print translate("DELETE_MESSAGES_NOTE");?>
                &nbsp;<font class="error">&nbsp;<?=$errors['mail_delete']?></font>
            </td>
        </tr>
        <tr class="header"><td colspan=2><?php print translate("SMTP_SETTINGS_OPTIONAL");?></td></tr>
        <tr class="subheader"><td colspan=2>
	<?php print translate("SMTP_SETTINGS_NOTE");?>
                <font class="error">&nbsp;<?=$errors['smtp']?></font></td></tr>
        <tr><th><?php print translate("LABEL_STATUS");?></th>
            <td>
                <label><input type="radio" name="smtp_active"  value="1"   <?=$info['smtp_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?></label>
                <label><input type="radio" name="smtp_active"  value="0"   <?=!$info['smtp_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?></label>
                &nbsp;<font class="error">&nbsp;<?=$errors['smtp_active']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("HOST");?></th>
            <td><input type="text" name="smtp_host" size=35 value="<?=$info['smtp_host']?>">
                &nbsp;<font class="error">&nbsp;<?=$errors['smtp_host']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("PORT");?></th>
            <td><input type="text" name="smtp_port" size=6 value="<?=$info['smtp_port']?$info['smtp_port']:''?>">
                &nbsp;<font class="error">&nbsp;<?=$errors['smtp_port']?></font>
            </td>
        </tr>
        <tr><th><?=translate('TEXT_AUTHENTICATION_REQUIRED_QUESTION')?></th>
            <td>

                 <label><input type="radio" name="smtp_auth"  value="1"
                    <?=$info['smtp_auth']?'checked':''?> /><?php print translate("YES");?></label>
                 <label><input type="radio" name="smtp_auth"  value="0"
                    <?=!$info['smtp_auth']?'checked':''?> /><?php print translate("NO");?></label>
                <font class="error">&nbsp;<?=$errors['smtp_auth']?></font>
            </td>
        </tr>
        <tr><th><?php print translate("ENCRYPTION");?></th>
            <td><?php print translate("ENCRYPTION_SMTP_NOTE");?></td>
        </tr>
    </table>
   </td></tr>
   <tr><td style="padding:10px 0 10px 220px;">
            <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SUBMIT");?>'>
            <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
            <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="admin.php?t=email"'>
        </td>
     </tr>
</form>
</table>
