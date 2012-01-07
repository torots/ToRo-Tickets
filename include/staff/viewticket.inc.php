<?php
//Note that ticket is initiated in tickets.php.
if(!defined('OSTSCPINC') || !@$thisuser->isStaff() || !is_object($ticket) ) die('Invalid path');
if(!$ticket->getId() or (!$thisuser->canAccessDept($ticket->getDeptId()) and $thisuser->getId()!=$ticket->getStaffId())) die(translate("TEXT_ACCESS_DENIED"));

$info=($_POST && $errors)?Format::input($_POST):array(); //Re-use the post info on error...savekeyboards.org

//Auto-lock the ticket if locking is enabled..if locked already simply renew it.
if($cfg->getLockTime() && !$ticket->acquireLock())
$warn.='Unable to obtain a lock on the ticket';

//We are ready baby...lets roll. Akon rocks!
$dept  = $ticket->getDept();  //Dept
$staff = $ticket->getStaff(); //Assiged staff.
$lock  = $ticket->getLock();  //Ticket lock obj
$id=$ticket->getId(); //Ticket ID.

if($staff)
$warn.='&nbsp;&nbsp;<span class="Icon assignedTicket">Ticket is assigned to '.$staff->getName().'</span>';
if(!$errors['err'] && ($lock && $lock->getStaffId()!=$thisuser->getId()))
$errors['err']='This ticket is currently locked by another staff member!';
if(!$errors['err'] && ($emailBanned=BanList::isbanned($ticket->getEmail())))
$errors['err']='Email is in banlist! Must be removed before any reply/response';
if($ticket->isOverdue())
$warn.='&nbsp;&nbsp;<span class="Icon overdueTicket">Marked overdue!</span>';

?>
<div style="margin-left: 2px; margin-top: 5px; margin-bottom: 10px;border: 0px;" align="left">
<a href="tickets.php"><?php print translate("LABEL_TICKETS");?></a> | <a href='#bottom'><?php print translate("BOTTOM");?></a>
</div>

