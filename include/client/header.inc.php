<?php
$title=($cfg && is_object($cfg))?$cfg->getTitle():'osTicket :: Support Ticket System';

$getlogo = "SELECT clientlogo FROM ".THEME_TABLE." WHERE clientlogoactive = '1'";
$result = mysql_query($getlogo) or die(mysql_error());
if (mysql_num_rows($result)==0)
 $logopath = "./images/logo1.png";
else
 $logopath = mysql_result($result,0);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type">
    <title><?=Format::htmlchars($title)?></title>
    <link rel="stylesheet" href="./styles/client_main.css" media="screen">
    <link rel="stylesheet" href="./styles/client_button.css" media="screen">
    <link rel="stylesheet" href="./styles/client_color.css" media="screen">
</head>
<body dir="<?php echo $dir; ?>">
<div id="container">
    <div id="header">
        <!--<a id="logo" href="index.php" title="<?= translate('TEXT_SUPPORT_CENTER'); ?>"><img src="./images/logo2.jpg" border=0 alt="Support Center"></a>-->
        <a id="logo" href="index.php" title="<?= translate('TEXT_SUPPORT_CENTER'); ?>"><img src="<?php echo $logopath; ?>" width='190' height='60' border=0 alt="Support Center"></a>
        <p><?= translate('TEXT_SUPPORT_TICKET_SISTEM'); ?></p>
    </div>
    <ul id="nav">
         <?                    
         if($thisclient && is_object($thisclient) && $thisclient->isValid()) {?>
         <li><a class="log_out" href="logout.php"><?= translate('TEXT_LOG_OUT'); ?></a></li>
         <?}else {?>
         <li><a class="ticket_status" href="tickets.php"><?= translate('TEXT_TICKETS_STATUS'); ?></a></li>
         <?}?>
         <li><a class="new_ticket" href="open.php"><?= translate('TEXT_NEW_TICKET'); ?></a></li>
         <li><a class="home" href="index.php"><?= translate('TEXT_HOME'); ?></a></li>
    </ul>
    <div id="content">
