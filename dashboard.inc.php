<?php
/*
 
 toro Ticketing System
 

*/

if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(translate("TEXT_ACCESS_DENIED"));

require_once('functions.php');
?>
<br>
<table>
 <tr>
  <td colspan="3">

  </td>
 </tr>
 <tr>
  <td valign=top>
   Avg Open Time:<br><ul>
   <?
    $thisyear = include('./reports/timecalc_currentyear.php');
    //$lastyear = include('./reports/timecalc_lastyear.php');
    $alltime = include('./reports/timecalc_alltime.php');
   ?>
   </ul><br>
   Avg Response Time:<br><ul>
    <?
     $avgrespontime = include('./reports/avgrespontime_currentyear.php');
     //$avgrespontime = include('./reports/avgrespontime_lastyear.php');
     $avgrespontime = include('./reports/avgrespontime_alltime.php');
    ?>
   </ul><br>
   # of Tickets opened:<br><ul>
   <?
    $ticketsthisyear = include('./reports/tickets_thisyear.php');
    //$ticketslastyear = include('./reports/tickets_lastyear.php');
    $ticketslastyear = include('./reports/tickets_alltime.php');
   ?>
  </td>
  <td width=10>
   &nbsp;
  </td>
  <td width=450 valign=top>

   <fieldset style="border: 1px solid #000000;">
    <legend>Client Message of the Day</legend>
     <?php 
      //$query = "SELECT motd,motd_lastupdated FROM toro_config";
      $query = "SELECT client_motd,client_motd_lastupdated FROM ".CONFIG_TABLE.";";
      $result = mysql_query($query) or die( "Error: Query Failed");

      $client_motd = mysql_result($result,0,"client_motd");
      $client_motd_lastupdated = mysql_result($result,0,"client_motd_lastupdated");

      echo $client_motd;
     ?>
   </fieldset>
   <br>
   <fieldset style="border: 1px solid #000000;">
    <legend>Staff Message of the Day</legend>
     <?php
      //$query = "SELECT motd,motd_lastupdated FROM toro_config";
      $query = "SELECT staff_motd,staff_motd_lastupdated FROM ".CONFIG_TABLE.";";
      $result = mysql_query($query) or die( "Error: Query Failed");

      $staff_motd = mysql_result($result,0,"staff_motd");
      $staff_motd_lastupdated = mysql_result($result,0,"staff_motd_lastupdated");

      echo $staff_motd;
     ?>
   </fieldset>
   <br>
   <!--<fieldset style="width:320px;height:90px;border: 1px solid #000000;">-->
   <fieldset style="border: 1px solid #000000;">
    <legend>Last 5 Log Entries:</legend>
     <table cellspacing=0 cellpadding=0><tr bgcolor=#CCE3F3>
      <th>&nbsp;&nbsp;&nbsp; id</th><th>&nbsp; &nbsp;</th><th>type</th><th>&nbsp; &nbsp;</th>
      <th>title</th><th>&nbsp; &nbsp;</th><th>created</th><th>&nbsp; &nbsp;</th></tr>
    <?php
     $query = "SELECT * FROM ".SYSLOG_TABLE." ORDER BY log_id DESC LIMIT 5";

     $result=mysql_query($query) or die( "Error: Query Failed");
     $num = 5;
     $i=0;
     while ($i < $num) {

      $log_id = mysql_result($result,$i,"log_id");
      $log_type = mysql_result($result,$i,"log_type");
      $title = mysql_result($result,$i,"title");
      $log = mysql_result($result,$i,"log");
      $created = mysql_result($result,$i,"created");

      if ($i % 2 != 0) # An odd row
       $bgColor = "#CCE3F3";
      else # An even row
       $bgColor = "#FFFFFF";

      print "<tr bgcolor=".$bgColor."><td>&nbsp; ".$log_id."</td><td>&nbsp; &nbsp;</td>"
           ."<td nowrap>".$log_type."</td><td>&nbsp; &nbsp;</td><td>"
           ."<span class=\"tooltip\">".$title."<span class=\"classic\">$log</span>"
           ."</span></td><td>&nbsp; &nbsp;</td><td>".$created."</td><td>&nbsp; &nbsp;</td></tr>";
      ++$i;
     }
    ?>
    </table>
   </fieldset> 
  </td>
  <td width=10>

  </td>
  <td valign=top>
   &nbsp;
  </td>
 </tr>
 <tr>
  <td colspan="3">
   
  </td>
 </tr>
</table>