<table width="100%" cellpadding="2" cellspacing="0" border="0">

	<tr>
		<td class="msg" width=50%><?php print translate("LABEL_TICKET");?> #<?=$ticket->getExtId()?>&nbsp;<a
			href="tickets.php?id=<?=$id?>" title="Reload"><span
				class="Icon refresh">&nbsp;</span> </a></td>

		<td class="msg" width=50%><? if($thisuser->canEditTickets() || ($thisuser->isManager() && $dept->getId()==$thisuser->getDeptId())) { ?>
			<a href="tickets.php?id=<?=$id?>&a=edit" title="Edit Ticket"
			class="Icon editTicket"><?php print translate("EDIT_TICKET");?></a> <?}?>
		</td>
	</tr>
	<tr>
		<td width=50%>
			<table align="center" class="ticketinfo" cellspacing="1"
				cellpadding="3" width="100%" border=0>
				<tr>
					<th><?php print translate("LABEL_STATUS");?>:</th>
					<td><?=$ticket->getStatus()?></td>
				</tr>
				<tr>
					<th><?php print translate("LABEL_PRIORITY");?>:</th>
					<td><?=$ticket->getPriority()?></td>
				</tr>
				<tr>
					<th><?php print translate("LABEL_DEPARTMENT");?>:</th>
					<td><?=Format::htmlchars($ticket->getDeptName())?></td>
				</tr>
				<tr>
					<th><?php print translate("LABEL_CREATE_DATE");?>:</th>
					<td><?=Format::db_datetime($ticket->getCreateDate())?></td>
				</tr>
			</table>
		</td>
		<td width=50% valign="top">
			<table align="center" class="ticketinfo" cellspacing="1"
				cellpadding="3" width="100%" border=0>
				<tr>
					<th><?php print translate("LABEL_NAME");?>:</th>
					<td><?=Format::htmlchars($ticket->getName())?></td>
				</tr>
				<tr>
					<th><?php print translate("LABEL_EMAIL_ADDRESS");?>:</th>
					<td><?php 
					echo $ticket->getEmail();
					if(($related=$ticket->getRelatedTicketsCount())) {
						echo sprintf('&nbsp;&nbsp;<a href="tickets.php?a=search&query=%s" title="Related Tickets">(<b>%d</b>)</a>',
						urlencode($ticket->getEmail()),$related);
					}
					?>
					</td>
				</tr>
				<tr>
					<th><?php print translate("LABEL_PHONE");?>:</th>
					<td><?=Format::phone($ticket->getPhoneNumber())?></td>
				</tr>
				<tr>
					<th><?php print translate("LABEL_TICKET_SOURCE");?>:</th>
					<td><?=$ticket->getSource()?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 class="msg"><?php print translate("LABEL_SUBJECT");?>: <?=Format::htmlchars($ticket->getSubject())?>
		</td>
	</tr>
	<tr>
		<td valign="top" width=50%>
			<table align="center" class="ticketinfo" cellspacing="1"
				cellpadding="3" width="100%" border=0>
				<tr>
					<th><?php print translate("ASSIGNED_STAFF");?>:</th>
					<td><?=$staff?Format::htmlchars($staff->getName()):'- unassigned -'?>
					</td>
				</tr>
				<tr>
					<th nowrap><?php print translate("LAST_RESPONSE");?>:</th>
					<td><?=Format::db_datetime($ticket->getLastResponseDate())?></td>
				</tr>
				<?php
				if($ticket->isOpen()){ ?>
				<tr>
					<th><?php print translate("TEXT_DUE_DATE");?>:</th>
					<td><?=Format::db_datetime($ticket->getDueDate())?></td>
				</tr>
				<?php
				}else { ?>
				<tr>
					<th><?php print translate("DATE_CLOSED");?>:</th>
					<td><?=Format::db_datetime($ticket->getCloseDate())?></td>
				</tr>
				<?php
				}
				?>
			</table>
		</td>
		<td width=50% valign="top">
			<table align="center" class="ticketinfo" cellspacing="1"
				cellpadding="3" width="100%" border=0>
				<tr>
					<th><?php print translate("DATE_CLOSED");?>:</th>
					<td><?
					$ht=$ticket->getHelpTopic();
					echo Format::htmlchars($ht?$ht:'N/A');
					?>
					</td>
				</tr>
				<tr>
					<th><?php print translate("IP_ADDRESS");?>:</th>
					<td><?=$ticket->getIP()?></td>
				</tr>
				<tr>
					<th nowrap><?php print translate("LAST_MESSAGE");?>:</th>
					<td><?=Format::db_datetime($ticket->getLastMessageDate())?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?
        $query='SELECT headers,created FROM '.TICKET_MESSAGE_TABLE.' WHERE ticket_id='.db_input($id).' ORDER BY created DESC LIMIT 1';
          $result=mysql_query($query);
         while ($row = mysql_fetch_assoc($result)) {
         $headers=htmlentities($row['headers']);
         }
?>


<?php if($headers!=''){ ?>
<span class="msg"><a href="#" onclick="ShowHeaders();"><?php print translate("SHOW_HIDE_HEADERS");?></a></span>
<table align="center" id="headerTable" style="display:none" class="ticketinfo" cellspacing="1" cellpadding="3" width="100%" border=0>
     <tr>

          <td><?=nl2br($headers);?>
          </td>
          </tr>
</table>
<?php } ?>

<div>
<?if($errors['err'] && $_POST['a']=='process') {?>
	<p align="center" id="errormessage">
	<?=$errors['err']?>
	</p>
	<?}elseif($msg && $_POST['a']=='process' || $_POST['a']=='update' ) {?>
	<p align="center" id="infomessage">
	<?=$msg?>
	</p>
	<?}elseif($warn) {?>
	<p id="warnmessage">
	<?=$warn?>
	</p>
	<?}?>
