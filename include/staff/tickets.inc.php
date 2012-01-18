<?php
if(!defined('OSTSCPINC') || !@$thisuser->isStaff()) die(print translate("TEXT_ACCESS_DENIED"));

//Get ready for some deep shit..(I admit..this could be done better...but the shit just works... so shutup for now).

$qstr='&'; //Query string collector
if($_REQUEST['status']) { //Query string status has nothing to do with the real status used below; gets overloaded.
    $qstr.='status='.urlencode($_REQUEST['status']);
}

//See if this is a search
$search=$_REQUEST['a']=='search'?true:false;
$searchTerm='';
//make sure the search query is 3 chars min...defaults to no query with warning message
if($search) {
  $searchTerm=$_REQUEST['query'];
  if( ($_REQUEST['query'] && strlen($_REQUEST['query'])<3) 
      || (!$_REQUEST['query'] && isset($_REQUEST['basic_search'])) ){ //Why do I care about this crap...
      $search=false; //Instead of an error page...default back to regular query..with no search.
      $errors['err']=translate('ERROR_Search_term_more_3');
      $searchTerm='';
  }
}
$showoverdue=$showanswered=false;
$staffId=0; //Nothing for now...TODO: Allow admin and manager to limit tickets to single staff level.
//Get status we are actually going to use on the query...making sure it is clean!
$status=null;
switch(strtolower($_REQUEST['status'])){ //Status is overloaded
    case 'open':
        $status='open';
        break;
    case 'pending':
        $status='pending';
        break;
    case 'closed':
        $status='closed';
        break;
    case 'overdue':
        $status='open';
        $showoverdue=true;
        $results_type=translate(TEXT_OVERDUE_TICKETS);
        break;
    case 'assigned':
        //$status='Open'; //
        $staffId=$thisuser->getId();
        break;
    case 'answered':
        $status='open';
        $showanswered=true;
        $results_type=translate(TEXT_ANSWERED_TICKETS);
        break;
    default:
        if(!$search)
            $status='open';
}

// This sucks but we need to switch queues on the fly! depending on stats fetched on the parent.
if($stats) { 
    if(!$stats['open'] && (!$status || $status=='open')){
        if(!$cfg->showAnsweredTickets() && $stats['answered']) {
             $status='open';
             $showanswered=true;
             $results_type=translate(TEXT_ANSWERED_TICKETS);
//        }elseif(!$stats['answered']) { //no open or answered tickets (+-queue?) - show closed tickets.???
//            $status='closed';
//            $results_type=translate(TEXT_CLOSED_TICKETS);
        }
    }
}

$qwhere ='';
/* DEPTS
   STRICT DEPARTMENTS BASED (a.k.a Categories) PERM. starts the where 
   if dept returns nothing...show only tickets without dept which could mean..none?
   Note that dept selected on search has nothing to do with departments allowed.
   User can also see tickets assigned to them regardless of the ticket's dept.
*/
$depts=$thisuser->getDepts(); //if dept returns nothing...show only tickets without dept which could mean..none...and display an error. huh?
if(!$depts or !is_array($depts) or !count($depts)){
    //if dept returns nothing...show only orphaned tickets (without dept) which could mean..none...and display an error.
    $qwhere =' WHERE ticket.dept_id IN ( 0 ) ';
}else if($thisuser->isadmin()){
    //user allowed acess to all departments.
    $qwhere =' WHERE 1'; // Brain fart...can not thing of a better way other than selecting all depts + 0 ..wasted query in my book?
}else{
    //limited depts....user can access tickets assigned to them regardless of the dept.
    $qwhere =' WHERE (ticket.dept_id IN ('.implode(',',$depts).') OR ticket.staff_id='.$thisuser->getId().')';
}


//STATUS
if($status){
    $qwhere.=' AND status='.db_input(strtolower($status));    
}

//Sub-statuses Trust me!
if($staffId && ($staffId==$thisuser->getId())) { //Staff's assigned tickets.
    $results_type=translate('TEXT_Assigned_Tickets');
    $qwhere.=' AND ticket.status="open" AND ticket.staff_id='.db_input($staffId);
}elseif($showoverdue) { //overdue
    $qwhere.=' AND isoverdue=1 ';
}elseif($showanswered) { ////Answered
    $qwhere.=' AND isanswered=1 ';
}elseif(!$search && !$cfg->showAnsweredTickets() && !strcasecmp($status,'open')) {
    $qwhere.=' AND isanswered=0 ';
}
 

