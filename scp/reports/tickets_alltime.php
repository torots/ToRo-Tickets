<?php

// query database for ALL tickets created and closed values.
$query = "select COUNT(ticket_id) as counted FROM ".TICKET_TABLE." WHERE YEAR(`created`) is NOT NULL;";
$result=mysql_query($query)or die(mysql_error());
$num = mysql_numrows($result);

//$values = array();
$vals = array();

$i=0;
while ($i < $num) {
 $counted = mysql_result($result,$i,"counted");
 
$debug=0;

 // if debug is 1 print extra info
 if($debug==1) {
  echo "Opened: ".$created."($time1) Closed: ".$closed."($time2) --- <br>";
 }

	++$i;
}

 echo "<b>" .translate("TEXT_ALL_TIME"). ":</b> ".$counted." <br>";

?>