</div>
	<?
	//Ticket adminstrative options...admin,managers and staff with manage perm allowed.
	if($thisuser->canManageTickets() || $thisuser->isManager()){ ?>
<table cellpadding="0" cellspacing="2" border="0" width="100%"
	class="ticketoptions">
	<tr>
		<td>
			<form name=action action='tickets.php?id=<?=$id?>' method=post
				class="inline">

				<?php 
           $sql = "SELECT defaultAction FROM " .CONFIG_TABLE;
           $result = mysql_query($sql) or die (mysql_error());
           while($row = mysql_fetch_array($result)){

           if($row['defaultAction']=='changePriority'){ $prioritySelected='selected'; }else{ $prioritySelected='BLARG';  }
           if($row['defaultAction']=='markOverdue'){ $overdueSelected='selected'; }else{ $overdueSelected='';  }
           if($row['defaultAction']=='closeTicket'){ $closeSelected='selected'; }else{ $closeSelected='';  }
           if($row['defaultAction']=='banDelete'){ $banSelected='selected'; }else{ $banSelected='';  }
           if($row['defaultAction']=='delete'){ $deleteSelected='selected'; }else{ $deleteSelected='';  }
	   if($row['defaultAction']=='release'){ $releaseSelected='selected'; }else{ $releaseSelected='';  }


           }
        ?>

				<input type='hidden' name='ticket_id' value="<?=$id?>" /> <input
								
					type='hidden' name='a' value="process" /> <span for="do"> &nbsp;<b><?php print translate("ACTION");?>:</b>
				</span> <select id="do" name="do"
					onChange="this.form.ticket_priority.disabled=strcmp(this.options[this.selectedIndex].value,'change_priority','reopen','overdue')?false:true;">
					<option value=""><?php print translate("SELECT_ONE");?></option>
					<option value="change_priority" <?php echo $prioritySelected;?>
					<?=$info['do']=='change_priority'?'selected':''?>><?php print translate("CHANGE_PRIORITY");?></option>
					<?if(!$ticket->isoverdue()){ ?>
					<option value="overdue" <?php echo $overdueSelected;?> <?=$info['do']=='overdue'?'selected':''?>><?php print translate("MARK_OVERDUE");?></option>
						<?}?>
						<?if($ticket->isAssigned()){ ?>
					<option value="release" <?php echo $releaseSelected;?> <?=$info['do']=='release'?'selected':''?>><?php print translate("RELEASE_UNASSIGN");?></option>
						<?}?>

						<?if($thisuser->canCloseTickets()){
							//if you can close a ticket...reopening it is given.
							if(($ticket->isOpen())||($ticket->isPending())){?>
					<option value="close" <?php echo $closeSelected; ?> <?=$info['do']=='close'?'selected':''?>><?php print translate("CLOSE_TICKET");?></option>
						<?}else{?>
					<option value="reopen" <?php echo $closeSelected; ?> <?=$info['do']=='reopen'?'selected':''?>><?php print translate("LABEL_REOPEN_TICKET");?></option>
						<?}
						}?>
						<?php
						if($thisuser->canManageBanList()) {
							if(!$emailBanned) {?>
					<option value="banemail" <?php echo $banSelected; ?> >
						<?php print translate("BAN");?>
						<?=$ticket->isOpen()?'&amp; '.translate("LABEL_CLOSE"):''?>
					</option>
					<?}else{?>
					<option value="unbanemail" <?php echo $banSelected; ?> ><?php print translate("UNBAN");?></option>
					<?}
						}?>

						<?if($thisuser->canDeleteTickets()){ //oooh...fear the deleters! ?>
					<option value="delete" <?php echo $deleteSelected; ?> ><?php print translate("LABEL_DELETE");?> <?php print translate("LABEL_TICKET");?></option>
					<?}?>
				</select> <span for="ticket_priority">Priority:</span> <select
					id="ticket_priority" name="ticket_priority" 
					<?php if((!$info['do']) && ($prioritySelected!='selected')){ echo 'disabled'; } ?>
					<option value="0" selected="selected">-Unchanged-</option>
					<?
					$priorityId=$ticket->getPriorityId();
					$resp=db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
					while($row=db_fetch_array($resp)){ ?>
					<option value="<?=$row['priority_id']?>"
					<?=$priorityId==$row['priority_id']?'disabled':''?>>
					<?php print eval('?>' .$row['priority_desc']. '<?php ');?>
					</option>
					<?}?>
				</select> &nbsp;&nbsp; <input class="button" type="submit"
					value='<?php print translate("LABEL_GO");?>'>
			</form>
	
	</tr>
	</td>
</table>
					<?}?>
