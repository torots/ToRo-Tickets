<?php

$year = date('Y');

// MySQL query from HELL!
//$query = "
//SELECT 
//AVG(
//FLOOR(DATEDIFF(
//(SELECT sec.created 
// FROM toro_ticket_response as sec 
// WHERE main.ticket_id = sec.ticket_id AND YEAR(main.created)='".$year."'
// GROUP BY sec.ticket_id)
//,main.created)/7)*5 +
//DATEDIFF(
//(SELECT sec.created 
// FROM toro_ticket_response as sec 
// WHERE main.ticket_id = sec.ticket_id AND YEAR(main.created)='".$year."'
// GROUP BY sec.ticket_id)
//,main.created)%7 -
//IF( DAYOFWEEK(main.created) = 1, IF(DAYOFWEEK(
//(SELECT sec.created 
// FROM toro_ticket_response as sec 
// WHERE main.ticket_id = sec.ticket_id AND YEAR(main.created)='".$year."'
// GROUP BY sec.ticket_id)
//) = 7, 2, 1),
//  IF( DAYOFWEEK(main.created) = 7, IF(DAYOFWEEK(
//(SELECT sec.created 
// FROM toro_ticket_response as sec 
// WHERE main.ticket_id = sec.ticket_id AND YEAR(main.created)='".$year."'
// GROUP BY sec.ticket_id)
//) = 1, 1, 2),
//    IF( DAYOFWEEK(
//(SELECT sec.created 
// FROM toro_ticket_response as sec 
// WHERE main.ticket_id = sec.ticket_id AND YEAR(main.created)='".$year."'
// GROUP BY sec.ticket_id)
//) < DAYOFWEEK(main.created), 2, 0 )))
//) as fubar
//FROM toro_ticket as main";

//$result=mysql_query($query) or die( "Erro: Query Failed");;
//$fu = mysql_result($result,0,"fubar");

// debug section
if($debug = 0) {
 echo "<B>Total Avg Time in Days:</b> $fu <br>";
}

// convert days to seconds (*24 hours *60 minutes *60seconds)
$timeInSeconds = $fu * 24 * 60 * 60;
 
echo "<b>".$year.":</b> ".duration($timeInSeconds)."<br>";

?>
