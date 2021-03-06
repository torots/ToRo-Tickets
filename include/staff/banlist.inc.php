<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

$select='SELECT * ';
$from='FROM '.BANLIST_TABLE;
$where='';
//make sure the search query is 3 chars min...defaults to no query with warning message
if($_REQUEST['a']=='search') {
    if(!$_REQUEST['query'] || strlen($_REQUEST['query'])<3) {
        $errors['err']='Search term must be more than 3 chars';
    }else{
        //Do the search
        $search=true;
        $qstr.='&a='.urlencode($_REQUEST['a']);
        $qstr.='&query='.urlencode($_REQUEST['query']);
        $searchTerm=trim($_REQUEST['query']);
        if(strpos($searchTerm,'@') && Validator::is_email($searchTerm)){ //pulling all tricks!
            $where=' WHERE email='.db_input($searchTerm);
        }else{
            $where=' WHERE email LIKE \'%'.db_real_escape($searchTerm,false).'%\'';
        } 
    }
}
//I admit this crap sucks...but who cares??
$sortOptions=array('date'=>'added','email'=>'email');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
//Sorting options...
if($_REQUEST['sort']) {
    $order_column =$sortOptions[$_REQUEST['sort']];
}

if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}


$order_column=$order_column?$order_column:'added';
$order=$order?$order:'DESC';

$order_by=" ORDER BY $order_column $order ";

$total=db_count('SELECT count(*) '.$from.' '.$where);
$pagelimit=$thisuser->getPageLimit();
$pagelimit=$pagelimit?$pagelimit:PAGE_LIMIT; //true default...if all fails.
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('admin.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
//Ok..lets roll...create the actual query
$query="$select $from $where $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;
$banlist = db_query($query);
$showing=db_num_rows($banlist)?$pageNav->showing():'';
$result_type=($search)?'Search Results':translate("BANNED_EMAIL_ADDRESSES");
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..
$showadd=($errors && $_POST['a']=='add')?true:false;
?>
<div id='search' align="left"  style="display:<?=$showadd?'none':'block'?>;">
    <form action="admin.php?t=settings" method="GET" >
        <input type='hidden' name='t' value='banlist'>
        <input type='hidden' name='a' value='search'>
        <?php print translate("LABEL_QUERY");?>:&nbsp;<input type="text" name="query" value="<?=Format::htmlchars($_REQUEST['query'])?>">
        &nbsp;&nbsp;
        <input type="submit" name="search" class="button" value='<?php print translate("LABEL_SEARCH");?>'>
        &nbsp;<a href="#" class="msg" onClick="showHide('add','search'); return false;">(<?php print translate("ADD");?>)</a>
    </form>
</div>
<div id='add' style="display:<?=$showadd?'block':'none'?>;" align="left">
    <form action="admin.php?t=settings" method="POST" >
    <input type='hidden' name='t' value='banlist'>
    <input type='hidden' name='a' value='add'>
    <?php print translate("LABEL_EMAIL");?>:&nbsp;<input type="text" name="email" value="<?=$_POST['email']?>">
    &nbsp;
    <input type="submit" name="add" class="button" value="Add">
     &nbsp;<a href="#" class="msg" onClick="showHide('add','search'); return false;">(Search)</a>
    </form>
</div>
<div class="msg"><?=$result_type?>:&nbsp;<?=$showing?></div>
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
   <form action="admin.php?t=banlist" method="POST" name="banlist" onSubmit="return checkbox_checker(document.forms['banlist'],1,0);">
   <input type=hidden name='t' value='banlist'>
   <input type=hidden name='a' value='remove'>
   <tr><td>
    <table border="0" cellspacing=0 cellpadding=2 class="dtable" align="center" width="100%">
        <tr>
	        <th width="7px">&nbsp;</th>
	        <th width=250>
                <a href="admin.php?t=banlist&sort=email&order=<?=$negorder?><?=$qstr?>" title="Sort by email <?=$negorder?>"><?php print translate("LABEL_EMAIL");?></a></th>
	        <th width=200 ><?php print translate("SUBMITTER");?></th> 
	        <th width=150 nowrap>
                <a href="admin.php?t=banlist&sort=date&order=<?=$negorder?><?=$qstr?>" title="Sort By Create Date <?=$negorder?>"><?php print translate("DATE_ADDED");?></a></th>
        </tr>
        <?
        $class = 'row1';
        $total=0;
        $sids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($banlist && db_num_rows($banlist)):
            while ($row = db_fetch_array($banlist)) {
                $sel=false;
                if($sids && in_array($row['id'],$sids)){
                    $class="$class highlight";
                    $sel=true;
                }
                ?>
            <tr class="<?=$class?>" id="<?=$row['id']?>">
                <td width=7px>
                  <input type="checkbox" name="ids[]" value="<?=$row['id']?>" <?=$sel?'checked':''?> 
                        onClick="highLight(this.value,this.checked);">
                <td><?=$row['email']?></td>
                <td><?=$row['submitter']?></td>
                <td><?=Format::db_datetime($row['added'])?></td>
            </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //nothin' found!! ?> 
            <tr class="<?=$class?>"><td colspan=4><b>Query returned 0 results</b>&nbsp;&nbsp;<a href="admin.php?t=banlist">Index list</a></td></tr>
        <?
        endif; ?>
     </table>
    </td></tr>
    <?
    if(db_num_rows($banlist)>0): //Show options..
     ?>
    <tr>
        <td style="padding-left:20px">
            Select:&nbsp;
            <a href="#" onclick="return select_all(document.forms['banlist'],true)">All</a>&nbsp;&nbsp;
            <a href="#" onclick="return toogle_all(document.forms['banlist'],true)">Toggle</a>&nbsp;&nbsp;
            <a href="#" onclick="return reset_all(document.forms['banlist'])">None</a>&nbsp;&nbsp;
            &nbsp;page:<?=$pageNav->getPageLinks()?>
            
        </td>
    </tr>
    <tr>
        <td align="center">
            <input class="button" type="submit" name="delete" value='<?php print translate("REMOVE_FROM_LIST");?>' 
                     onClick='return confirm("Are you sure you want to REMOVE selected email from ban list?");'>
        </td>
    </tr>
    <?
    endif;
    ?>
    </form>
 </table>