<br>
					<?
					//Internal Notes
					$sql ='SELECT note_id,title,note,source,created FROM '.TICKET_NOTE_TABLE.' WHERE ticket_id='.db_input($id).' ORDER BY created DESC';
					if(($resp=db_query($sql)) && ($notes=db_num_rows($resp))){
						$display=($notes>5)?'none':'block'; //Collapse internal notes if more than 5.
						?>
<div align="left">
	<a class="Icon note" href="#"
		onClick="toggleLayer('ticketnotes'); return false;"><?php print translate("INTERNAL_NOTES");?> (<?=$notes?>)</a><br>
	<br>
	<div id='ticketnotes' style="display:<?=$display?>;text-align:center;">
	<?
	while($row=db_fetch_array($resp)) {?>
		<table align="center" class="note" cellspacing="0" cellpadding="1"
			width="100%" border=0>
			<tr>
				<th><?=Format::db_daydatetime($row['created'])?>&nbsp;-&nbsp; <?php print translate("POSTED_BY");?>  <?=$row['source']?></th>
			</tr>
			<? if($row['title']) {?>
			<tr class="header">
				<td><?=Format::display($row['title'])?></td>
			</tr>
			<?} ?>
			<tr>
				<td><?=Format::display($row['note'])?></td>
			</tr>
			
            		<tr class="header">
              		<td><?php echo $ticket->getAttachmentStr($row['note_id'],'N')?></td>
            		</tr>
		</table>
		<?} ?>
	</div>
</div>
		<?} ?>
<div align="left">
	<a class="Icon thread" href="#"
		onClick="toggleLayer('ticketthread'); return false;"><?php print translate("TEXT_TICKET_THREAD");?></a>
	<div id="ticketthread">
	<?
	//get messages
	$sql='SELECT msg.msg_id,msg.created,msg.message,count(attach_id) as attachments  FROM '.TICKET_MESSAGE_TABLE.' msg '.
            ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE." attach ON  msg.ticket_id=attach.ticket_id AND msg.msg_id=attach.ref_id AND ref_type='M' ".
            ' WHERE  msg.ticket_id='.db_input($id).
            ' GROUP BY msg.msg_id ORDER BY created'; 
	$msgres =db_query($sql);
	while ($msg_row = db_fetch_array($msgres)) {
		?>
		<table align="center" class="message" cellspacing="0" cellpadding="1"
			width="100%" border=0>
			<tr>
				<th><?=Format::db_daydatetime($msg_row['created'])?></th>
			</tr>
			<?if($msg_row['attachments']>0){ ?>
			<tr class="header">
				<td><?=$ticket->getAttachmentStr($msg_row['msg_id'],'M')?></td>
			</tr>
			<?}?>
			<tr>
				<td><?=Format::display($msg_row['message'])?>&nbsp;</td>
			</tr>
		</table>
		<?
		//get answers for messages
		$sql='SELECT resp.*,count(attach_id) as attachments FROM '.TICKET_RESPONSE_TABLE.' resp '.
                ' LEFT JOIN '.TICKET_ATTACHMENT_TABLE." attach ON  resp.ticket_id=attach.ticket_id AND resp.response_id=attach.ref_id AND ref_type='R' ".
                ' WHERE msg_id='.db_input($msg_row['msg_id']).' AND resp.ticket_id='.db_input($id).
                ' GROUP BY resp.response_id ORDER BY created';
		$resp =db_query($sql);
		while ($resp_row = db_fetch_array($resp)) {
			$respID=$resp_row['response_id'];
			?>
		<table align="center" class="response" cellspacing="0" cellpadding="1"
			width="100%" border=0>
			<tr>
				<th><?=Format::db_daydatetime($resp_row['created'])?>&nbsp;-&nbsp;<?=$resp_row['staff_name']?>
				</th>
			</tr>
			<?if($resp_row['attachments']>0){ ?>
			<tr class="header">
				<td><?=$ticket->getAttachmentStr($respID,'R')?></td>
			</tr>
			<?}?>
			<tr>
				<td><?=Format::display($resp_row['response'])?></td>
			</tr>
		</table>
		<?}
		$msgid =$msg_row['msg_id'];
	}?>
	</div>
