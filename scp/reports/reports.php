<?php

// Base includes [config file, functions, and DB connection]
include('./config.php');
include($_CONF['inc'] . 'functions.php');
require_once ($_CONF['inc'] . 'dbconnect.php');

?>


<html>
<head>
<title>HHI Support Site Data Page</title>

<link rel="stylesheet" type="text/css" href="css/reports.css">
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

<div id="sidebar">
<div id="sidetitle"><font color=white>Top 10 menu</font></div>
<div id="sidemenu">
<?
echo "<a href=topten_openers_year.php>ticket openers ".date('Y')."</a><br><br>";
echo "<a href=topten_openers.php>ticket openers<br>(all time)</a><br><br>";
echo "<a href=topten_sites_year.php>ticket sites ".date('Y')."</a><br><br>";
echo "<a href=topten_sites.php>ticket sites<br>(all time)</a><br><br>";
//echo "<a href=toptensites.php>top10 ticket sites</a><br><br>";

?>
</div>
</div>

<div id="main">
<!-- main container -->


<a href="http://support.harborhomes.org">support.harborhomes.org</a> is used to open, update, and track support needs<br>
across all HHI and Afilliate sites.  Here is some information regarding the site:<br><ul>
Average time tickets remained open:<br><ul>
<?
 $thisyear = include('timecalc_currentyear.php');
 $lastyear = include('timecalc_lastyear.php');
 $alltime = include('timecalc_alltime.php');
?>
</ul><br>
Avg Response Time:<br><ul>
<?
 $avgrespontime = include('avgrespontime_currentyear.php');
 $avgrespontime = include('avgrespontime_lastyear.php');
 $avgrespontime = include('avgrespontime_alltime.php');
?>
</ul><br>
# of Tickets opened:<br><ul>
<?
 $ticketsthisyear = include('tickets_thisyear.php');
 $ticketslastyear = include('tickets_lastyear.php');
 $ticketslastyear = include('tickets_alltime.php');
?>
</ul><br>

</ul>

<p style="position: relative;z-index:-1;left:-100px;">This graph breaks down tickets opened by month/year and displays the past 18 months:
<? include('tickets_monthly.php'); ?>
<br><img src="http://support.harborhomes.org/scp/reports/reports/tickets_monthly.png" alt="TeSt" style="position: relative;z-index:-1;left:-100px;">
</div> 
 
 

<br><br><br> 
 
<!-- end div id="main" -->
</div>

<div style="position:relative" id="footer">
&copy; 2007-<? echo date(Y) ?> Harbor Homes, Inc.
</div>



</body>
</html>
