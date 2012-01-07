<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

$rep=null;
$newuser=true;
if($staff && $_REQUEST['a']!='new'){
    $rep=$staff->getInfo();
    $title='Update: '.$rep['firstname'].' '.$rep['lastname'];
    $action='update';
    $pwdinfo='To reset the password enter a new one below';
    $newuser=false;
}else {
    $title=translate("NEW_STAFF_MEMBER");
    $pwdinfo=translate("PASSWORD_REQUIRED");
    $action='create';
    $rep['resetpasswd']=isset($rep['resetpasswd'])?$rep['resetpasswd']:1;
    $rep['isactive']=isset($rep['isactive'])?$rep['isactive']:1;
    $rep['dept_id']=$rep['dept_id']?$rep['dept_id']:$_GET['dept'];
    $rep['isvisible']=isset($rep['isvisible'])?$rep['isvisible']:1;
}
$rep=($errors && $_POST)?Format::input($_POST):Format::htmlchars($rep);

//get the goodies.
$groups=db_query('SELECT group_id,group_name FROM '.GROUP_TABLE);
$depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE);

?>
<div class="msg"><?=$title?></div>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
<form action="admin.php" method="post">
 <input type="hidden" name="do" value="<?=$action?>">
 <input type="hidden" name="a" value="<?=Format::htmlchars($_REQUEST['a'])?>">
 <input type="hidden" name="t" value="staff">
 <input type="hidden" name="staff_id" value="<?=$rep['staff_id']?>">
 <tr><td>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate("USER_ACCOUNT");?></td></tr>
        <tr class="subheader"><td colspan=2><?php print translate("ACCOUNT_INFORMATION");?></td></tr>
        <tr>
            <th><?php print translate('LABEL_USERNAME') ?>:</th>
            <td><input type="text" name="username" value="<?=$rep['username']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['username']?></font></td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_DEPARTMENT");?>:</th>
            <td>
                <select name="dept_id">
                    <option value=0><?php print translate("SELECT_ONE");?></option>
                    <?
                    while (list($id,$name) = db_fetch_row($depts)){
                        $selected = ($rep['dept_id']==$id)?'selected':''; ?>
                        <option value="<?=$id?>"<?=$selected?>><?=$name?> Dept</option>
                    <?
                    }?>
                </select>&nbsp;<font class="error">*&nbsp;<?=$errors['dept']?></font>
            </td>
        </tr>
        <tr>
            <th><?php print translate("GROUP");?>:</th>
            <td>
                <select name="group_id">
                    <option value=0><?php print translate("SELECT_ONE");?></option>
                    <?
                    while (list($id,$name) = db_fetch_row($groups)){
                        $selected = ($rep['group_id']==$id)?'selected':''; ?>
                        <option value="<?=$id?>"<?=$selected?>><?=$name?></option>
                    <?
                    }?>
                </select>&nbsp;<font class="error">*&nbsp;<?=$errors['group']?></font>
            </td>
        </tr>
        <tr>
            <th><?php print translate("NAME_FIRST_LAST");?>:</th>
            <td>
                <input type="text" name="firstname" value="<?=$rep['firstname']?>">&nbsp;<font class="error">*</font>
                &nbsp;&nbsp;&nbsp;<input type="text" name="lastname" value="<?=$rep['lastname']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['name']?></font></td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_EMAIL_ADDRESS");?>:</th>
            <td><input type="text" name="email" size=25 value="<?=$rep['email']?>">
                &nbsp;<font class="error">*&nbsp;<?=$errors['email']?></font></td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_OFFICE_PHONE");?>:</th>
            <td>
                <input type="text" name="phone" value="<?=$rep['phone']?>" >&nbsp;Ext&nbsp;
                <input type="text" name="phone_ext" size=6 value="<?=$rep['phone_ext']?>" >
                    &nbsp;<font class="error">&nbsp;<?=$errors['phone']?></font></td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_CELL_PHONE");?>:</th>
            <td>
                <input type="text" name="mobile" value="<?=$rep['mobile']?>" >
                    &nbsp;<font class="error">&nbsp;<?=$errors['mobile']?></font></td>
        </tr>
        <tr>
            <th valign="top"><?php print translate("SIGNATURE");?>:</th>
            <td><textarea name="signature" cols="21" rows="5" style="width: 60%;"><?=$rep['signature']?></textarea></td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_PASSWORD");?>:</th>
            <td>
                <i><?=$pwdinfo?></i>&nbsp;&nbsp;&nbsp;<font class="error">&nbsp;<?=$errors['npassword']?></font> <br/>
                <input type="password" name="npassword" AUTOCOMPLETE=OFF >&nbsp;
            </td>
        </tr>
        <tr>
            <th><?php print translate("LABEL_PASSWORD_AGAIN");?>:</th>
            <td class="mainTableAlt"><input type="password" name="vpassword" AUTOCOMPLETE=OFF >
                &nbsp;<font class="error">&nbsp;<?=$errors['vpassword']?></font></td>
        </tr>
        <tr>
            <th><?php print translate("FORCE_PASSWORD_CHANGE");?>:</th>
            <td>
                <input type="checkbox" name="resetpasswd" <?=$rep['resetpasswd'] ? 'checked': ''?>><?php print translate("FORCE_PASSWORD_CHANGE_NOTE");?></td>
        </tr>
        <tr class="header"><td colspan=2><?php print translate("ACCOUNT_PERMISSIONS_STATUS_SETTINGS");?></td></tr>
        <tr class="subheader"><td colspan=2>
            <?php print translate("ACCOUNT_PERMISSIONS_STATUS_SETTINGS_NOTE");?></td>
        </tr> 
        <tr><th><b><?php print translate("LABEL_STATUS");?></b></th>
            <td>
                        <input type="radio" name="isactive"  value="1" <?=$rep['isactive']?'checked':''?> /><b><?php print translate("TEXT_ACTIVE");?></b>
                        <input type="radio" name="isactive"  value="0" <?=!$rep['isactive']?'checked':''?> /><b><?php print translate("LOCKED");?></b>
                        &nbsp;&nbsp;
            </td>
        </tr>
        <tr><th><b><?php print translate("LABEL_TYPE");?></b></th>
            <td class="mainTableAlt">
                        <input type="radio" name="isadmin"  value="1" <?=$rep['isadmin']?'checked':''?> /><font color="red"><b><?php print translate("ADMIN");?></b></font>
                        <input type="radio" name="isadmin"  value="0" <?=!$rep['isadmin']?'checked':''?> /><b><?php print translate("LABEL_STAFF");?></b>
                        &nbsp;&nbsp;
            </td>
        </tr>
        <tr><th><?php print translate("DIRECTORY_LISTING");?>:</th>
            <td>
               <input type="checkbox" name="isvisible" <?=$rep['isvisible'] ? 'checked': ''?>><?php print translate("DIRECTORY_LISTING_NOTE");?>
            </td>
        </tr>
        <tr><th><?php print translate("VACATION_MODE");?>:</th>
            <td class="mainTableAlt">
             <input type="checkbox" name="onvacation" <?=$rep['onvacation'] ? 'checked': ''?>>
	     <?php print translate("VACATION_MODE_NOTE");?>
                &nbsp;<font class="error">&nbsp;<?=$errors['vacation']?></font>
            </td>
        </tr>
    </table>
   </td></tr>
   <tr><td style="padding:5px 0 10px 210px;">
        <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SUBMIT");?>'>
        <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
        <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="admin.php?t=staff"'>
    </td></tr>
  </form>
</table>