//Show assigned?? Admin can not be limited. Dept managers see all tickets within the dept.
if(!$cfg->showAssignedTickets() && !$thisuser->isadmin()) {
    $qwhere.=' AND (ticket.staff_id=0 OR ticket.staff_id='.db_input($thisuser->getId()).' OR dept.manager_id='.db_input($thisuser->getId()).') ';
}


//Search?? Somebody...get me some coffee 
$deep_search=false;
if($search):
    $qstr.='&a='.urlencode($_REQUEST['a']);
    $qstr.='&t='.urlencode($_REQUEST['t']);
    if(isset($_REQUEST['advance_search'])){ //advance search box!
        $qstr.='&advance_search=Search';
    }

    //query
    if($searchTerm){
        $qstr.='&query='.urlencode($searchTerm);
        $queryterm=db_real_escape($searchTerm,false); //escape the term ONLY...no quotes.
        if(is_numeric($searchTerm)){
            $qwhere.=" AND ticket.ticketID LIKE '$queryterm%'";
        }elseif(strpos($searchTerm,'@') && Validator::is_email($searchTerm)){ //pulling all tricks!
            $qwhere.=" AND ticket.email='$queryterm'";
        }else{//Deep search!
            //This sucks..mass scan! search anything that moves! 
            
            $deep_search=true;
            if($_REQUEST['stype'] && $_REQUEST['stype']=='FT') { //Using full text on big fields.
                $qwhere.=" AND ( ticket.email LIKE '%$queryterm%'".
                            " OR ticket.name LIKE '%$queryterm%'".
                            " OR ticket.subject LIKE '%$queryterm%'".
                            " OR note.title LIKE '%$queryterm%'".
                            " OR MATCH(message.message)   AGAINST('$queryterm')".
                            " OR MATCH(response.response) AGAINST('$queryterm')".
                            " OR MATCH(note.note) AGAINST('$queryterm')".
                            ' ) ';
            }else{
            $qwhere.=" AND ( ticket.email LIKE '%$queryterm%'".
                        " OR ticket.name LIKE '%$queryterm%'".
                        " OR ticket.subject LIKE '%$queryterm%'".
                        " OR message.message LIKE '%$queryterm%'".
                        " OR response.response LIKE '%$queryterm%'".
                        " OR note.note LIKE '%$queryterm%'".
                        " OR note.title LIKE '%$queryterm%'".
                        ' ) ';
        }
    }
    }
    //department
    if($_REQUEST['dept'] && ($thisuser->isadmin() || in_array($_REQUEST['dept'],$thisuser->getDepts()))) {
    //This is dept based search..perm taken care above..put the sucker in.
        $qwhere.=' AND ticket.dept_id='.db_input($_REQUEST['dept']);
        $qstr.='&dept='.urlencode($_REQUEST['dept']);
    }
    //dates
    $startTime  =($_REQUEST['startDate'] && (strlen($_REQUEST['startDate'])>=8))?strtotime($_REQUEST['startDate']):0;
    $endTime    =($_REQUEST['endDate'] && (strlen($_REQUEST['endDate'])>=8))?strtotime($_REQUEST['endDate']):0;
    if( ($startTime && $startTime>time()) or ($startTime>$endTime && $endTime>0)){
        $errors['err']='Entered date span is invalid. Selection ignored.';
        $startTime=$endTime=0;
    }else{
        //Have fun with dates.
        if($startTime){
            $qwhere.=' AND ticket.created>=FROM_UNIXTIME('.$startTime.')';
            $qstr.='&startDate='.urlencode($_REQUEST['startDate']);
                        
        }
        if($endTime){
            $qwhere.=' AND ticket.created<=FROM_UNIXTIME('.$endTime.')';
            $qstr.='&endDate='.urlencode($_REQUEST['endDate']);
        }
}

endif;

//I admit this crap sucks...but who cares??
//$sortOptions=array('date'=>'ticket.created','ID'=>'ticketID','pri'=>'priority_urgency','dept'=>'dept_name');
$sortOptions=array('date'=>'ticket.created','ID'=>'ticketID','pri'=>'priority_urgency','dept'=>'dept_name','ass'=>'firstname','timeopen'=>'created','subject'=>'subject','from'=>'name');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');

