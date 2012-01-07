<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));

$info['phrase']=($errors && $_POST['phrase'])?Format::htmlchars($_POST['phrase']):$cfg->getAPIPassphrase();
$select='SELECT * ';
$from='FROM '.API_KEY_TABLE;
$where='';
$sortOptions=array('date'=>'created','ip'=>'ipaddr');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
//Sorting options...
if($_REQUEST['sort']) {
    $order_column =$sortOptions[$_REQUEST['sort']];
}

if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}
$order_column=$order_column?$order_column:'ipaddr';
$order=$order?$order:'ASC';
$order_by=" ORDER BY $order_column $order ";

$total=db_count('SELECT count(*) '.$from.' '.$where);
$pagelimit=1000;//No limit.
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('admin.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
$query="$select $from $where $order_by";
//echo $query;
$result = db_query($query);
$showing=db_num_rows($result)?$pageNav->showing():'';
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..
$deletable=0;
?>
<div class="msg"><?php print translate("API_KEYS");?></div>
<hr>
<div><b><?=$showing?></b></div>
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
   <form action="admin.php?t=api" method="POST" name="api" onSubmit="return checkbox_checker(document.forms['api'],1,0);">
   <input type=hidden name='t' value='api'>
   <input type=hidden name='do' value='mass_process'>
   <tr><td>
    <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
        <tr>
	        <th width="7px">&nbsp;</th>
	        <th><?php print translate("API_KEY");?></th>
            <th width="10" nowrap><?php print translate("TEXT_ACTIVE");?></th>
            <th width="100" nowrap>&nbsp;&nbsp;<?php print translate("IP_ADDRESS");?></th>
	        <th width="150" nowrap>&nbsp;&nbsp;
                <a href="admin.php?t=api&sort=date&order=<?=$negorder?><?=$qstr?>" title="Sort By Create Date <?=$negorder?>"><?php print translate("TEXT_CREATED");?></a></th>
        </tr>
        <?
        $class = 'row1';
        $total=0;
        $active=$inactive=0;
        $sids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($result && db_num_rows($result)):
            $dtpl=$cfg->getDefaultTemplateId();
            while ($row = db_fetch_array($result)) {
                $sel=false;
                $disabled='';
                if($row['isactive'])
                    $active++;
                else
                    $inactive++;
                    
                if($sids && in_array($row['id'],$sids)){
                    $class="$class highlight";
                    $sel=true;
                }
                ?>
            <tr class="<?=$class?>" id="<?=$row['id']?>">
                <td width=7px>
                  <input type="checkbox" name="ids[]" value="<?=$row['id']?>" <?=$sel?'checked':''?>
                        onClick="highLight(this.value,this.checked);">
                <td>&nbsp;<?=$row['apikey']?></td>
                <td><?=$row['isactive']?translate("YES"):translate("NO")?></td>
                <td>&nbsp;<?=$row['ipaddr']?></td>
                <td>&nbsp;<?=Format::db_datetime($row['created'])?></td>
            </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //nothin' found!! ?> 
            <tr class="<?=$class?>"><td colspan=5><b>Query returned 0 results</b>&nbsp;&nbsp;<a href="admin.php?t=templates">Index list</a></td></tr>
        <?
        endif; ?>
     
     </table>
    </td></tr>
    <?
    if(db_num_rows($result)>0): //Show options..
     ?>
    <tr>
        <td align="center">
            <?php
            if($inactive) {?>
                <input class="button" type="submit" name="enable" value="Enable"
                     onClick='return confirm("Are you sure you want to ENABLE selected keys?");'>
            <?php
            }
            if($active){?>
            &nbsp;&nbsp;
                <input class="button" type="submit" name="disable" value='<?php print translate("TEXT_DISABLE");?>'
                     onClick='return confirm("Are you sure you want to DISABLE selected keys?");'>
            <?}?>
            &nbsp;&nbsp;
            <input class="button" type="submit" name="delete" value='<?php print translate("LABEL_DELETE");?>' 
                     onClick='return confirm("Are you sure you want to DELETE selected keys?");'>
        </td>
    </tr>
    <?
    endif;
    ?>
    </form>
 </table>
 <br/>
 <div class="msg"><?php print translate("ADD_NEW_IP");?></div>
 <hr>
 <div>
   <?php print translate("ADD_NEW_IP_ADDRESS");?>&nbsp;&nbsp;<font class="error"><?=$errors['ip']?></font>
   <form action="admin.php?t=api" method="POST" >
    <input type=hidden name='t' value='api'>
    <input type=hidden name='do' value='add'>
    <?php print translate("NEW_IP");?>:
    <input name="ip" size=30 value="<?=($errors['ip'])?Format::htmlchars($_REQUEST['ip']):''?>" />
    <font class="error">*&nbsp;</font>&nbsp;&nbsp;
     &nbsp;&nbsp; <input class="button" type="submit" name="add" value='<?php print translate("ADD");?>'>
    </form>
 </div>
 <br/>
 <div class="msg"><?php print translate("API_PASSPHRASE");?></div>
 <hr>
 <div>
   <?php print translate("API_PASSPHRASE_REQUIREMENTS");?><br/>
   <form action="admin.php?t=api" method="POST" >
    <input type=hidden name='t' value='api'>
    <input type=hidden name='do' value='update_phrase'>
    <?php print translate("PHRASE");?>:
    <input name="phrase" size=50 value="<?=Format::htmlchars($info['phrase'])?>" />
    <font class="error">*&nbsp;<?=$errors['phrase']?></font>&nbsp;&nbsp;
     &nbsp;&nbsp; <input class="button" type="submit" name="update" value='<?php print translate("LABEL_SUBMIT");?>'>
    </form>
    <br/><br/>
    <div><i><?php print translator("API_PASSPHRASE_NOTE");?></i></div>
 </div>
