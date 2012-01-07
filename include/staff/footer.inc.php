
</div> 
</div>
<div id="footer">Copyright &copy; <?php
   $startd = '2011';
   $currd = date(Y);

   if ($startd == $currd) {
    echo "$startd";
   }
   else {
    echo $startd.'-'.$currd;
   }
  ?>&nbsp;torots.com. &nbsp;All Rights Reserved.</div>
<?php if(is_object($thisuser) && $thisuser->isStaff()) {?>
<div>
    <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
    <img src="autocron.php" alt="" width="1" height="1" border="0" />
    <!-- Do not remove <img src="autocron.php" alt="" width="1" height="1" border="0" /> or your auto cron will cease to function -->
</div>
<?}?>
</div>
</body>
</html>
