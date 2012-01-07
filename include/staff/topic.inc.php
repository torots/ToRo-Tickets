<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

$info=($_POST && $errors)?Format::input($_POST):array(); //Re-use the post info on error...savekeyboards.org
if($topic && $_REQUEST['a']!='new'){
    $title='Edit Topic';
    $action='update';
    $info=$info?$info:$topic->getInfo();
}else {
   $title='New Help Topic';
   $action='create';
   $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
}
//get the goodies.
$depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE);
$priorities= db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
?>
<form action="admin.php?t=topics" method="post">
 <input type="hidden" name="do" value="<?=$action?>">
 <input type="hidden" name="a" value="<?=Format::htmlchars($_REQUEST['a'])?>">
 <input type='hidden' name='t' value='topics'>
 <input type="hidden" name="topic_id" value="<?=$info['topic_id']?>">
<table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
    <tr class="header"><td colspan=2><?=$title?></td></tr>
    <tr class="subheader">
        <td colspan=2 >Disabling auto response will overwrite dept settings.</td>
    </tr>
    <tr>
        <th width="20%">Help Topic:</th>
        <td><input type="text" name="topic" size=45 value="<?=$info['topic']?>">
            &nbsp;<font class="error">*&nbsp;<?=$errors['topic']?></font></td>
    </tr>
    <tr><th>Topic Status</th>
        <td>
            <input type="radio" name="isactive"  value="1"   <?=$info['isactive']?'checked':''?> />Active
            <input type="radio" name="isactive"  value="0"   <?=!$info['isactive']?'checked':''?> />Disabled
        </td>
    </tr>
    <tr>
        <th nowrap>Auto Response:</th>
        <td>
            <input type="checkbox" name="noautoresp" value=1 <?=$info['noautoresp']? 'checked': ''?> >
                <b>Disable</b> autoresponse for this topic.   (<i>Overwrite Dept setting</i>)
        </td>
    </tr>
    <tr>
        <th>New Ticket Priority:</th>
        <td>
            <select name="priority_id">
                <option value=0>Select Priority</option>
                <?
                while (list($id,$name) = db_fetch_row($priorities)){
                    $selected = ($info['priority_id']==$id)?'selected':''; ?>
                    <option value="<?=$id?>"<?=$selected?>><?=$name?></option>
                <?
                }?>
            </select>&nbsp;<font class="error">*&nbsp;<?=$errors['priority_id']?></font>
        </td>
    </tr>
    <tr>
        <th nowrap>New Ticket Department:</th>
        <td>
            <select name="dept_id">
                <option value=0>Select Department</option>
                <?
                while (list($id,$name) = db_fetch_row($depts)){
                    $selected = ($info['dept_id']==$id)?'selected':''; ?>
                    <option value="<?=$id?>"<?=$selected?>><?=$name?> Dept</option>
                <?
                }?>
            </select>&nbsp;<font class="error">*&nbsp;<?=$errors['dept_id']?></font>
        </td>
    </tr>
</table>
<div style="padding-left:220px;">
    <input class="button" type="submit" name="submit" value="Submit">
    <input class="button" type="reset" name="reset" value="Reset">
    <input class="button" type="button" name="cancel" value="Cancel" onClick='window.location.href="admin.php?t=topics"'>
</div>
</form>