//Sorting options...
if($_REQUEST['sort']) {
    $order_by =$sortOptions[$_REQUEST['sort']];
}
if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}
if($_GET['limit']){
    $qstr.='&limit='.urlencode($_GET['limit']);
}
if(!$order_by && $showanswered) {
    $order_by='ticket.lastresponse DESC, ticket.created'; //No priority sorting for answered tickets.
}elseif(!$order_by && !strcasecmp($status,'closed')){
    $order_by='ticket.closed DESC, ticket.created'; //No priority sorting for closed tickets.
}


$order_by =$order_by?$order_by:'priority_urgency,effective_date DESC ,ticket.created';
$order=$order?$order:'DESC';
$pagelimit=$_GET['limit']?$_GET['limit']:$thisuser->getPageLimit();
$pagelimit=$pagelimit?$pagelimit:PAGE_LIMIT; //true default...if all fails.
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;


$qselect = 'SELECT DISTINCT ticket.ticket_id,lock_id,ticketID,ticket.dept_id,ticket.staff_id,subject,name,ticket.email,dept_name,staff.firstname,staff.lastname '.
 ',ticket.status,ticket.source,isoverdue,ticket.created,pri.*,CASE WHEN status = "open" THEN FLOOR(TIME_TO_SEC(TIMEDIFF(now(),ticket.created))/60) ELSE FLOOR(TIME_TO_SEC(TIMEDIFF(ticket.closed,ticket.created))/60) END AS timeopen,count(attach.attach_id) as attachments ';
$qfrom=' FROM '.TICKET_TABLE.' ticket LEFT JOIN '.DEPT_TABLE.' dept ON ticket.dept_id=dept.dept_id '.
 ' LEFT JOIN '.STAFF_TABLE.' staff ON ticket.staff_id=staff.staff_id';

if($search && $deep_search) {
    $qfrom.=' LEFT JOIN '.TICKET_MESSAGE_TABLE.' message ON (ticket.ticket_id=message.ticket_id )';
    $qfrom.=' LEFT JOIN '.TICKET_RESPONSE_TABLE.' response ON (ticket.ticket_id=response.ticket_id )';
    $qfrom.=' LEFT JOIN '.TICKET_NOTE_TABLE.' note ON (ticket.ticket_id=note.ticket_id )';
}

