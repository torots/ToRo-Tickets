<?php
/*

  ToRo Ticketing System
  File for Staff Layout


*/

?>


   <form name='ticketHandling' action='<?php echo $PHP_SELF; ?>' method='post'>   
     <table border=1 cellspacing=0 cellpadding=2 class="tform" align="center" width="100%">

      <tr class='header'>
      <td colspan='2'><?php print translate("TICKET_HANDLING");?></td>
      </tr>

      <tr class='subheader'>
      <td width='5%'><?php print translate("ATTRIBUTE");?></td><td width='20%'><?php print translate("SETTING");?></td>           
      </tr>
     
      <tr>
       <th><?php print translate("DEFAULT_ACTION");?></th>
       <td><select name='defaultAction'>

       <?php 
           $sql = "SELECT defaultAction FROM " .CONFIG_TABLE;
	   $result = mysql_query($sql) or die (mysql_error());
	   while($row = mysql_fetch_array($result)){

	   if($row['defaultAction']=='changePriority'){ $prioritySelected='selected'; }else{ $prioritySelected='';  }
	   if($row['defaultAction']=='markOverdue'){ $overdueSelected='selected'; }else{ $overdueSelected='';  }
	   if($row['defaultAction']=='closeTicket'){ $closeSelected='selected'; }else{ $closeSelected='';  }
	   if($row['defaultAction']=='banDelete'){ $banSelected='selected'; }else{ $banSelected='';  }
	   if($row['defaultAction']=='delete'){ $deleteSelected='selected'; }else{ $deleteSelected='';  }
	   if($row['defaultAction']=='release'){ $releaseSelected='selected'; }else{ $releaseSelected='';  }  

	   }
        ?>

        	   <option value='changePriority' <?php echo $prioritySelected;?> ><?php print translate("CHANGE_PRIORITY");?></option>
		   <option value='markOverdue'    <?php echo $overdueSelected;?>  ><?php print translate("MARK_OVERDUE");?></option>
		   <option value='closeTicket'    <?php echo $closeSelected;?>    ><?php print translate("CLOSE_TICKET");?></option>
		   <option value='release' 	  <?php echo $releaseSelected;?>  ><?php print translate("RELEASE_UNASSIGN");?></option>
		   <option value='banDelete'      <?php echo $banSelected;?>      ><?php print translate("BAN_AND_DELETE");?></option>
		   <option value='delete'         <?php echo $deleteSelected;?>   ><?php print translate("LABEL_DELETE");?></option>
           </select></td>
      </tr>

      <tr><td colspan='2' align='right'><input type='submit' class='button' value='<?php print translate("LABEL_SUBMIT");?>' name='submit' /></td></tr>
      
     </table>
    </form>

    <br />

 <table border="1" cellspacing=0 cellpadding=2 class="tform" align="center" width="100%">
 
 <tr class='header'>
   <td colspan='5'><?php print translate("COLUMN_MANAGEMENT");?></td>
 </tr>

  <tr class='subheader'>
   <td width='5%'><?php print translate("TEXT_ACTIVE");?></td><td width='20%'><?php print translate("LABEL_NAME");?></td><td width='50%'><?php print translate("DESCRIPTION");?></td><td width='10%'><?php print translate("ORDER");?></td><td width='15%'></td>
  </tr>


<? 

   $sql = "SELECT id,name,description,weight,active FROM ".COLUMNS_TABLE." ORDER BY active DESC, weight asc";
   $result = mysql_query($sql) or die(mysql_error());
   
   while($row = mysql_fetch_array($result)){

   if($row['active']==1){ $activeChecked = 'checked'; }else{ $activeChecked = ''; }
   if($row['active']==0){ $color = '#666666'; }else{ $color = '#FFFFFF'; }

   print "<form name='formNum" .$row['id']. "' action='" .$PHP_SELF. "' method='post'>";
   print "<tr style='background-color: " .$color. ";'><td width='10%' align='center'><input type='checkbox' name='active' value='1' " .$activeChecked. " /></td>".
   "<td width='20%'><input type='text' name='name' value='" .$row['name']. "' /></td>".
   "<td width='60%'><input type='text' name='desc'  size='85' value='" .$row['description']. "' /></td>".
   "<td><input type='text' value='" .$row['weight']. "' size=3 name='weight' /></td><td><input type='submit' class='button' name='update' value='" .translate('UPDATE'). "' /></tr>";
   print "<input type='hidden' name='id' value='" .$row['id']. "' />";
   print "<input type='hidden' name='go' value='submit' />";
   print "</form>";
   }


?>

  </table>
