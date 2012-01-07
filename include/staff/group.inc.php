<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));

$info=($errors && $_POST)?Format::input($_POST):Format::htmlchars($group);
if($group && $_REQUEST['a']!='new'){
    $title=translate("EDIT_GROUP"). ': '.$group['group_name'];
    $action='update';
}else {
    $title=translate("ADD_NEW_GROUP");
    $action='create';
    $info['group_enabled']=isset($info['group_enabled'])?$info['group_enabled']:1; //Default to active 
}

?>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <form action="admin.php" method="POST" name="group">
 <input type="hidden" name="do" value="<?=$action?>">
 <input type="hidden" name="a" value="<?=Format::htmlchars($_REQUEST['a'])?>">
 <input type="hidden" name="t" value="groups">
 <input type="hidden" name="group_id" value="<?=$info['group_id']?>">
 <input type="hidden" name="old_name" value="<?=$info['group_name']?>">
 <tr><td>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?=Format::htmlchars($title)?></td></tr>
        <tr class="subheader"><td colspan=2>
            <?php print translate("GROUP_NOTE");?>
            </td></tr>
        <tr><th><?php print translate("LABEL_NAME");?>:</th>
            <td><input type="text" name="group_name" size=25 value="<?=$info['group_name']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['group_name']?></font>
                    
            </td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_STATUS");?>:</th>
            <td>
                <input type="radio" name="group_enabled"  value="1"   <?=$info['group_enabled']?'checked':''?> /> <?php print translate("TEXT_ACTIVE");?>
                <input type="radio" name="group_enabled"  value="0"   <?=!$info['group_enabled']?'checked':''?> /> <?php print translate("TEXT_DISABLED");?>
                &nbsp;<font class="error">&nbsp;<?=$errors['group_enabled']?></font>
            </td>
        </tr>
        <tr><th valign="top"><br><?php print translate('LABEL_DEPT_ACCESS')?></th>
            <td class="mainTableAlt"><i><?php print translate('TEXT_SELECT_DEPARTMENTS_GROUP_MEMBERS')?></i>
                &nbsp;<font class="error">&nbsp;<?=$errors['depts']?></font><br/>
                <?
                //Try to save the state on error...
                $access=($_POST['depts'] && $errors)?$_POST['depts']:explode(',',$info['dept_access']);
                $depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name');
                while (list($id,$name) = db_fetch_row($depts)){
                    $ck=($access && in_array($id,$access))?'checked':''; ?>
                    <input type="checkbox" name="depts[]" value="<?=$id?>" <?=$ck?> > <?=$name?><br/>
                <?
                }?>
		<?php print translate("LABEL_SELECT");?>:&nbsp;
                <a href="#" onclick="return select_all(document.forms['group'])"><?php print translate("LABEL_ALL");?></a>&nbsp;&nbsp;
                <a href="#" onclick="return reset_all(document.forms['group'])"><?php print translate("LABEL_NONE");?></a>&nbsp;&nbsp; 
            </td>
        </tr>
        <tr><th><?php print translate("CAN_CREATE_TICKETS");?></th>
            <td>
                <input type="radio" name="can_create_tickets"  value="1"   <?=$info['can_create_tickets']?'checked':''?> /><?php print translate("YES");?> 
                <input type="radio" name="can_create_tickets"  value="0"   <?=!$info['can_create_tickets']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_CREATE_TICKETS_NOTE");?></i>
            </td>
        </tr>
        <tr><th><?php print translate("CAN_EDIT_TICKETS");?></th>
            <td>
                <input type="radio" name="can_edit_tickets"  value="1"   <?=$info['can_edit_tickets']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_edit_tickets"  value="0"   <?=!$info['can_edit_tickets']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_EDIT_TICKETS_NOTE");?></i>
            </td>
        </tr>
        <tr><th><?php print translate("CAN_CLOSE_TICKETS");?></th>
            <td>
                <input type="radio" name="can_close_tickets"  value="1" <?=$info['can_close_tickets']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_close_tickets"  value="0" <?=!$info['can_close_tickets']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_CLOSE_TICKETS_NOTE");?></i>
            </td>
        </tr>
        <tr><th><?php print translate("CAN_TRANSFER_TICKETS");?></th>
            <td>
                <input type="radio" name="can_transfer_tickets"  value="1" <?=$info['can_transfer_tickets']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_transfer_tickets"  value="0" <?=!$info['can_transfer_tickets']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_TRANSFER_TICKETS_NOTE");?></i>
            </td>
        </tr>
        <tr><th><?php print translate("CAN_DELETE_TICKETS");?></th>
            <td>
                <input type="radio" name="can_delete_tickets"  value="1"   <?=$info['can_delete_tickets']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_delete_tickets"  value="0"   <?=!$info['can_delete_tickets']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_DELETE_TICKETS_NOTE");?></i>
            </td>
        </tr>
        <tr><th><?php print translate("CAN_BAN_EMAILS");?></th>
            <td>
                <input type="radio" name="can_ban_emails"  value="1" <?=$info['can_ban_emails']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_ban_emails"  value="0" <?=!$info['can_ban_emails']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_BAN_EMAILS_NOTE");?></i>
            </td>
        </tr>
	<tr><th><?php print translate("CAN_MASS_BAN_AND_DELETE_EMAILS");?></th>
	    <td>
	        <input type="radio" name="can_mass_ban_emails"  value="1" <?=$info['can_mass_ban_emails']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_mass_ban_emails"  value="0" <?=!$info['can_mass_ban_emails']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_MASS_BAN_AND_DELETE_EMAILS_NOTE");?></i>
            </td>
        </tr>
        <tr><th><?php print translate("CAN_MANAGE_PREMADE");?></th>
            <td>
                <input type="radio" name="can_manage_kb"  value="1" <?=$info['can_manage_kb']?'checked':''?> /><?php print translate("YES");?>
                <input type="radio" name="can_manage_kb"  value="0" <?=!$info['can_manage_kb']?'checked':''?> /><?php print translate("NO");?>
                &nbsp;&nbsp;<i><?php print translate("CAN_MANAGE_PREMADE_NOTE");?></i>
            </td>
        </tr>
    </table>
    <tr><td style="padding-left:165px;padding-top:20px;">
        <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SUBMIT");?>'>
        <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
        <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="admin.php?t=groups"'>
        </td>
    </tr>
 </form>
</table>
