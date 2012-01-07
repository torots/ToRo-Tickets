<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));

//List all groups.   
$sql='SELECT grp.group_id,group_name,group_enabled,count(staff.staff_id) as users, grp.created,grp.updated'
     .' FROM '.GROUP_TABLE.' grp LEFT JOIN '.STAFF_TABLE.' staff USING(group_id)';
$groups=db_query($sql.' GROUP BY grp.group_id ORDER BY group_name');    
$showing=($num=db_num_rows($groups))?translate("USER_GROUPS"):translate("NO_GROUPS");
?>
<div class="msg"><?=$showing?></div>
<table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="admin.php?t=groups" method="POST" name="groups" onSubmit="return checkbox_checker(document.forms['groups'],1,0);">
    <input type=hidden name='a' value='update_groups'>
    <tr><td>
    <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
        <tr>
	        <th width="7px">&nbsp;</th>
	        <th width=200><?php print translate("GROUP");?></th>
                <th width=100><?php print translate("LABEL_STATUS");?></th>
	        <th width=10><?php print translate("LABEL_STAFF_MEMBERS");?></th>
	        <th><?php print translate("TEXT_CREATED");?></th>
	        <th><?php print translate("LABEL_LAST_UPDATED");?></th>
        </tr>
        <?
        $class = 'row1';
        $total=0;
        $grps=($errors && is_array($_POST['grps']))?$_POST['grps']:null;
        if($groups && db_num_rows($groups)):
            while ($row = db_fetch_array($groups)) {
                $sel=false;
                if(($grps && in_array($row['group_id'],$grps)) || ($gID && $gID==$row['group_id']) ){
                    $class="$class highlight";
                    $sel=true;
                }
                ?>
            <tr class="<?=$class?>" id="<?=$row['group_id']?>">
                <td width=7px>
                  <input type="checkbox" name="grps[]" value="<?=$row['group_id']?>" <?=$sel?'checked':''?>  onClick="highLight(this.value,this.checked);">
                <td><a href="admin.php?t=grp&id=<?=$row['group_id']?>"><?=Format::htmlchars($row['group_name'])?></a></td>
                <td><b><?=$row['group_enabled']?'Active':'Disabled'?></b></td>
                <td>&nbsp;&nbsp;<a href="admin.php?t=staff&gid=<?=$row['group_id']?>"><?=$row['users']?></a></td>
                <td><?=Format::db_date($row['created'])?></td>
                <td><?=Format::db_datetime($row['updated'])?></td>
            </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //not tickets found!! ?> 
            <tr class="<?=$class?>"><td colspan=6><b>Query returned 0 results</b></td></tr>
        <?
        endif; ?>
    </table>
    <?
    if(db_num_rows($groups)>0): //Show options..
     ?>
    <tr>
        <td style="padding-left:20px;">
            <?php print translate("LABEL_SELECT");?>:&nbsp;
            <a href="#" onclick="return select_all(document.forms['groups'],true)"><?php print translate("LABEL_ALL");?></a>&nbsp;&nbsp;
            <a href="#" onclick="return toogle_all(document.forms['groups'],true)"><?php print translate("LABEL_TOGGLE");?></a>&nbsp;&nbsp;
            <a href="#" onclick="return reset_all(document.forms['groups'])"><?php print translate("LABEL_CANCEL");?>E</a>&nbsp;&nbsp;
        </td>
    </tr>
    <tr>
        <td align="center">
            <input class="button" type="submit" name="activate_grps" value='<?php print translate("TEXT_ENABLE");?>' 
                onClick=' return confirm("Are you sure you want to ENABLE selected group(s)");'>
            <input class="button" type="submit" name="disable_grps" value='<?php print translate("TEXT_DISABLE");?>' 
                onClick=' return confirm("Are you sure you want to DISABLE selected group(s)");'>
            <input class="button" type="submit" name="delete_grps" value='<?php print translate("LABEL_DELETE");?>' 
                onClick=' return confirm("Are you sure you want to DELETE selected group(s)");'>
        </td>
    </tr>
    <?
    endif;
    ?>
    </form>
</table>