$qgroup=' GROUP BY ticket.ticket_id';
//get ticket count based on the query so far..
$total=db_count("SELECT count(DISTINCT ticket.ticket_id) $qfrom $qwhere");
//pagenate
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL('tickets.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
//
//Ok..lets roll...create the actual query
//ADD attachment,priorities and lock crap
$qselect.=' ,count(attach.attach_id) as attachments, IF(ticket.reopened is NULL,ticket.created,ticket.reopened) as effective_date';
$qfrom.=' LEFT JOIN '.TICKET_PRIORITY_TABLE.' pri ON ticket.priority_id=pri.priority_id '.
        ' LEFT JOIN '.TICKET_LOCK_TABLE.' tlock ON ticket.ticket_id=tlock.ticket_id AND tlock.expire>NOW() '.
        ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  ticket.ticket_id=attach.ticket_id ';

$query="$qselect $qfrom $qwhere $qgroup ORDER BY $order_by $order LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
// echo $query;
$tickets_res = db_query($query);
$showing=db_num_rows($tickets_res)?$pageNav->showing():"";
if(!$results_type) {
    $results_type=($search)?translate(TEXT_SEARCH_RESULTS):translate('TEXT_'.strtoupper($status).'_TICKETS');
}
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..

//Permission  setting we are going to reuse.
$canDelete=$canClose=false;
$canDelete=$thisuser->canDeleteTickets();
$canClose=$thisuser->canCloseTickets();
$basic_display=!isset($_REQUEST['advance_search'])?true:false;

//YOU BREAK IT YOU FIX IT.
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
<!-- SEARCH FORM START -->
<div id='basic' style="display:<?=$basic_display?'block':'none'?>">
    <form action="tickets.php" method="get">
    <input type="hidden" name="a" value="search">
    <table>
        <tr>
            <td><?php print translate(LABEL_QUERY);?>: </td>
            <td><input type="text" id="query" name="query" size=30 value="<?=Format::htmlchars($_REQUEST['query'])?>"></td>
            <td><input type="submit" name="basic_search" class="button" value="<?php print translate(LABEL_SEARCH); ?>">
             &nbsp;[ <a href="#" onClick="showHide('basic','advance'); return false;"><?php print translate(LABEL_ADVANCED);?></a> ] </td>
        </tr>
    </table>
    </form>
</div>
<div id='advance' style="display:<?=$basic_display?'none':'block'?>">
 <form action="tickets.php" method="get">
 <input type="hidden" name="a" value="search">
  <table>
    <tr>
        <td><?php print translate(LABEL_QUERY);?>: </td><td><input type="text" id="query" name="query" value="<?=Format::htmlchars($_REQUEST['query'])?>"></td>
        <td><?php print translate(LABEL_DEPT);?>:</td>
        <td><select name="dept"><option value=0><?php print translate(TEXT_ALL_DEPARTMENTS)?></option>
            <?
                //Showing only departments the user has access to...
                $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE;
                if(!$thisuser->isadmin())
                    $sql.=' WHERE dept_id IN ('.implode(',',$thisuser->getDepts()).')';
                
                $depts= db_query($sql);
                while (list($deptId,$deptName) = db_fetch_row($depts)){
                $selected = ($_GET['dept']==$deptId)?'selected':''; ?>
                <option value="<?=$deptId?>"<?=$selected?>><?=$deptName?></option>
            <?
            }?>
            </select>
        </td>
        <td><?php print translate(LABEL_STATUS_IS)?>:</td><td>
    
        <select name="status">
            <option value='any' selected ><?php print translate(TEXT_ANY_STATUS)?></option>
            <option value="open" <?=!strcasecmp($_REQUEST['status'],'Open')?'selected':''?>><?php print translate('TEXT_OPEN');?></option>
            <option value="overdue" <?=!strcasecmp($_REQUEST['status'],'overdue')?'selected':''?>><?php print translate('TEXT_OVERDUE');?></option>
            <option value="closed" <?=!strcasecmp($_REQUEST['status'],'Closed')?'selected':''?>><?php print translate('TEXT_CLOSED');?></option>
        </select>
        </td>
     </tr>
    </table>
    <div>
        <?php print translate('LABEL_DATE_SPAN');?>:
        &nbsp;<?php print translate('LABEL_FROM');?>&nbsp;<input id="sd" name="startDate" value="<?=Format::htmlchars($_REQUEST['startDate'])?>" 
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF>
            <a href="#" onclick="event.cancelBubble=true;calendar(getObj('sd'));"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp; <?php print translate('LABEL_TO');?> &nbsp;&nbsp;
            <input id="ed" name="endDate" value="<?=Format::htmlchars($_REQUEST['endDate'])?>" 
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF >
                <a href="#" onclick="event.cancelBubble=true;calendar(getObj('ed')); return false;"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp;
    </div>
    <table>
    <tr>
       <td><?php print translate('LABEL_TYPE');?>:</td>
       <td>       
        <select name="stype">
            <option value="LIKE" <?=(!$_REQUEST['stype'] || $_REQUEST['stype'] == 'LIKE') ?'selected':''?>>Scan (%)</option>
            <option value="FT"<?= $_REQUEST['stype'] == 'FT'?'selected':''?>>Fulltext</option>
        </select>
 

       </td>
       <td><?php print translate('LABEL_SORT_BY');?>::</td><td>
        <?php 
         $sort=$_GET['sort']?$_GET['sort']:'date';
        ?>
        <select name="sort">
    	    <option value="ID" <?= $sort== 'ID' ?'selected':''?>><?php print translate(LABEL_TICKET_NUMBER)?></option>
            <option value="pri" <?= $sort == 'pri' ?'selected':''?>><?php print translate(LABEL_PRIORITY)?></option>
            <option value="date" <?= $sort == 'date' ?'selected':''?>><?php print translate(LABEL_DATE)?></option>
            <option value="dept" <?= $sort == 'dept' ?'selected':''?>><?php print translate(LABEL_DEPT)?></option>
        </select>
        <select name="order">
            <option value="DESC"<?= $_REQUEST['order'] == 'DESC' ?'selected':''?>><?php print translate(LABEL_DESCENDING)?></option>
            <option value="ASC"<?= $_REQUEST['order'] == 'ASC'?'selected':''?>><?php print translate(LABEL_ASCENDING)?></option>
        </select>
       </td>
        <td><?php print translate(LABEL_RESULTS_PER_PAGE)?>:</td><td>
        <select name="limit">
        <?
         $sel=$_REQUEST['limit']?$_REQUEST['limit']:15;
         for ($x = 5; $x <= 25; $x += 5) {?>
            <option  value="<?=$x?>" <?=($sel==$x )?'selected':''?>><?=$x?></option>
        <?}?>
        </select>
     </td>
     <td>
     <input type="submit" name="advance_search" class="button" value="<?php print translate(LABEL_SEARCH)?>">
       &nbsp;[ <a href="#" onClick="showHide('advance','basic'); return false;" ><?php print translate(LABEL_BASIC)?></a> ]
    </td>
  </tr>
 </table>
 </form>
</div>
<script type="text/javascript">

    var options = {
        script:"ajax.php?api=tickets&f=search&limit=10&",
        varname:"input",
        shownoresults:false,
        maxresults:10,
        callback: function (obj) { document.getElementById('query').value = obj.id; document.forms[0].submit();}
    };
    var autosug = new bsn.AutoSuggest('query', options);
</script>
<!-- SEARCH FORM END -->
<div style="margin-bottom:20px">
 <table width="100%" border="0" cellspacing=0 cellpadding=0 align="center">
    <tr>
        <td width="80%" class="msg" >&nbsp;<b><?=$showing?>&nbsp;&nbsp;&nbsp;<?=$results_type?></b></td>
        <td nowrap style="text-align:right;padding-right:20px;">
            <a href=""><img src="images/refresh.gif" alt="Refresh" border=0></a>
        </td>
    </tr>
 </table>
 <table width="100%" border="0" cellspacing=1 cellpadding=2>
    <form action="tickets.php" method="POST" name='tickets' onSubmit="return checkbox_checker(this,1,0);">
    <input type="hidden" name="a" value="mass_process" >
    <input type="hidden" name="status" value="<?=$statusss?>" >
    <tr><td>
       <table width="100%" border="0" cellspacing=0 cellpadding=2 class="dtable" align="center">
        <tr>

	<?php   // Table Headers are stored in _columns, print off the active headers here and order by their weight.

		$thList = "SELECT th_code FROM ".COLUMNS_TABLE." WHERE active=1 ORDER BY weight";
		$result = mysql_query($thList)or die(mysql_error());
		
		while($th = mysql_fetch_array($result)){
		eval('?>' . $th['th_code'] . '<?php ');
		}

	?>

        </tr>
        <?
        $class = "row1";
        $total=0;
        if($tickets_res && ($num=db_num_rows($tickets_res))):
            while ($row = db_fetch_array($tickets_res)) {
                $tag=$row['staff_id']?'assigned':'openticket';
                $flag=null;
                if($row['lock_id'])
                    $flag='locked';
                elseif($row['staff_id'])
                    $flag='assigned';
                elseif($row['isoverdue'])
                    $flag='overdue';

                $tid=$row['ticketID'];
                $subject = Format::truncate($row['subject'],40);
                if(!strcasecmp($row['status'],'open') && !$row['isanswered'] && !$row['lock_id']) {
                    $tid=sprintf('<b>%s</b>',$tid);
                    //$subject=sprintf('<b>%s</b>',Format::truncate($row['subject'],40)); // Making the subject bold is too much for the eye
                }
                ?>
            <tr class="<?=$class?> " id="<?=$row['ticket_id']?>">

	    <?php   // Tooltips!

	    $sql = "SELECT tool_tips FROM ".CONFIG_TABLE;
	    $result = mysql_query($sql)or die(mysql_error());
	    $tooltip_check = mysql_result($result,0);

	    // Check to see if tooltips are turned on.
	    $responseTime = "SELECT created from ".TICKET_RESPONSE_TABLE." WHERE ticket_id=" .$row['ticket_id']. " ORDER BY created DESC LIMIT 1";
	    //print "Rtime: " .$responseTime. "<br />";
	    $rTime = mysql_query($responseTime)or die(mysql_error());

	    $num_rows = mysql_num_rows($rTime);

	    if($num_rows>0){
	    while($time = mysql_fetch_array($rTime)){
	     if($time['created']!=''){ $rCompare = $time['created']; }else{ $rCompare=''; }
	    }
	    }else{ $rCompare='0000-00-00 00:00:00'; }
	    

	    $messageTime  = "SELECT created from ".TICKET_MESSAGE_TABLE." WHERE ticket_id=" .$row['ticket_id']. " ORDER BY created DESC LIMIT 1";
	    //print "Mtime: " .$messageTime. "<br />";
	    $mTime = mysql_query($messageTime)or die(mysql_error());
	    while($time = mysql_fetch_array($mTime)){
	    
	    if($time['created']!=''){ $mCompare = $time['created']; }else{ $mCompare=''; }
            }

	    //print "M: " .$mCompare. "<br />";
	    //print "R: " .$rCompare. "<br />";

	    if($mCompare > $rCompare){ $search='message'; }else{ $search='response'; }

	    // print "Search: " .$search. "<br />";

	    $lastComm = "SELECT " .$search. " FROM ".TICKET_TABLE."_" .$search. " ".
			"WHERE ".TICKET_TABLE."_" .$search. ".ticket_id='" .$row['ticket_id']. "' ".
			"ORDER BY created DESC LIMIT 1";

	    // print $lastComm;

	    $CommResult=mysql_query($lastComm) or die(mysql_error());
   
	    while($tip = mysql_fetch_array($CommResult)){

	    if($tip['message']==''){ $tooltip=$tip['response']; }else{ $tooltip=$tip['message']; }
	    if($tip['response']==''){ $tooltip=$tip['message']; }else{ $tooltip=$tip['response']; }
	   
        $tooltip = implode(' ', array_slice(explode(' ', $tooltip), 0, 50));
        if(trim($tooltip)==''){ $tooltip = "No response or messages yet"; }
 
   
		}// End comm while loop
	    
	    // Table Data is stored in _columns, print off the active data cells here and order by their weight.

                $tdList = "SELECT td_code FROM ".COLUMNS_TABLE." WHERE active=1 ORDER BY weight";
		        // print $tdList;
                $result = mysql_query($tdList)or die(mysql_error());

                while($td = mysql_fetch_array($result)){
                eval('?>' . $td['td_code'] . '<?php ');
                }

            ?>
	    
            </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //no tickets found!! ?> 
            <tr class="<?=$class?>"><td colspan=8><b><?php print translate('LABEL_QUERY_RETURNED_ZERO_RESULT')?></b></td></tr>
        <?
        endif; ?>
       </table>
    </td></tr>
    <?
    if($num>0){ //if we actually had any tickets returned.
    ?>
        <tr><td style="padding-left:20px">
            <?if($canDelete || $canClose) { ?>
            <?php print translate('LABEL_SELECT')?>:
                <a href="#" onclick="return select_all(document.forms['tickets'],true)"><?php print translate('LABEL_ALL')?></a>&nbsp;
                <a href="#" onclick="return reset_all(document.forms['tickets'])"><?php print translate('LABEL_NONE')?></a>&nbsp;
                <a href="#" onclick="return toogle_all(document.forms['tickets'],true)"><?php print translate('LABEL_TOgGLE')?></a>&nbsp;
            <?}?>
            <?php print translate('LABEL_PAGE')?>:<?=$pageNav->getPageLinks()?>
        </td></tr>
        <?php if($canClose or $canDelete) { ?>
        <tr><td align="center"> <br>
            <?
            $status=$_REQUEST['status']?$_REQUEST['status']:$status;

            //If the user can close the ticket...mass reopen is allowed.
            //If they can delete tickets...they are allowed to close--reopen..etc.
            switch (strtolower($status)) {
                case 'closed': ?>
                    <input class="button" type="submit" name="reopen" value="<?php print translate('LABEL_REOPEN')?>"
                        onClick=' return confirm("Are you sure you want to reopen selected tickets?");'>
                    <?
                    break;
                case 'open':
                case 'answered':
                case 'assigned':
                    ?>
                    <input class="button" type="submit" name="overdue" value="<?php print translate('LABEL_OVERDUE')?>"
                        onClick=' return confirm("Are you sure you want to mark selected tickets overdue/stale?");'>
                    <input class="button" type="submit" name="close" value="<?php print translate('LABEL_CLOSE')?>"
                        onClick=' return confirm("Are you sure you want to close selected tickets?");'>
                    <?
                    break;
                default: //search??
                    ?>
                    <input class="button" type="submit" name="close" value="<?php print translate('LABEL_CLOSE')?>"
                        onClick=' return confirm("Are you sure you want to close selected tickets?");'>
                    <input class="button" type="submit" name="reopen" value="<?php print translate('LABEL_REOPEN')?>"
                        onClick=' return confirm("Are you sure you want to reopen selected tickets?");'>
            <?
            }
            if($canDelete) {?>
	        <input class="button" type="submit" name="banDelete" value='<?php print translate("LABEL_BAN_AND_DELETE");?>'
                        onClick=' return confirm("Are you ABSOLUTLEY CERTAIN this was spam?");'>
                <input class="button" type="submit" name="delete" value="<?php print translate('LABEL_DELETE')?>" 
                    onClick=' return confirm("Are you sure you want to DELETE selected tickets?");'>
            <?}?>
        </td></tr>
        <?php }
    } ?>
    </form>
 </table>
</div>

<?
