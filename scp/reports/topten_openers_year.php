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

$year = date(Y);

?>

<html>
<head>
<title>HHI Support Report Top Ten Ticket openers for <? echo $year ?></title>

<link rel="stylesheet" type="text/css" href="css/admin.css">

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

$query = "select `email`,`name`,COUNT(`ticket_id`) as `counted` FROM `ost_ticket` WHERE YEAR(created)='$year' GROUP by `email` ORDER BY `counted` DESC LIMIT 0,10;";
$result=mysql_query($query);
$num = mysql_numrows($result);

$values = array();
while($row = mysql_fetch_assoc($result)) {	
					$values[$row['type']] = $row['counted'];
					}

$runningtot = array_sum($values);

echo "<br><br>";
?>

<b>Top Ten Ticket Openers of <? echo $year ?></b><br>

<? 
 $i=0;
 while ($i < $num) {
  $email = mysql_result($result,$i,"email");
  $name = mysql_result($result,$i,"name");
  $counted = mysql_result($result,$i,"counted");
  $actnum = $i + 1;
	
	print "<b>".$actnum."</b> &nbsp; $name ($counted) <br>";
	
	++$i;
}
?>	
	
<br>
<a href="javascript:toggleLayer('EmpForm');" title="See Data: Locations">See Raw Data: Top Ten Ticket Openers for <? echo $year ?></a><br>
<div id="EmpForm">

<?

$i=0;
while ($i < $num) {
 $email = mysql_result($result,$i,"email");
 $name = mysql_result($result,$i,"name");
 $counted = mysql_result($result,$i,"counted");
 
 $actnum = $i + 1;
 
 if($debug=='0') {  
  print("$actnum - $email, $counted, $name<br>");
 }
 
 ++$i;
}

 ?>
</div>

<!-- END TEST -->

<br><br><br> 
 
<!-- end div id="main" -->
</div>

<div style="position:relative" id="footer">
&copy; 2007-<? echo date(Y) ?> Harbor Homes, Inc.
</div>



</body>
</html>


