<?php
if(!defined('OSTCLIENTINC')) die('Kwaheri');

$e=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$t=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);
?>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div style="margin:5px 0px 100px 0;text-align:center; width:100%;">
    <p align="center"><?php echo translate('TEXT_LOGIN_VIEW_STATUS_OF_TICKET');?>;
    </p>
    <span class="error"><?=Format::htmlchars($loginmsg)?></span>
    <form action="login.php" method="post">
    <table cellspacing="1" cellpadding="5" border="0" bgcolor="#000000" align="center">
        <tr bgcolor="#EEEEEE"> 
            <td><?php echo translate('LABEL_EMAIL'); ?>:</td><td><input type="text" name="lemail" size="25" value="<?=$e?>"></td>
            <td><?php echo translate('LABEL_TICKET_ID')?>:</td><td><input type="text" name="lticket" size="10" value="<?=$t?>"></td>
            <td><input class="button" type="submit" value="<?php echo translate('LABEL_VIEW_STATUS');?>"></td>
        </tr>
    </table>
    </form>
</div>
