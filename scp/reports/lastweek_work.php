<?php

// base includes
// config file
include('./config.php');
// custom functions
include($_CONF['inc'] . 'functions.php');
// db connection script
require_once ($_CONF['inc'] . 'dbconnect.php');

// debug mode - extra display stuff
if($debug=='1') { 
 echo "OLOC: ".$_POST["corp_name"]."<br>OYEAR: ".$_POST["form_corp_year"]."<br>";
 echo "LOC: $corp_name<br>YEAR: $report_year";
 }

$staffname = $_GET['staff'];
 
?>

<html>
<head>
<title>HHI Support: Last Week tickets by site for <? echo $staffname; ?></title>

<link rel="stylesheet" type="text/css" href="css/admin.css">
<!-- echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/db_resultx.css\">"; -->
<script>
function toggleLayer( whichLayer )
{
  var elem, vis;
  if( document.getElementById ) // this is the way the standards work
    elem = document.getElementById( whichLayer );
  else if( document.all ) // this is the way old msie versions work
      elem = document.all[whichLayer];
  else if( document.layers ) // this is the way nn4 works
    elem = document.layers[whichLayer];
  vis = elem.style;
  // if the style.display value is blank we try to figure it out here
  if(vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)
    vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';
  vis.display = (vis.display==''||vis.display=='block')?'none':'block';
}
</script>

</head>

<body>

<div id="head">
 <!-- header container -->
 <div id="header">
  <map name=MicrosoftOfficeMap0><area shape=Rect coords="25, 132, 90, 154" 
	href="http://weblibrary.harborhomes.org"></map>
	<img border=0 width=750 height=183 id="_x0000_i1025" 
	src="weblibrarysecondarypageheader.png" usemap="#MicrosoftOfficeMap0">
 
  <div id="topsearch">
   <form method='get' action='/search'>
     <input type=text name=searchtext width=50 class=form>
     <input type=submit name=whattodo value='Search' class=form>
   </form>
  </div>
 
 </div>
 <!-- close header container -->
</div>

<div id="main">
<!-- main container -->


<? 
$sunday = date(('Y-m-d H:i:s'), strtotime('last sunday 09:00'));

$query = "select DISTINCT(ost_ticket.ticket_id) as ticket,ost_ticket.agency as agency,ost_ticket.closed as closed,ost_ticket_response.ticket_id from ost_ticket,ost_ticket_response WHERE (ost_ticket_response.staff_name = '$staffname' AND ost_ticket.ticket_id = ost_ticket_response.ticket_id) AND (ost_ticket.created >= '$sunday' OR ost_ticket_response.created >= '$sunday' OR ost_ticket.updated >= '$sunday' OR ost_ticket.closed >= '$sunday') ORDER BY ost_ticket.closed ASC;";
$result=mysql_query($query);
$num = mysql_numrows($result);


$values = array();
while($row = mysql_fetch_assoc($result)) {	
					$values[$row['ticket']] = $row['agency'];
					}

					
					
$runningtot = array_sum($values);

// include code to make type_year.png
//include('./report_corp_year_lookup.php');

echo "<br><br>";
?>

<b>Tickets <? echo $staffname; ?> worked on:</b><br>
(note: tickets are from oldest to newest based on created date)<br><br>
<b>#</b> &nbsp; <b>site name [date closed]</b><br>
<!--<img src="./reports/corp_<? echo $report_year; ?>.png" alt="Incident Corp Graph"><br>-->
<? 
 $i=0;
 while ($i < $num) {
  $agency = mysql_result($result,$i,"agency");
  $ticket = mysql_result($result,$i,"ticket");
	$closed = mysql_result($result,$i,"closed");
  $actnum = $i + 1;
	
  $getsite = "select agency from ost_agencies where id='$agency'";	
	$siteresult = mysql_query($getsite);
	$site = mysql_result($siteresult,0,"agency");
	
	print "<b>".$actnum."</b> &nbsp; $site [$closed] <br>";
	
	++$i;
}
?>	
	
<!-- END TEST -->

<br><br><br> 
 
<!-- end div id="main" -->
</div>

<div style="position:relative" id="footer">
&copy; 2007-<? echo date(Y) ?> Harbor Homes, Inc.
</div>



</body>
</html>


