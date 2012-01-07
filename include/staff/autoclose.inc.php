<?php
if(defined('TOROPVTLTD') && is_object($user))
{
	include_once(INCLUDE_DIR.'class.ticket.php');

	// select all tickets marked as 'pending' where updated is older than 5 days ago
	$sql  = "SELECT ticket_id FROM " . TICKET_TABLE;
	$sql .= " WHERE status = 'pending'";
	$sql .= " AND updated <= '" . date("Y-m-d", strtotime('-5 days')) . " 00:00:00'";

	$old_pending_tickets = db_query($sql) or die("Query Error: " . mysql_error()); 


	// for each record, mark as closed
	while ($pendingTicket = mysql_fetch_array($old_pending_tickets))
	{
		$ticket = new Ticket($pendingTicket['ticket_id']);
		$ticket->close();
	}
}
?> 
