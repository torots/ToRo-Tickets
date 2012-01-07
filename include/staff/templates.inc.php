<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

$select='SELECT tpl.*,count(dept.tpl_id) as depts ';
$from='FROM '.EMAIL_TEMPLATE_TABLE.' tpl '.
      'LEFT JOIN '.DEPT_TABLE.' dept USING(tpl_id) ';
$where='';
$sortOptions=array('date'=>'tpl.created','name'=>'tpl.name');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
//Sorting options...
if($_REQUEST['sort']) {
    $order_column =$sortOptions[$_REQUEST['sort']];
}

if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}
$order_column=$order_column?$order_column:'name';
$order=$order?$order:'ASC';
$order_by=" ORDER BY $order_column $order ";

$total=db_count('SELECT count(*) '.$from.' '.$where);
$pagelimit=1000;//No limit.
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('admin.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
$query="$select $from $where GROUP BY tpl.tpl_id $order_by";
//echo $query;
$result = db_query($query);
$showing=db_num_rows($result)?$pageNav->showing():'';
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..
$deletable=0;
?>
<div class="msg"><?php print translate("LABEL_EMAIL_TEMPLATES");?></div>
<hr>
<div><b><?=$showing?></b></div>
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
   <form action="admin.php?t=templates" method="POST" name="tpl" onSubmit="return checkbox_checker(document.forms['tpl'],1,0);">
   <input type=hidden name='t' value='templates'>
   <input type=hidden name='do' value='mass_process'>
   <tr><td>
    <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
        <tr>
	        <th width="7px">&nbsp;</th>
	        <th>
                <a href="admin.php?t=templates&sort=name&order=<?=$negorder?><?=$qstr?>" title="Sort by name <?=$negorder?>"><?php print translate("LABEL_NAME");?></a></th>
            <th width="20" nowrap><?php print translate("IN_USE");?></th>
	        <th width="170" nowrap>&nbsp;&nbsp;
                <a href="admin.php?t=templates&sort=date&order=<?=$negorder?><?=$qstr?>" title="Sort By Create Date <?=$negorder?>"><?php print translate("LABEL_LAST_UPDATED");?></a></th>
            <th width="170" nowrap><?php print translate("TEXT_CREATED");?></th>
        </tr>
        <?
        $class = 'row1';
        $total=0;
        $sids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($result && db_num_rows($result)):
            $dtpl=$cfg->getDefaultTemplateId();
            while ($row = db_fetch_array($result)) {
                $sel=false;
                $disabled='';
                if($dtpl==$row['tpl_id'] || $row['depts'])
                    $disabled='disabled';
                else {
                    $deletable++;
                    if($sids && in_array($row['tpl_id'],$sids)){
                        $class="$class highlight";
                        $sel=true;
                    }
                }
                ?>
            <tr class="<?=$class?>" id="<?=$row['tpl_id']?>">
                <td width=7px>
                  <input type="checkbox" name="ids[]" value="<?=$row['tpl_id']?>" <?=$sel?'checked':''?> <?=$disabled?>
                        onClick="highLight(this.value,this.checked);">
                <td><a href="admin.php?t=templates&id=<?=$row['tpl_id']?>"><?=$row['name']?></a></td>
                <td><?=$disabled?'Yes':'No'?></td>
                <td><?=Format::db_datetime($row['updated'])?></td>
                <td><?=Format::db_datetime($row['created'])?></td>
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
    if(db_num_rows($result)>0 && $deletable): //Show options..
     ?>
    <tr>
        <td align="center">
            <input class="button" type="submit" name="delete" value="Delete Template(s)" 
                     onClick='return confirm("Are you sure you want to DELETE selected template(s)?");'>
        </td>
    </tr>
    <?
    endif;
    ?>
    </form>
 </table>
 <br/>
 <div class="msg"><?php print translate("ADD_NEW_TEMPLATE");?></div>
 <hr>
 <div>
   <?php print translate("CREATE_NEW_TEMPLATE_NOTE");?><br/>
   <form action="admin.php?t=templates" method="POST" >
    <input type=hidden name='t' value='templates'>
    <input type=hidden name='do' value='add'>
    <?php print translate("LABEL_NAME");?>:
    <input name="name" size=30 value="<?=($errors)?Format::htmlchars($_REQUEST['name']):''?>" />
    <font class="error">*&nbsp;<?=$errors['name']?></font>&nbsp;&nbsp;
    <?php print translate("COPY");?>: 
    <select name="copy_template">
        <option value=0><?php print translate("SELECT_TEMPLATE_TO_COPY");?></option>
          <?
          $result=db_query('SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_TABLE);
          while (list($id,$name)= db_fetch_row($result)){ ?>
              <option value="<?=$id?>"><?=$name?></option>
                  <?
          }?>
     </select>&nbsp;<font class="error">*&nbsp;<?=$errors['copy_template']?></font>
     &nbsp;&nbsp; <input class="button" type="submit" name="add" value='<?php print translate("ADD");?>'>
 </div>
 <br/>
 <div class="msg"><?php print translate("VARIABLES");?></div>
 <hr>
 <div>
 <?php print translate("VARIABLES_DESCRIPTION");?>
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
    <tr><td width="50%" valign="top"><b><?php print translate("BASE_VARIABLES");?></b></td><td><b><?php print translate("OTHER_VARIABLES");?></b></td></tr>
    <tr>
        <td width="50%" valign="top">
            <table width="100%" border="0" cellspacing=1 cellpadding=1>
                <tr><td width="100">%id</td><td><?php print translate("LABEL_TICKET_ID");?></td></tr>
                <tr><td>%ticket</td><td><?php print translate("LABEL_TICKET_NUMBER");?></td></tr>
                <tr><td>%email</td><td><?php print translate("LABEL_EMAIL_ADDRESS");?></td></tr>
                <tr><td>%name</td><td><?php print translate("FULL_NAME");?></td></tr>
                <tr><td>%subject</td><td><?php print translate("LABEL_SUBJECT");?></td></tr>
                <tr><td>%topic</td><td><?php print translate("LABEL_HELP_TOPIC");?></td></tr>
                <tr><td>%phone</td><td><?php print translate("PHONE_NUMBER_AND_EXT");?></td></tr>
                <tr><td>%status</td><td><?php print translate("LABEL_STATUS");?></td></tr>
                <tr><td>%priority</td><td><?php print translate("LABEL_PRIORITY");?></td></tr>
                <tr><td>%dept</td><td><?php print translate("LABEL_DEPARTMENT");?></td></tr>
                <tr><td>%assigned_staff</td><td><?php print translate("ASSIGNED_STAFF");?></td></tr>
                <tr><td>%createdate</td><td><?php print translate("DATE_CREATED");?></td></tr>
                <tr><td>%duedate</td><td><?php print translate("TEXT_DUE_DATE");?></td></tr>
                <tr><td>%closedate</td><td><?php print translate("DATE_CLOSED");?></td></tr>
        </table>
        </td>
        <td valign="top">
            <table width="100%" border="0" cellspacing=1 cellpadding=1>
                <tr><td width="100">%message</td><td><?php print translate("MESSAGE_INCOMING");?></td></tr>
                <tr><td>%response</td><td><?php print translate("RESPONSE_OUTGOING");?></td></tr>
                <tr><td>%note</td><td><?php print translate("LABEL_INTERNAL_NOTE");?></td></tr>
                <tr><td>%staff</td><td><?php print translate("VARIABLE_STAFFS_NAME");?></td></tr>
                <tr><td>%assignee</td><td><?php print translate("ASSIGNED_STAFF");?></td></tr>
                <tr><td>%assigner</td><td><?php print translate("STAFF_ASSIGNING_TICKET");?></td></tr>
                <tr><td>%url</td><td>toroTS's <?php print translate("BASE_URL");?></td></tr>

            </table>
        </td>
    </tr>
 </table>
 </div>




