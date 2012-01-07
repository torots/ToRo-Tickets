<?php

$year = date(Y);

// query database for ALL tickets created and closed values.
$query = "select COUNT(ticket_id) as counted FROM ".TICKET_TABLE." WHERE YEAR(`created`)='$year';";
$result=mysql_query($query);
$num = mysql_numrows($result);

//$values = array();
$vals = array();

$i=0;
while ($i < $num) {
 $counted = mysql_result($result,$i,"counted");
 
 // if debug is 1 print extra info
 if(debug==1) {
  echo "Opened: ".$created."($time1) Closed: ".$closed."($time2) --- <br>";
 }

	++$i;
}

 echo "<b>$year:</b> ".$counted." <br>";

?>
