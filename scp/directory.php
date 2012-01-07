<?php
/*********************************************************************
    directory.php

    Company's directories.

    r h <p@torots.com>
    Copyright (c)  2006-2011 toroTS
    http://www.torots.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: directory.php,v 1.2 2011/10/20 20:41:35 root Exp $
**********************************************************************/

require('staff.inc.php');
$nav->setTabActive('directory');
$desc = translate("LABEL_STAFF_MEMBERS");
$nav->addSubMenu(array('desc'=>$desc,'href'=>'directory.php','iconclass'=>'staff'));

$WHERE=' WHERE isvisible=1 ';
$sql=' SELECT staff.staff_id,staff.dept_id, firstname,lastname,email,phone,phone_ext,mobile,dept_name,onvacation '.
     ' FROM '.STAFF_TABLE.' staff LEFT JOIN  '.DEPT_TABLE.' USING(dept_id)';
if($_POST && $_POST['a']=='search') {
    $searchTerm=$_POST['query']; 
    if($searchTerm){
        $query=db_real_escape($searchTerm,false); //escape the term ONLY...no quotes.
        if(is_numeric($searchTerm)){
            $WHERE.=" AND staff.phone LIKE '%$query%'";
        }elseif(strpos($searchTerm,'@') && Validator::is_email($searchTerm)){
            $WHERE.=" AND staff.email='$query'";
        }else{
            $WHERE.=" AND ( staff.email LIKE '%$query%'".
                         " OR staff.lastname LIKE '%$query%'".
                         " OR staff.firstname LIKE '%$query%'".
                        ' ) ';
        }
    }
    if($_POST['dept'] && is_numeric($_POST['dept'])) {
        $WHERE.=' AND staff.dept_id='.db_input($_POST['dept']);
    }
}

$users=db_query("$sql $WHERE ORDER BY lastname,firstname");
//Render the page.
require_once(STAFFINC_DIR.'header.inc.php');
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
<div align="left">
    <form action="directory.php" method="POST" >
    <input type='hidden' name='a' value='search'>
    <?php print translate("LABEL_SEARCH_FOR");?> :&nbsp;<input type="text" name="query" value="<?=Format::htmlchars($_REQUEST['query'])?>">
    <?php print translate("LABEL_DEPT");?>.
    <select name="dept">
            <option value=0><?php print translate("LABEL_ALL_DEPARTMENTS");?></option>
            <?
            $depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE);
            while (list($deptId,$deptName) = db_fetch_row($depts)){
                $selected = ($_POST['dept']==$deptId)?'selected':''; ?>
                <option value="<?=$deptId?>"<?=$selected?>><?=$deptName?></option>
           <?}?>
    </select>
    &nbsp;
    <input type="submit" name="search" class="button" value="<?php print translate("LABEL_GO");?>">
    </form>
</div>
<? if($users && db_num_rows($users)):?>
<div class="msg"><?php print translate("LABEL_STAFF_MEMBERS");?></div>
<table border="0" cellspacing=0 cellpadding=2 class="dtable" width="100%">
    <tr>
        <th><?php print translate("LABEL_NAME");?></th>
        <th><?php print translate("LABEL_DEPT");?></th>
        <th><?php print translate("LABEL_EMAIL");?></th>
        <th><?php print translate("LABEL_PHONE");?></th>
        <th><?php print translate("LABEL_MOBILE");?></th>
    </tr>
    <?
    $class = 'row1';
    while ($row = db_fetch_array($users)) {
        $name=ucfirst($row['firstname'].' '.$row['lastname']);
        $ext=$row['phone_ext']?'&nbsp;Ext&nbsp;'.$row['phone_ext']:'';
        ?>
        <tr class="<?=$class?>" id="<?=$row['staff_id']?>" onClick="highLightToggle(this.id);">
            <td><?=$name?>&nbsp;</td>
            <td><?=$row['dept_name']?>&nbsp;</td>
            <td><?=$row['email']?>&nbsp;</td>
            <td><?=Format::phone($row['phone'])?>&nbsp;<?=$ext?></td>
            <td><?=Format::phone($row['mobile'])?>&nbsp;</td>
        </tr>
        <?
        $class = ($class =='row2') ?'row1':'row2';
    }
    ?>
</table>
<?
else:
echo "<b>Problems displaying directory</b>";
endif;
include_once(STAFFINC_DIR.'footer.inc.php');
?>
