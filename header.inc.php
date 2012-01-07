<? if(!defined('OSTSCPINC') || !is_object($thisuser) || !$thisuser->isStaff() || !is_object($nav)) die(print translate("TEXT_ACCESS_DENIED"));?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php
$uri = $_SERVER['REQUEST_URI'];
// echo "URI is " .$uri;
if ($uri == "/dev/scp/index.php" || $uri == "/dev/scp/tickets.php") {
$sql = "SELECT auto_refresh_rate FROM ".STAFF_TABLE;
$result = mysql_query($sql)or die(mysql_error());

while($row = mysql_fetch_array($result)){
if($row['auto_refresh_rate']>0){ //Refresh rate
$refresh=($row['auto_refresh_rate']*60);
echo '<meta http-equiv="refresh" content="'.$refresh.'" />';
  }
 }
}
?>
<title>toroTS :: Staff Control Panel</title>
<link rel="stylesheet" href="../styles/staff_main.css" media="screen">
<link rel="stylesheet" href="../styles/staff_style.css" media="screen">
<link rel="stylesheet" href="../styles/staff_color.css" type="text/css">
<link rel="stylesheet" href="../styles/staff_button.css" type="text/css">
<link rel="stylesheet" href="../styles/staff_tabs.css" type="text/css">
<link rel="stylesheet" href="../styles/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="../styles/js/ajax.js"></script>
<script type="text/javascript" src="../styles/js/scp.js"></script>
<script type="text/javascript" src="../styles/js/tabber.js"></script>
<script type="text/javascript" src="../styles/js/calendar.js"></script>
<script type="text/javascript" src="../styles/js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
<?php
if($cfg && $cfg->getLockTime()) { //autoLocking enabled.?>
<script type="text/javascript" src="../styles/js/autolock.js" charset="utf-8"></script>
<?}?>
</head>
<body>
<?php
if($sysnotice){?>
<div id="system_notice"><?php echo $sysnotice; ?></div>
<?php 
}

// Get the logo
$getlogo = "SELECT stafflogo FROM ".THEME_TABLE." WHERE stafflogoactive = '1'";
$result = mysql_query($getlogo) or die(mysql_error());
if (mysql_num_rows($result)==0)
 $logopath = "../images/logo1.png";
else
 $logopath = mysql_result($result,0);

$langID = $thisuser->getStaffLang();

?>
<div id="container">
    <div id="header">
        <a id="logo" href="index.php" title="toroTS"><img src="<?php echo $logopath; ?>" width="250" height="76" alt="toroTS"></a>
        <p id="info"><?php translate('TEXT_WELCOME_BACK_STAFF'); ?> 
           <?php
            if($thisuser->isAdmin() && !defined('ADMINPAGE')) { ?>
            | <a href="admin.php"><?php translate('LABEL_ADMIN_PANEL');?></a> 
            <?}else{?>
            | <a href="index.php"><?php translate('LABEL_STAFF_PANEL');?></a>
            <?}?>
            | <a href="profile.php?t=pref"><?php echo translate('LABEL_MY_PREFERENCE');?></a> | <a href="logout.php"><?php echo translate('LABEL_LOG_OUT');?></a></p>
    </div>
    <div id="nav">
        <ul id="main_nav" <?=!defined('ADMINPAGE')?'class="dist"':''?>>
            <?
            if(($tabs=$nav->getTabs()) && is_array($tabs)){
             foreach($tabs as $tab) { ?>
                <li><a <?=$tab['active']?'class="active"':''?> href="<?=$tab['href']?>" title="<?=$tab['title']?>"><?=$tab['desc']?></a></li>
            <?}
            }else{ //?? ?>
                <li><a href="profile.php" title="<?php echo translate('LABEL_MY_PREFERENCE');?>"><?php echo translate('LABEL_MY_ACCOUNT');?></a></li>
            <?}?>
        </ul>
        <ul id="sub_nav">
            <?php
            if(($subnav=$nav->getSubMenu()) && is_array($subnav)){
              foreach($subnav as $item) { ?>
                <li><a class="<?=$item['iconclass']?>" href="<?=$item['href']?>" title="<?echo $item['title'];?>"><?=$item['desc']?></a></li>
              <?}
            }?>
        </ul>
    </div>
    <div class="clear"></div>
    <div id="content" width="100%">

