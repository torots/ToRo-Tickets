<?php
if(!defined('OSTSCPINC') or !$thisuser->canManageKb()) die(print translate("TEXT_ACCESS_DENIED"));
$info=($errors && $_POST)?Format::input($_POST):Format::htmlchars($answer);
if($answer && $_REQUEST['a']!='add'){
    $title=translate("EDIT_PREMADE");
    $action='update';
}else {
    $title=translate("ADD_NEW_PREMADE_REPLY");
    $action='add';
    $info['isenabled']=1;
}
?>
<div>

    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div class="msg"><?=$title?></div>
<table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="kb.php" method="POST" name="group">
    <input type="hidden" name="a" value="<?=$action?>">
    <input type="hidden" name="id" value="<?=$info['premade_id']?>">
    <tr><td width=80px><?php print translate("TITLE");?>:</td>
        <td><input type="text" size=45 name="title" value="<?=$info['title']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['title']?></font>
        </td>
    </tr>
    <tr>
        <td><?php print translate('LABEL_STATUS');?>:</td>
        <td>
            <input type="radio" name="isenabled"  value="1"   <?=$info['isenabled']?'checked':''?> /> <?php print translate("TEXT_ACTIVE");?>
            <input type="radio" name="isenabled"  value="0"   <?=!$info['isenabled']?'checked':''?> /> <?php print translate("OFFLINE");?>
            &nbsp;<font class="error">&nbsp;<?=$errors['isenabled']?></font>
        </td>
    </tr>
    <tr><td valign="top"><?php print translate('LABEL_CATEGORY');?>:</td>
        <td><?php print translate("PREMADE_DEPARTMENT_NOTE");?>&nbsp;<font class="error">&nbsp;<?=$errors['depts']?></font><br/>
            <select name=dept_id>
                <option value=0 selected><?php print translate('LABEL_ALL_DEPARTMENTS'); ?></option>
                <?
                $depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name');
                while (list($id,$name) = db_fetch_row($depts)){
                    $ck=($info['dept_id']==$id)?'selected':''; ?>
                    <option value="<?=$id?>" <?=$ck?>><?=$name?></option>
                <?
                }?>
            </select>
        </td>
    </tr>
    <tr><td valign="top"><?php print translate('ANSWER');?>:</td>
        <td><?php print translate("PREMADE_REPLY_NOTE");?>&nbsp;<font class="error">*&nbsp;<?=$errors['answer']?></font><br/>
            <textarea name="answer" id="answer" cols="90" rows="9" wrap="soft" style="width:80%"><?=$info['answer']?></textarea>
        </td>
    </tr>
    <tr>
        <td nowrap>&nbsp;</td>
        <td><br>
            <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SUBMIT");?>'>
            <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
            <input class="button" type="button" name="cancel" value='<?php print translate("LABEL_CANCEL");?>' onClick='window.location.href="kb.php"'>
        </td>
    </tr>
    </form>
</table>
