<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    r h <p@torots.com>
    Copyright (c)  2006-2011 toroTS
    http://www.torots.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: index.php,v 1.1 2012/01/19 16:09:09 root Exp $
**********************************************************************/
require('client.inc.php');
//We are only showing landing page to users who are not logged in.
if($thisclient && is_object($thisclient) && $thisclient->isValid()) {
    require('tickets.php');
    exit;
}


require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="index">
<h1><?= translate('TEXT_WELCOME_TITLE'); ?></h1>
<p class="big"> <?= translate('TEXT_WELCOME_MSG'); ?></p>
 <?php
  $query = "SELECT client_motd,client_motd_lastupdated FROM ".CONFIG_TABLE.";";
  $result = mysql_query($query) or die( "Error: Query Failed");

  $motd = mysql_result($result,0,"client_motd");
  $motd_lastupdated = mysql_result($result,0,"client_motd_lastupdated");

  if(!empty($motd)) {
   echo "<hr /><b>Message of the Day:</b> ";
   echo $motd;
  }  
 ?>
<hr />
<br />
<div class="lcol">
  <img src="./images/new_ticket_icon.jpg" width="48" height="48" align="left" style="padding-bottom:150px;">
  <h3><?= translate('TITLE_BOX_NEW_TICKET'); ?></h3>
  <p><?= translate('TEXT_BOX_NEW_TICKET'); ?></p>
  <form method="link" action="open.php">
  <br /><br />
  <input type="submit" class="button" value="<?= translate('LABEL_OPEN_NEW_TICKET') ?>">
  </form>
</div>
<div class="rcol">
  <img src="./images/ticket_status_icon.jpg" width="48" height="48" align="left" style="padding-bottom:150px;">
  <h3><?= translate('TITLE_OPEN_PREVIOUS_TICKET'); ?></h3>
  <?= translate('TEXT_OPEN_PREVIOUS_TICKET'); ?>
  <br /><br />
  <form class="status_form" action="login.php" method="post">
    <fieldset>
      <label><?= translate('LABEL_EMAIL'); ?></label>
      <input type="text" name="lemail">
    </fieldset>
    <fieldset>
     <label><?= translate('LABEL_TICKET_NUMBER'); ?></label>
     <input type="text" name="lticket">
    </fieldset>
    <fieldset>
        <label>&nbsp;</label>
         <input type="submit" class="button" value="<?= translate('LABEL_CHECK_STATUS'); ?>">
    </fieldset>
  </form>
</div>
<div class="clear"></div>
<br />
</div>
<br />
<?require(CLIENTINC_DIR.'footer.inc.php'); ?>