</div>
<table align="center" cellspacing="0" cellpadding="3" width="90%"
	border=0>
	<?if($_POST['a']!='process') {?>
	<tr>
		<td align="center"><?if($errors['err']) {?>
			<p align="center" id="errormessage">
			<?=$errors['err']?>
			</p> <?}elseif($msg) {?>
			<p align="center" id="infomessage">
			<?=$msg?>
			</p> <?}?>
		</td>
	</tr>
	<?}?>
	<tr>
		<td align="center">
			<div class="tabber">
				<div id="reply" class="tabbertab" align="left">
					<h2>
					<?php print translate('TEXT_POST_REPLY')?>
					</h2>
					<p>
					
					
					<form action="tickets.php?id=<?=$id?>#reply" name="reply"
						id="replyform" method="post" enctype="multipart/form-data">
						<input type="hidden" name="ticket_id" value="<?=$id?>"> <input
							type="hidden" name="msg_id" value="<?=$msgid?>"> <input
							type="hidden" name="a" value="reply">
						<div>
							<font class="error">&nbsp;<?=$errors['response']?> </font>
						</div>
						<div>
						<?
						$sql='SELECT premade_id,title FROM '.KB_PREMADE_TABLE.' WHERE isenabled=1 '.
                                ' AND (dept_id=0 OR dept_id='.db_input($ticket->getDeptId()).')';
						$canned=db_query($sql);
						if($canned && db_num_rows($canned)) {
							?>
							<?php print translate('LABEL_CANNED_RESPONSE')?>
							:&nbsp; <select id="canned" name="canned"
								onChange="getCannedResponse(this.options[this.selectedIndex].value,this.form,'response');this.selectedIndex='0';">
								<option value="0" selected="selected">
								<?php print translate('TEXT_SELECT_PREMADE_REPLY')?>
								</option>
								<?while(list($cannedId,$title)=db_fetch_row($canned)) { ?>
								<option value="<?=$cannedId?>">
								<?=Format::htmlchars($title)?>
								</option>
								<?}?>
							</select>&nbsp;&nbsp;&nbsp;<label><input type='checkbox'
								value='1' name=append checked="true" /> <?php print translate('LABEL_APPEND')?>
							</label>
							<?}?>
							<textarea name="response" id="response" cols="90" rows="9"
								wrap="soft" style="width: 90%"><?=$info['response']?></textarea>
						</div>
						<?php if($cfg->canUploadFiles()){ //TODO: may be allow anyways and simply email out attachment?? ?>
						<div style="margin-top: 3px;">
							<label for="attachment"><?php print translate("TEXT_ATTACH_FILE");?>:</label> <input type="file"
								name="attachment" id="attachment" size=30px
								value="<?=$info['attachment']?>" /> <font class="error">&nbsp;<?=$errors['attachment']?>
							</font>
						</div>
						<?php }?>
						<?
						$appendStaffSig=$thisuser->appendMySignature();
						$appendDeptSig=$dept->canAppendSignature();
						$info['signature']=!$info['signature']?'none':$info['signature']; //change 'none' to 'mine' to default to staff signature.
						if($appendStaffSig || $appendDeptSig) { ?>
						<div style="margin-top: 10px;">
							<label for="signature" nowrap><?php print translate('LABEL_APPEND_SIGNATURE')?>:</label>
							<label><input type="radio" name="signature" value="none" checked>
							<?php print translate('LABEL_NONE')?> </label>
							<?if($appendStaffSig) {?>
							<label> <input type="radio" name="signature" value="mine"
							<?=$info['signature']=='mine'?'checked':''?>> <?php print translate('LABEL_MY_SIGNATURE')?>
							</label>
							<?}?>
							<?if($appendDeptSig) {?>
							<label><input type="radio" name="signature" value="dept"
							<?=$info['signature']=='dept'?'checked':''?>> <?php print translate('LABEL_DEPT_SIGNATURE')?>
							</label>
							<?}?>
						</div>
						<?}?>
						<div style="margin-top: 3px;">
							<b><?php print translate('TEXT_TICKET_STATUS')?>:</b>
							<?
							if($ticket->isPending())
{
	?>
	
	<?php
}else{
	$checked=isset($info['ticket_status'])?'checked':''; //Staff must explicitly check the box to change status..
	if($ticket->isOpen()){?>
	<label><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Close" <?=$checked?> > <?php print translate("CLOSE_ON_REPLY");?>
	<?}else{ ?>
	<label><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Reopen" <?=$checked?> > Reopen on Reply
	<?
	}
}
$checked=isset($info['pending_status'])?'checked':''; //Staff must explicitly check the box to change status..
if(!$ticket->isClosed())
{
	if($ticket->isPending()){
	?>
        <label><input type="checkbox" name="ticket_status" id="l_ticket_status" value="Close" <?=$checked?> > Close on Reply</label>
	<label><input type="checkbox" name="pending_status" id="l_pending_status" value="Pending" checked="checked" > Ticket is pending (Untick to mark not pending)</label>
	<?}else{ ?>
	<label><input type="checkbox" name="pending_status" id="l_pending_status" value="Pending" <?=$checked?> > <?php print translate("MARK_PENDING");?></label>
	<?
	}
}
							?>
						</div>
						<p>
						
						
						<div style="margin-left: 50px; margin-top: 30px; margin-bottom: 10px; border: 0px;">
							<input class="button" type='submit'
								value='<?= translate('LABEL_POST_REPLY')?>' /> <input
								class="button" type='reset'
								value='<?= translate('LABEL_RESET')?>' /> <input
								class="button" type='button'
								value='<?= translate('LABEL_CANCEL')?>'
								onClick="history.go(-1)" />
						</div>
						</p>
					</form>
					</p>
				</div>
				<div id="notes" class="tabbertab" align="left">
					<h2>
					<?php print translate('LABEL_POST_INTERNAL_NOTE')?>
					</h2>
					<p>
					
					
					<form action="tickets.php?id=<?=$id?>#notes" name="notes"
						class="inline" method="post" enctype="multipart/form-data">
						<input type="hidden" name="ticket_id" value="<?=$id?>"> <input
							type="hidden" name="a" value="postnote">
						<div>
							<label for="title"><?php print translate('LABEL_NOTE_TITLE')?>:</label> <input
								type="text" name="title" id="title" value="<?=$info['title']?>"
								size=30px /> </select><font class="error">*&nbsp;<?=$errors['title']?>
							</font>
						</div>
						<div style="margin-top: 3px;">
							<label for="note" valign="top"><?php print translate('LABEL_ENTER_NOTE_CONTENT')?>
								<font class="error">*&nbsp;<?=$errors['note']?> </font> </label><br />
							<textarea name="note" id="note" cols="80" rows="7" wrap="soft"
								style="width: 90%"><?=$info['note']?></textarea>
						</div>
						<?php if($cfg->canUploadFiles()){ //TODO: may be allow anyways and simply email out attachment?? ?>
                        			<div style="margin-top: 3px;">
                            			<label for="attachment" ><?php print translate("TEXT_ATTACH_FILE");?>:</label>
                            			<input type="file" name="attachment" id="attachment2" size=30px value="<?php echo $info['attachment']; ?>" /> 
                                		<font class="error">&nbsp;<?php echo $errors['attachment']; ?></font>
                        			</div>
                        			<?php }?>

						<?
						//When the ticket is assigned Allow assignee, admin or ANY dept manager to close it
						if(!$ticket->isAssigned() || $thisuser->isadmin()  || $thisuser->isManager() || $thisuser->getId()==$ticket->getStaffId()) {
							?>
						<div style="margin-top: 3px;">
							<b><?php print translate('LABEL_TICKET_STATUS')?>:</b>
							<?
							$checked=($info && isset($info['ticket_status']))?'checked':''; //not selected by default.
							if($ticket->isOpen()){?>
							<label><input type="checkbox" name="ticket_status"
								id="ticket_status" value="Close" <?=$checked?>> <?php print translate('LABEL_CLOSE_TICKET')?>
							</label>
							<?}else{ ?>
							<label><input type="checkbox" name="ticket_status"
								id="ticket_status" value="Reopen" <?=$checked?>> <?php print translate('LABEL_REOPEN_TICKET')?>
							</label>
							<?}?>
						</div>
						<?}?>
						<p>
						
						
						<div align="left"
							style="margin-left: 50px; margin-top: 10px; margin-bottom: 10px; border: 0px;">
							<input class="button" type='submit'
								value='<?= translate('LABEL_SUBMIT')?>' /> <input
								class="button" type='reset'
								value='<?= translate('LABEL_RESET')?>' /> <input
								class="button" type='button'
								value='<?= translate('LABEL_CANCEL')?>'
								onClick="history.go(-1)" />
						</div>
						</p>
					</form>
					</p>
				</div>
				<?
				if($thisuser->canTransferTickets()) {
					?>
				<div id="transfer" class="tabbertab" align="left">
					<h2>
					<?php print translate('TEXT_DEPT_TRANSFER')?>
					</h2>
					<p>
					
					
					<form action="tickets.php?id=<?=$id?>#transfer" name="notes"
						method="post" enctype="multipart/form-data">
						<input type="hidden" name="ticket_id" value="<?=$id?>"> <input
							type="hidden" name="a" value="transfer">
						<div>
							<span for="dept_id"><?php print translate('LABEL_DEPARTMENT')?>:</span> <select
								id="dept_id" name="dept_id">
								<option value="" selected="selected">
								<?php print translate('TEXT_SELECT_TARGET_DEPT')?>
								</option>
								<?
								$depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.' WHERE dept_id!='.db_input($ticket->getDeptId()));
								while (list($deptId,$deptName) = db_fetch_row($depts)){
									$selected = ($info['dept_id']==$deptId)?'selected':''; ?>
								<option value="<?=$deptId?>" <?=$selected?>>
								<?=$deptName?>
									Department
								</option>
								<?
								}?>
							</select><font class='error'>&nbsp;*<?=$errors['dept_id']?> </font>
						</div>
						<div>
							<span><?php print translate('TEXT_COMMENTS_REASONS_TRANSFER')?> <font
								class='error'>&nbsp;*<?=$errors['message']?> </font> </span>
							<textarea name="message" id="message" cols="80" rows="7"
								wrap="soft" style="width: 90%;"><?=$info['message']?></textarea>
						</div>
						<p>
						
						
						<div
							style="margin-left: 50px; margin-top: 5px; margin-bottom: 10px; border: 0px;"
							align="left">
							<input class="button" type='submit'
								value='<?= translate('LABEL_TRANSFER')?>' /> <input
								class="button" type='reset'
								value='<?= translate('LABEL_RESET')?>' /> <input
								class="button" type='button'
								value='<?= translate('LABEL_CANCEL')?>'
								onClick="history.go(-1)" />
						</div>
						</p>
					</form>
					</p>
				</div>
				<?}?>
				<?
				//When the ticket is assigned Allow assignee, admin or ANY dept manager to reassign the ticket.
				if(!$ticket->isAssigned() || $thisuser->isadmin()  || $thisuser->isManager() || $thisuser->getId()==$ticket->getStaffId()) {
					?>
				<div id="assign" class="tabbertab" align="left">

					<h2>
					<?php print $staff?translate('LABEL_RE_ASSIGN_TICKET'):translate('LABEL_ASSIGN_TO_STAFF')?>
					</h2>
					<p>
					
					
					<form action="tickets.php?id=<?=$id?>#assign" name="notes"
						method="post" enctype="multipart/form-data">
						<input type="hidden" name="ticket_id" value="<?=$id?>"> <input
							type="hidden" name="a" value="assign">
						<div>
							<span for="staffId"><?php print translate('LABEL_STAFF_MEMBER')?>:</span>
							<select id="staffId" name="staffId">
								<option value="0" selected="selected">
								<?php print translate('TEXT_SELECT_STAFF_MEMBER')?>
								</option>
								<?
								//TODO: make sure the user's group is also active....DO a join.
								$sql=' SELECT staff_id,CONCAT_WS(", ",lastname,firstname) as name FROM '.STAFF_TABLE.
                                     ' WHERE isactive=1 AND onvacation=0 ';
								if($ticket->isAssigned())
								$sql.=' AND staff_id!='.db_input($ticket->getStaffId());
								$depts= db_query($sql.' ORDER BY lastname,firstname ');
								while (list($staffId,$staffName) = db_fetch_row($depts)){

									$selected = ($info['staffId']==$staffId)?'selected':''; ?>
								<option value="<?=$staffId?>" <?=$selected?>>
								<?=$staffName?>
								</option>
								<?
								}?>
							</select><font class='error'>&nbsp;*<?=$errors['staffId']?> </font>
						</div>
						<div>
							<span><?php print translate("ASSIGN_STAFF_NOTE");?><font class='error'>&nbsp;*<?=$errors['assign_message']?>
							</font> </span><br />
							<textarea name="assign_message" id="assign_message" cols="80"
								rows="7" wrap="soft" style="width: 90%;"><?=$info['assign_message']?></textarea>
						</div>
						<p>
						
						
						<div
							style="margin-left: 50px; margin-top: 5px; margin-bottom: 10px; border: 0px;"
							align="left">
							<input class="button" type='submit'
								value='<?= translate('LABEL_ASSIGN')?>' /> <input
								class="button" type='reset'
								value='<?= translate('LABEL_RESET')?>' /> <input
								class="button" type='button'
								value='<?= translate('LABEL_CANCEL')?>'
								onClick="history.go(-1)" />
						</div>
						</p>
					</form>
					</p>
				</div>
				<?}?>
				<?
	     // Merge Tickets MOD
             //Admin and Manager function - merge tickets.
            if( ($thisuser->isadmin() || $thisuser->isManager() ) &&  ($ticket->getStatus()!='closed') ) {  
                 ?>
            <div id="mergeticket" class="tabbertab"  align="left">
 
                <h2><?php print translate("MERGE_TICKET");?></h2>
 
		   <?
				   $sql = 'SELECT ticket_id, concat(ticketid,": ",subject) AS label FROM '.TICKET_TABLE.' WHERE email='.
                                  '(SELECT email FROM ' .TICKET_TABLE.' WHERE ticket_id = '.$id.') AND status '.
                                  '= "open" AND ticket_id <> '.$id.' ORDER BY `created` DESC ';
				     	       $lookuptickets = db_query($sql);
					       		      	   if (db_num_rows($lookuptickets)) {
													?>
                <p>
                    <form action="tickets.php?id=<?=$id?>#mergeticket" name="notes" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="ticket_id" value="<?=$id?>">
                        <input type="hidden" name="a" value="mergeticket">
                        <input type="hidden" name="email" value="<?php echo $ticket->getEmail(); ?>">
                        <input type="hidden" name="status" value="<?php echo $ticket->getStatus(); ?>">
                           <?php print translate("MERGE_WITH_TICKET");?> # <select id="keepticket" name="keepticket">
			   	      	      	     	     <?php
									   while (list($ticket_id,$label) = db_fetch_row($lookuptickets)){
                           ?>                                    
                             <option value="<?=$ticket_id?>"><?=$label?></option>
                           <?
                           }?>
                           </select>
                           <div><input type="checkbox" value="1" name="notifycustomer" checked="true" /><?php print translate("INFORM_CUSTOMER_TICKET_MERGE");?></div>
                           <div  style="margin-left: 50px; margin-top: 5px; margin-bottom: 10px;border: 0px;" align="left">
                              <input class="button" type='submit' value='<?php print translate("MERGE_TICKET");?>'/>
                              <!-- I don't think we need this (ScottRo) <input class="button" type='reset' value='Reset' />
                              <input class="button" type='button' value='Cancel' onClick="history.go(-1)" />-->
                           </div>
						<?php } else {
						      	             echo "Sorry, this ticket cannot be merged into another since there is no ticket that has the same email address with this.";
								     	  	             }
														?>	    
                        </p>
                    </form>
                </p>
            </div>
            <?}?>
			</div>
		</td>
	</tr>
</table>
<div style="margin-left: 50px; margin-top: 5px; margin-bottom: 10px;border: 0px;" align="left">
  <a href="tickets.php"><?php print translate("LABEL_TICKETS");?></a> | <a href="#"><?php print translate("TOP");?></a><a name='bottom'></a>
  </div>
