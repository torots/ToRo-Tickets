<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));
$info=null;
if($dept && $_REQUEST['a']!='new'){
    //Editing Department.
    $title=translate('LABEL_UPDATE_DEPARTMENT');
    $action='update';
    $info=$dept->getInfo();
}else {
    $title=translate('LABEL_NEW_DEPARTMENT');
    $action='create';
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
}
$info=($errors && $_POST)?Format::input($_POST):Format::htmlchars($info);

?>
<div class="msg"><?=$title?></div>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <form action="admin.php?t=dept&id=<?=$info['dept_id']?>" method="POST" name="dept">
 <input type="hidden" name="do" value="<?=$action?>">
 <input type="hidden" name="a" value="<?=Format::htmlchars($_REQUEST['a'])?>">
 <input type="hidden" name="t" value="dept">
 <input type="hidden" name="dept_id" value="<?=$info['dept_id']?>">
 <tr><td>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate('LABEL_DEPARTMENT')?></td></tr>
        <tr class="subheader"><td colspan=2 ><?php print translate('TEXT_DEPT_DEPENDS_ON_EMAIL')?></td></tr>
        <tr><th><?php print translate('LABEL_DEPT_NAME')?>:</th>
            <td><input type="text" name="dept_name" size=25 value="<?=$info['dept_name']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['dept_name']?></font>
                    
            </td>
        </tr>
        <tr>
            <th><?php print translate('LABEL_DEPT_EMAIL')?>:</th>
            <td>
                <select name="email_id">
                    <option value=""><?= translate('LABEL_SELECT_ONE_EMAIL') ?></option>
                    <?
                    $emails=db_query('SELECT email_id,email,name,smtp_active FROM '.EMAIL_TABLE);
                    while (list($id,$email,$name,$smtp) = db_fetch_row($emails)){
                        $email=$name?"$name &lt;$email&gt;":$email;
                        if($smtp)
                            $email.=' (SMTP)';
                        ?>
                     <option value="<?=$id?>"<?=($info['email_id']==$id)?'selected':''?>><?=$email?></option>
                    <?
                    }?>
                 </select>
                 &nbsp;<font class="error">*&nbsp;<?=$errors['email_id']?></font>&nbsp;(<?php print translate('LABEL_OUTGOING_EMAIL')?>)
            </td>
        </tr>    
        <? if($info['dept_id']) { //update 
            $users= db_query('SELECT staff_id,CONCAT_WS(" ",firstname,lastname) as name FROM '.STAFF_TABLE.' WHERE dept_id='.db_input($info['dept_id']));
            ?>
        <tr>
            <th><?php print translate('LABEL_DEPT_MANAGER')?>:</th>
            <td>
                <?if($users && db_num_rows($users)) {?>
                <select name="manager_id">
                    <option value=0 >-------<?php print translate('LABEL_DEPT_MANAGER_NONE')?>-------</option>
                    <option value=0 disabled ><?php print translate('LABEL_SELECT_MANAGER_OPTIONAL')?></option>
                     <?
                     while (list($id,$name) = db_fetch_row($users)){ ?>
                        <option value="<?=$id?>"<?=($info['manager_id']==$id)?'selected':''?>><?=$name?></option>
                     <?}?>
                     
                </select>
                 <?}else {?>
                       <?php print translate('LABEL_NO_MANAGER_ADD_USERS')?>
                       <input type="hidden" name="manager_id"  value="0" />
                 <?}?>
                    &nbsp;<font class="error">&nbsp;<?=$errors['manager_id']?></font>
            </td>
        </tr>
        <?}?>
        <tr><th><?php print translate('LABEL_DEPT_TYPE')?></th>
            <td>
                <input type="radio" name="ispublic"  value="1"   <?=$info['ispublic']?'checked':''?> /><?php print translate('LABEL_PUBLIC')?>
                <input type="radio" name="ispublic"  value="0"   <?=!$info['ispublic']?'checked':''?> /><?php print translate('LABEL_PRIVATE_HIDDEN')?>
                &nbsp;<font class="error"><?=$errors['ispublic']?></font>
            </td>
        </tr>
        <tr>
            <th valign="top"><br/><?php print translate('LABEL_DEPT_SIGNATURE')?>:</th>
            <td>
                <i><?php print translate('TEXT_REQUIRED_WHEN_DEPT_IS_PUBLIC')?></i>&nbsp;&nbsp;&nbsp;<font class="error"><?=$errors['dept_signature']?></font><br/>
                <textarea name="dept_signature" cols="21" rows="5" style="width: 60%;"><?=$info['dept_signature']?></textarea>
                <br>
                <input type="checkbox" name="can_append_signature" <?=$info['can_append_signature'] ?'checked':''?> >
                <?php print translate('TEXT_CAM_BE_APPENDED_TO_RESPONSES')?>  
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_EMAIL_TEMPLATES')?>:</th>
            <td>
                <select name="tpl_id">
                    <option value=0 disabled><?php print translate('LABEL_SELECT_TEMPLATE')?></option>
                    <option value="0" selected="selected"><?php print translate('LABEL_SYSTEM_DEFAULT_TEMPLATE')?></option>
                    <?
                    $templates=db_query('SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_TABLE.' WHERE tpl_id!='.db_input($cfg->getDefaultTemplateId()));
                    while (list($id,$name) = db_fetch_row($templates)){
                        $selected = ($info['tpl_id']==$id)?'SELECTED':''; ?>
                        <option value="<?=$id?>"<?=$selected?>><?=Format::htmlchars($name)?></option>
                    <?
                    }?>
                </select><font class="error">&nbsp;<?=$errors['tpl_id']?></font><br/>
                <i><?php print translate('TEXT_USED_FOR_OUTGOING_EMAILS')?></i>
            </td>
        </tr>
        <tr class="header"><td colspan=2><?php print translate('LABEL_AUTORESPONDERS')?></td></tr>
        <tr class="subheader"><td colspan=2>
	        <?php print translate('TEXT_GLOBAL_AUTO_RESPONSE_SETTINGS')?> 
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_NEW_TICKET')?>:</th>
            <td>
                <input type="radio" name="ticket_auto_response"  value="1"   <?=$info['ticket_auto_response']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="ticket_auto_response"  value="0"   <?=!$info['ticket_auto_response']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_NEW_MESSAGE')?>:</th>
            <td>
                <input type="radio" name="message_auto_response"  value="1"   <?=$info['message_auto_response']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="message_auto_response"  value="0"   <?=!$info['message_auto_response']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
            </td>
        </tr>
        <tr>
            <th><?php print translate('LABEL_AUTO_RESPONSE_EMAIL')?>:</th>
            <td>
                <select name="autoresp_email_id">
                    <option value="0" disabled><?= print translate('TEXT_SELECT_ONE_EMAIL') ?></option>
                    <option value="0" selected="selected"><?php print translate('LABEL_DEPT_EMAIL_ABOVE')?></option>
                    <?
                    $emails=db_query('SELECT email_id,email,name,smtp_active FROM '.EMAIL_TABLE.' WHERE email_id!='.db_input($info['email_id']));
                    if($emails && db_num_rows($emails)) {
                        while (list($id,$email,$name,$smtp) = db_fetch_row($emails)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            if($smtp)
                                $email.=' (SMTP)';
                            ?>
                            <option value="<?=$id?>"<?=($info['autoresp_email_id']==$id)?'selected':''?>><?=$email?></option>
                        <?
                        }
                    }?>
                 </select>
                 &nbsp;<font class="error">&nbsp;<?=$errors['autoresp_email_id']?></font>&nbsp;<br/>
                 <i><?php print translate('TEXT_EMAIL_ADDRESS_USED_SEND_AUTO_RESPONSES')?></i>
            </td>
        </tr>
    </table>
    </td></tr>
    <tr><td style="padding:10px 0 10px 200px;">
        <input class="button" type="submit" name="submit" value="<?php print translate('LABEL_SUBMIT')?>">
        <input class="button" type="reset" name="reset" value="<?php print translate('LABEL_RESET')?>">
        <input class="button" type="button" name="cancel" value="<?php print translate('LABEL_CANCEL')?>" onClick='window.location.href="admin.php?t=dept"'>
    </td></tr>
    </form>
</table>
