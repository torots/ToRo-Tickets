<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));

//Get the config info.
$config=($errors && $_POST)?Format::input($_POST):Format::htmlchars($cfg->getConfig());
//Basic checks for warnings...
$warn=array();
if($config['allow_attachments'] && !$config['upload_dir']) {
    $errors['allow_attachments']='You need to setup upload dir.';    
}else{
    if(!$config['allow_attachments'] && $config['allow_email_attachments'])
        $warn['allow_email_attachments']='*Attachments Disabled.';
    if(!$config['allow_attachments'] && ($config['allow_online_attachments'] or $config['allow_online_attachments_onlogin']))
        $warn['allow_online_attachments']='<br>*Attachments Disabled.';
}

if(!$errors['enable_captcha'] && $config['enable_captcha'] && !extension_loaded('gd'))
    $errors['enable_captcha']='GD required for captcha to work';
    

//Not showing err on post to avoid alarming the user...after an update.
if(!$errors['err'] &&!$msg && $warn )
    $errors['err']='Possible errors detected, please check the warnings below';
    
$gmtime=Misc::gmtime();
$depts= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.' WHERE ispublic=1');
$templates=db_query('SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_TABLE.' WHERE cfg_id='.db_input($cfg->getId()));
?>
<div class="msg"><?php print translate('LABEL_SYSTEM_PREFERENCES_AND_SETTINGS');?>&nbsp;&nbsp;(v<?=$config['ostversion']?>)</div>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
 <form action="admin.php?t=pref" method="post">
 <input type="hidden" name="t" value="pref">
 <tr><td>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header" ><td colspan=2><?php print translate('LABEL_GENERAL_SETTINGS')?></td></tr>
        <tr class="subheader">
            <td colspan=2"><?php print translate('TEXT_OFFLINE_MODE_WILL_DISABLE_CLIENT')?></td>
        </tr>
        <tr><th><b><?php print translate('LABEL_HELPDESK_STATUS')?></b></th>
            <td>
                <input type="radio" name="isonline"  value="1"   <?=$config['isonline']?'checked':''?> /><b><?php print translate("TEXT_ONLINE");?></b> <?php print translate('LABEL_ACTIVE_IN_PARENTHESIS')?>
                <input type="radio" name="isonline"  value="0"   <?=!$config['isonline']?'checked':''?> /><b><?php print translate("OFFLINE");?></b> <?php print translate('LABEL_DISABLED_IN_PARENTHESIS')?>
                &nbsp;<font class="warn">&nbsp;<?=$config['isoffline']?'toroTS offline':''?></font>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_URL')?></th>
            <td>
                <input type="text" size="40" name="helpdesk_url" value="<?=$config['helpdesk_url']?>"> 
                &nbsp;<font class="error">*&nbsp;<?=$errors['helpdesk_url']?></font></td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_NAME_TITLE')?></th>
            <td><input type="text" size="40" name="helpdesk_title" value="<?=$config['helpdesk_title']?>"> </td>
        </tr>
        <tr><th><?php print translate('LABEL_CLIENT_MOTD')?><br><?php print translate("LABEL_UPDATE_DATE");?>:<?php echo $config['client_motd_lastupdated']; ?></th>
            <td><textarea cols="60" name="client_motd" value=""><?php echo $config['client_motd']?></textarea></td>
        </tr>
        <tr><th><?php print translate('LABEL_STAFF_MOTD')?><br><?php print translate("LABEL_UPDATE_DATE");?>:<?php echo $config['staff_motd_lastupdated']; ?></th>
            <td><textarea cols="60" name="staff_motd" value=""><?php echo $config['staff_motd']?></textarea></td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_DEFAULT_EMAIL_TEMPLATES')?></th>
            <td>
                <select name="default_template_id">
                    <option value=0><?php print translate('LABEL_HELPDESK_SELECT_DEFAULT_TEMPLATE')?></option>
                    <?
                    while (list($id,$name) = db_fetch_row($templates)){
                        $selected = ($config['default_template_id']==$id)?'SELECTED':''; ?>
                        <option value="<?=$id?>"<?=$selected?>><?=$name?></option>
                    <?
                    }?>
                </select>&nbsp;<font class="error">*&nbsp;<?=$errors['default_template_id']?></font>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_DEFAULT_DEPARTMENT')?></th>
            <td>
                <select name="default_dept_id">
                    <option value=0><?php print translate('LABEL_HELPDESK_SELECT_DEFAULT_DEPARTMENT')?></option>
                    <?
                    while (list($id,$name) = db_fetch_row($depts)){
                    $selected = ($config['default_dept_id']==$id)?'SELECTED':''; ?>
                    <option value="<?=$id?>"<?=$selected?>><?=$name?> Dept</option>
                    <?
                    }?>
                </select>&nbsp;<font class="error">*&nbsp;<?=$errors['default_dept_id']?></font>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_DEFAULT_PAGE_SIZE')?></th>
            <td>
                <select name="max_page_size">
                    <?
                     $pagelimit=$config['max_page_size'];
                    for ($i = 5; $i <= 50; $i += 5) {
                        ?>
                        <option <?=$config['max_page_size'] == $i ? 'SELECTED':''?> value="<?=$i?>"><?=$i?></option>
                        <?
                    }?>
                </select>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_SYSTEM_LOG_LEVEL')?></th>
            <td>
                <select name="log_level">
                    <option value=0 <?=$config['log_level'] == 0 ? 'selected="selected"':''?>><?php print translate('LABEL_HELPDESK_NONE_DISABLE_LOGGER')?></option>
                    <option value=3 <?=$config['log_level'] == 3 ? 'selected="selected"':''?>> <?php print translate("TEXT_DEBUG");?></option>
                    <option value=2 <?=$config['log_level'] == 2 ? 'selected="selected"':''?>> <?php print translate("TEXT_WARNINGS");?></option>
                    <option value=1 <?=$config['log_level'] == 1 ? 'selected="selected"':''?>> <?php print translate("TEXT_ERRORS");?></option>
                </select>
                &nbsp;<?php print translate('LABEL_HELPDESK_PURGE_LOGS_AFTER')?>
                <select name="log_graceperiod">
                    <option value=0 selected><?php print translate('LABEL_HELPDESK_PURGE_NONE_DISABLE')?></option>
                    <?
                    for ($i = 1; $i <=12; $i++) {
                        ?>
                        <option <?=$config['log_graceperiod'] == $i ? 'SELECTED':''?> value="<?=$i?>"><?=$i?>&nbsp;<?=($i>1)?print translate('LABEL_MONTHS'):print translate('LABEL_MONTH')?></option>
                        <?
                    }?>
                </select>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_STAFF_EXCESSIVE_LOGINS')?></th>
            <td>
                <select name="staff_max_logins">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>',$i,(($config['staff_max_logins']==$i)?'selected="selected"':''),$i);
                    }
                    ?>
                </select> <?php print translate('LABEL_HELPDESK_ATTEMPT_ALLOWED_BEFORE_A')?> 
                <select name="staff_login_timeout">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>',$i,(($config['staff_login_timeout']==$i)?'selected="selected"':''),$i);
                    }
                    ?>
                </select> <?php print translate('LABEL_HELPDESK_PENALTY_IN_MINUTES')?>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_STAFF_SESSION_TIMEOUT')?></th>
            <td>
              <input type="text" name="staff_session_timeout" size=6 value="<?=$config['staff_session_timeout']?>">
                <?php print translate('TEXT_HELPDESK_STAFF_MAX_IDLE_TIME_IN_MINUTES')?>
            </td>
        </tr>
       <tr><th><?php print translate('LABEL_HELPDESK_BIND_STAFF_SESSION_TO_IP')?></th>
            <td>
              <input type="checkbox" name="staff_ip_binding" <?=$config['staff_ip_binding']?'checked':''?>>
               <?php print translate("LABEL_HELPDESK_BIND_STAFF_SESSION_TO_IP");?>.
            </td>
        </tr>

        <tr><th><?php print translate('LABEL_HELPDESK_CLIENT_EXCESSIVE_LOGINS')?></th>
            <td>
                <select name="client_max_logins">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>',$i,(($config['client_max_logins']==$i)?'selected="selected"':''),$i);
                    }

                    ?>
                </select><?php print translate('LABEL_HELPDESK_ATTEMPT_ALLOWED_BEFORE_A')?>
                <select name="client_login_timeout">
                  <?php
                    for ($i = 1; $i <= 10; $i++) {
                        echo sprintf('<option value="%d" %s>%d</option>',$i,(($config['client_login_timeout']==$i)?'selected="selected"':''),$i);
                    }
                    ?>
                </select> <?php print translate('LABEL_HELPDESK_PENALTY_IN_MINUTES')?>
            </td>
        </tr>

        <tr><th><?php print translate('LABEL_HELPDESK_CLIENT_SESSION_TIMEOUT')?></th>
            <td>
              <input type="text" name="client_session_timeout" size=6 value="<?=$config['client_session_timeout']?>">
                <?php print translate('TEXT_HELPDESK_CLIENT_MAX_IDLE_TIME_IN_MINUTES')?>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_CLICKABLE_URLS')?></th>
            <td>
              <input type="checkbox" name="clickable_urls" <?=$config['clickable_urls']?'checked':''?>>
                <?php print translate('LABEL_HELPDESK_MAKE_URLS_CLICKABLE')?>
            </td>
        </tr>
        <tr><th><?php print translate('LABEL_HELPDESK_ENABLE_AUTO_CRON')?></th>
            <td>
              <input type="checkbox" name="enable_auto_cron" <?=$config['enable_auto_cron']?'checked':''?>>
                <?php print translate('TEXT_HELPDESK_ENABLE_CRON_CALL_ON_STAFF_ACTIVITY')?>
            </td>
        </tr>

	<tr>
     <th><?php print translate("TOOLTIPS");?></th>
     <td>
      <input type="checkbox" name="enable_tooltips" <?=$config['tool_tips']?'checked':''?> /> <?php print translate("TOOLTIPS_NOTE");?>
     </td>
    </tr>
	
    </table>
    
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate("TITLE_DATE_AND_TIME");?></td></tr>
        <tr class="subheader">
            <td colspan=2><?php print translate("TEXT_REFER_TO_PHP");?>.</td>
        </tr>
        <tr><th><?php print translate("TEXT_TIME_FORMAT");?>:</th>
            <td>
                <input type="text" name="time_format" value="<?=$config['time_format']?>">
                    &nbsp;<font class="error">*&nbsp;<?=$errors['time_format']?></font>
                    <i><?=Format::date($config['time_format'],$gmtime,$config['timezone_offset'],$config['enable_daylight_saving'])?></i></td>
        </tr>
        <tr><th><?php print translate("TEXT_DATE_FORMAT");?>:</th>
            <td><input type="text" name="date_format" value="<?=$config['date_format']?>">
                        &nbsp;<font class="error">*&nbsp;<?=$errors['date_format']?></font>
                        <i><?=Format::date($config['date_format'],$gmtime,$config['timezone_offset'],$config['enable_daylight_saving'])?></i>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_DATE_AND_TIME_FORMAT");?>:</th>
            <td><input type="text" name="datetime_format" value="<?=$config['datetime_format']?>">
                        &nbsp;<font class="error">*&nbsp;<?=$errors['datetime_format']?></font>
                        <i><?=Format::date($config['datetime_format'],$gmtime,$config['timezone_offset'],$config['enable_daylight_saving'])?></i>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_DAY_DATE_AND_TIME_FORMAT");?></th>
            <td><input type="text" name="daydatetime_format" value="<?=$config['daydatetime_format']?>">
                        &nbsp;<font class="error">*&nbsp;<?=$errors['daydatetime_format']?></font>
                        <i><?=Format::date($config['daydatetime_format'],$gmtime,$config['timezone_offset'],$config['enable_daylight_saving'])?></i>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_DEFAULT_TIMEZONE");?>:</th>
            <td>
                <select name="timezone_offset">
                    <?
                    $gmoffset = date("Z") / 3600; //Server's offset.
                    echo"<option value=\"$gmoffset\">Server Time (GMT $gmoffset:00)</option>"; //Default if all fails.
                    $timezones= db_query('SELECT offset,timezone FROM '.TIMEZONE_TABLE);
                    while (list($offset,$tz) = db_fetch_row($timezones)){
                        $selected = ($config['timezone_offset'] ==$offset) ?'SELECTED':'';
                        $tag=($offset)?"GMT $offset ($tz)":" GMT ($tz)";
                        ?>
                        <option value="<?=$offset?>"<?=$selected?>><?=$tag?></option>
                        <?
                    }?>
                </select>
            </td>
        </tr>
        <tr>
            <th><?php print translate("TEXT_DAYLIGHT_SAVINGS");?>:</th>
            <td>
                <input type="checkbox" name="enable_daylight_saving" <?=$config['enable_daylight_saving'] ? 'checked': ''?>><?php print translate("TEXT_OBSERVE_DAYLIGHT_SAVINGS");?>
            </td>
        </tr>
    </table>
   
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate("TEXT_TICKET_OPTIONS_AND_SETTINGS");?></td></tr>
        <tr class="subheader"><td colspan=2><?php print translate("TEXT_TICKET_OPTIONS_DESCRIPTION");?>.</td></tr>
        <tr><th valign="top"><?php print translate("LABEL_TICKET_ID");?>:</th>
            <td>
                <input type="radio" name="random_ticket_ids"  value="0"   <?=!$config['random_ticket_ids']?'checked':''?> /> <?php print translate("TEXT_SEQUENTIAL");?>
                <input type="radio" name="random_ticket_ids"  value="1"   <?=$config['random_ticket_ids']?'checked':''?> /> <?php print translate("TEXT_RANDOM_RECOMMENDED");?>
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("TEXT_TICKET_PRIORITY");?>:</th>
            <td>
                <select name="default_priority_id">
                    <?
                    $priorities= db_query('SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE);
                    while (list($id,$tag) = db_fetch_row($priorities)){ ?>
                        <option value="<?=$id?>"<?=($config['default_priority_id']==$id)?'selected':''?>><?php eval('?>' . $tag . '<?php '); ?></option>
                    <?
                    }?>
                </select> &nbsp;<?php print translate("TEXT_DEFAULT_PRIORITY");?><br/>
                <input type="checkbox" name="allow_priority_change" <?=$config['allow_priority_change'] ?'checked':''?>>
                    <?php print translate("TEXT_ALLOW_USER_OVERWRITE_PRIORITY");?><br/>
                <input type="checkbox" name="use_email_priority" <?=$config['use_email_priority'] ?'checked':''?> >
                    <?php print translate("TEXT_USE_EMAIL_PRIORITY");?>

            </td>
        </tr>
        <tr><th><?php print translate("TEXT_MAXIMUM_OPEN_TICKETS");?>:</th>
            <td>
              <input type="text" name="max_open_tickets" size=4 value="<?=$config['max_open_tickets']?>"> 
                <?php print translate("TEXT_MAXIMUM_OPEN_TICKETS_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_AUTO_LOCK_TIME");?>:</td>
            <td>
              <input type="text" name="autolock_minutes" size=4 value="<?=$config['autolock_minutes']?>">
                 <font class="error"><?=$errors['autolock_minutes']?></font>
                <?php print translate("TEXT_AUTO_LOCK_TIME_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_TICKET_GRACE_PERIOD");?>:</th>
            <td>
              <input type="text" name="overdue_grace_period" size=4 value="<?=$config['overdue_grace_period']?>">
                <?php print translate("TEXT_TICKET_GRACE_PERIOD_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_REOPENED_TICKETS");?>:</th>
            <td>
              <input type="checkbox" name="auto_assign_reopened_tickets" <?=$config['auto_assign_reopened_tickets'] ? 'checked': ''?>> 
                <?php print translate("TEXT_REOPENED_TICKETS_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("LABEL_ASSIGNED_TICKETS");?>:</th>
            <td>
              <input type="checkbox" name="show_assigned_tickets" <?=$config['show_assigned_tickets']?'checked':''?>>
                <?php print translate("LABEL_ASSIGNED_TICKETS_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_ANSWERED_TICKETS");?>:</th>
            <td>
              <input type="checkbox" name="show_answered_tickets" <?=$config['show_answered_tickets']?'checked':''?>>
                <?php print translate("TEXT_ANSWERED_TICKETS");?>.
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_TICKET_ACTIVITY_LOG");?>:</th>
            <td>
              <input type="checkbox" name="log_ticket_activity" <?=$config['log_ticket_activity']?'checked':''?>>
                <?php print translate("TEXT_TICKET_ACTIVITY_LOG_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_STAFF_IDENTITY");?>:</th>
            <td>
              <input type="checkbox" name="hide_staff_name" <?=$config['hide_staff_name']?'checked':''?>>
                <?php print translate("TEXT_STAFF_IDENTITY_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_HUMAN_VERIFICATION");?>:</th>
            <td>
                <?php
                   if($config['enable_captcha'] && !$errors['enable_captcha']) {?>
                        <img src="../captcha.php" border="0" align="left">&nbsp;
                <?}?>
              <input type="checkbox" name="enable_captcha" <?=$config['enable_captcha']?'checked':''?>>
                <?php print translate("TEXT_HUMAN_VERIFICATION_DESCRIPTION");?>&nbsp;<font class="error">&nbsp;<?=$errors['enable_captcha']?></font><br/>
            </td>
        </tr>

    </table>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2 ><?php print translate("LABEL_EMAIL_SETTINGS");?></td></tr>
        <tr class="subheader"><td colspan=2><?php print translate("LABEL_EMAIL_SETTINGS_NOTE");?></td></tr>
        <tr><th valign="top"><br><b><?php print translate("TEXT_INCOMING_EMAILS");?></b>:</th>
            <td><?php print translate("TEXT_INCOMING_EMAILS_DESCRIPTION");?><br/>
                <input type="checkbox" name="enable_mail_fetch" value=1 <?=$config['enable_mail_fetch']? 'checked': ''?>  > <?php print translate("TEXT_ENABLE_IMAP_POP_FETCH");?><br/>
                <input type="checkbox" name="enable_email_piping" value=1 <?=$config['enable_email_piping']? 'checked': ''?>  > <?php print translate("TEXT_ENABLE_EMAIL_PIPING");?><br/>
                <input type="checkbox" name="strip_quoted_reply" <?=$config['strip_quoted_reply'] ? 'checked':''?>><?php print translate("TEXT_STRIP_QUOTED_REPLY");?><br/>
                <input type="text" name="reply_separator" value="<?=$config['reply_separator']?>"> <?php print translate("TEXT_REPLY_SEPARATOR_TAG");?>
                &nbsp;<font class="error">&nbsp;<?=$errors['reply_separator']?></font>
            </td>
        </tr>
        <tr><th valign="top"><br><b><?php print translate("TEXT_OUTGOING_EMAILS");?></b>:</th>
            <td>
                <?php print translate("TEXT_OUTGOING_EMAILS_DEFAULT");?><br/>
                <select name="default_smtp_id"
                    onChange="document.getElementById('overwrite').style.display=(this.options[this.selectedIndex].value>0)?'block':'none';">
                    <option value=0><?php print translate('LABEL_SELECT_ONE_EMAIL') ?></option>
                    <option value=0 selected="selected"><?php print translate("TEXT_NONE_USE_PHP_MAIL");?></option>
                    <?
                    $emails=db_query('SELECT email_id,email,name,smtp_host FROM '.EMAIL_TABLE.' WHERE smtp_active=1');
                    if($emails && db_num_rows($emails)) {
                        while (list($id,$email,$name,$host) = db_fetch_row($emails)){
                            $email=$name?"$name &lt;$email&gt;":$email;
                            $email=sprintf('%s (%s)',$email,$host);
                            ?>
                            <option value="<?=$id?>"<?=($config['default_smtp_id']==$id)?'selected="selected"':''?>><?=$email?></option>
                        <?
                        }
                    }?>
                 </select>&nbsp;&nbsp;<font class="error">&nbsp;<?=$errors['default_smtp_id']?></font><br/>
                 <span id="overwrite" style="display:<?=($config['default_smtp_id']?'display':'none')?>">
                    <input type="checkbox" name="spoof_default_smtp" <?=$config['spoof_default_smtp'] ? 'checked':''?>>
                        <?php print translate("TEXT_ALLOW_SPOOFING");?><font class="error">&nbsp;<?=$errors['spoof_default_smtp']?></font><br/>
                        </span>
             </td>
        </tr>
        <tr><th><?php print translate("TEXT_DEFAULT_SYSTEM_EMAIL");?>:</th>
            <td>
                <select name="default_email_id">
                    <option value=0 disabled><?php print translate("LABEL_SELECT_ONE_EMAIL"); ?></option>
                    <?
                    $emails=db_query('SELECT email_id,email,name FROM '.EMAIL_TABLE);
                    while (list($id,$email,$name) = db_fetch_row($emails)){ 
                        $email=$name?"$name &lt;$email&gt;":$email;
                        ?>
                     <option value="<?=$id?>"<?=($config['default_email_id']==$id)?'selected':''?>><?=$email?></option>
                    <?
                    }?>
                 </select>
                 &nbsp;<font class="error">*&nbsp;<?=$errors['default_email_id']?></font></td>
        </tr>
        <tr><th valign="top"><?php print translate("TEXT_DEFAULT_ALERT_EMAIL");?>:</th>
            <td>
                <select name="alert_email_id">
                    <option value=0 disabled><?php print translate('LABEL_SELECT_ONE_EMAIL') ?></option>
                    <option value=0 selected="selected"><?php print translate("TEXT_USE_DEFAULT_EMAIL_ABOVE");?></option>
                    <?
                    $emails=db_query('SELECT email_id,email,name FROM '.EMAIL_TABLE.' WHERE email_id != '.db_input($config['default_email_id']));
                    while (list($id,$email,$name) = db_fetch_row($emails)){
                        $email=$name?"$name &lt;$email&gt;":$email;
                        ?>
                     <option value="<?=$id?>"<?=($config['alert_email_id']==$id)?'selected':''?>><?=$email?></option>
                    <?
                    }?>
                 </select>
                 &nbsp;<font class="error">*&nbsp;<?=$errors['alert_email_id']?></font>
                <br/><?php print translate("TEXT_DEFAULT_ALERT_EMAIL_DESCRIPTION");?>
            </td>
        </tr>
        <tr><th><?php print translate("TEXT_SYSTEM_ADMIN_EMAIL_ADDRESS");?>:</th>
            <td>
                <input type="text" size=25 name="admin_email" value="<?=$config['admin_email']?>">
                    &nbsp;<font class="error">*&nbsp;<?=$errors['admin_email']?></font></td>
        </tr>
    </table>

    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate("TEXT_AUTORESPONDERS");?></td></tr>
        <tr class="subheader"><td colspan=2"><?php print translate("TEXT_AUTORESPONDERS_DESCRIPTION");?></td></tr>
        <tr><th valign="top"><?php print translate("TITLE_BOX_NEW_TICKET");?>:</th>
            <td><i><?php print translate("TEXT_AUTORESPONSE_TICKET_ID_NOTE");?></i><br>
                <input type="radio" name="ticket_autoresponder"  value="1"   <?=$config['ticket_autoresponder']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="ticket_autoresponder"  value="0"   <?=!$config['ticket_autoresponder']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("TEXT_AUTORESPOND_NEW_TICKET_BY_STAFF");?>:</th>
            <td><i><?php print translate("TEXT_AUTORESPOND_NEW_TICKET_BY_STAFF_NOTE");?></i><br>
                <input type="radio" name="ticket_notice_active"  value="1"   <?=$config['ticket_notice_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="ticket_notice_active"  value="0"   <?=!$config['ticket_notice_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("LABEL_NEW_MESSAGE");?>:</th>
            <td><i><?php print translate("TEXT_AUTORESPOND_NEW_MESSAGE_NOTE");?></i><br>
                <input type="radio" name="message_autoresponder"  value="1"   <?=$config['message_autoresponder']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="message_autoresponder"  value="0"   <?=!$config['message_autoresponder']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("TEXT_OVERLIMIT_NOTICE");?>:</th>
            <td><i><?php print translate("TEXT_OVERLIMIT_NOTICE_DESCRIPTION");?></i><br/>               
                <input type="radio" name="overlimit_notice_active"  value="1"   <?=$config['overlimit_notice_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="overlimit_notice_active"  value="0"   <?=!$config['overlimit_notice_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
                <br><i><?php print translate("TEXT_OVERLIMIT_NOTICE_NOTE");?></i><br>
            </td>
        </tr>
    </table>
    <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
        <tr class="header"><td colspan=2><?php print translate("TEXT_ALERTS_AND_NOTICES");?></td></tr>
        <tr class="subheader"><td colspan=2>
            <?php print translate("TEXT_ALERTS_AND_NOTICES_NOTE");?></td>
        </tr>
        <tr><th valign="top"><?php print translate("TEXT_NEW_TICKET_ALERT");?>:</th>
            <td>
                <input type="radio" name="ticket_alert_active"  value="1"   <?=$config['ticket_alert_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
                <input type="radio" name="ticket_alert_active"  value="0"   <?=!$config['ticket_alert_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
                <br><i><?php print translate("SELECT_RECIPIENTS");?></i>&nbsp;<font class="error">&nbsp;<?=$errors['ticket_alert_active']?></font><br>
                <input type="checkbox" name="ticket_alert_admin" <?=$config['ticket_alert_admin']?'checked':''?>> <?php print translate("ADMIN_EMAIL");?>
                <input type="checkbox" name="ticket_alert_dept_manager" <?=$config['ticket_alert_dept_manager']?'checked':''?>> <?php print translate("DEPARTMENT_MANAGER");?>
                <input type="checkbox" name="ticket_alert_dept_members" <?=$config['ticket_alert_dept_members']?'checked':''?>> <?php print translate("DEPARTMENT_MEMBERS");?> 
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("NEW_MESSAGE_ALERT");?>:</th>
            <td>
              <input type="radio" name="message_alert_active"  value="1"   <?=$config['message_alert_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
              <input type="radio" name="message_alert_active"  value="0"   <?=!$config['message_alert_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
              <br><i><?php print translate("SELECT_RECIPIENTS");?></i>&nbsp;<font class="error">&nbsp;<?=$errors['message_alert_active']?></font><br>
              <input type="checkbox" name="message_alert_laststaff" <?=$config['message_alert_laststaff']?'checked':''?>> <?php print translate("LAST_RESPONDENT");?>
              <input type="checkbox" name="message_alert_assigned" <?=$config['message_alert_assigned']?'checked':''?>> <?php print translate("ASSIGNED_STAFF");?>
              <input type="checkbox" name="message_alert_dept_manager" <?=$config['message_alert_dept_manager']?'checked':''?>> <?php print translate("DEPARTMENT_MANAGER");?> 
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("NEW_INTERNAL_NOTE_ALERT");?>:</th>
            <td>
              <input type="radio" name="note_alert_active"  value="1"   <?=$config['note_alert_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
              <input type="radio" name="note_alert_active"  value="0"   <?=!$config['note_alert_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
              <br><i><?php print translate("SELECT_RECIPIENTS");?></i>&nbsp;<font class="error">&nbsp;<?=$errors['note_alert_active']?></font><br>
              <input type="checkbox" name="note_alert_laststaff" <?=$config['note_alert_laststaff']?'checked':''?>> <?php print translate("LAST_RESPONDENT");?>
              <input type="checkbox" name="note_alert_assigned" <?=$config['note_alert_assigned']?'checked':''?>> <?php print translate("ASSIGNED_STAFF");?>
              <input type="checkbox" name="note_alert_dept_manager" <?=$config['note_alert_dept_manager']?'checked':''?>> <?php print translate("DEPARTMENT_MANAGER");?> 
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("OVERDUE_TICKET_ALERT");?>:</th>
            <td>
              <input type="radio" name="overdue_alert_active"  value="1"   <?=$config['overdue_alert_active']?'checked':''?> /><?php print translate("TEXT_ENABLE");?>
              <input type="radio" name="overdue_alert_active"  value="0"   <?=!$config['overdue_alert_active']?'checked':''?> /><?php print translate("TEXT_DISABLE");?>
              <br><i><?php print translate("OVERDUE_TICKET_NOTE");?></i>&nbsp;<font class="error">&nbsp;<?=$errors['overdue_alert_active']?></font><br>
              <input type="checkbox" name="overdue_alert_assigned" <?=$config['overdue_alert_assigned']?'checked':''?>> <?php print translate("ASSIGNED_STAFF");?>
              <input type="checkbox" name="overdue_alert_dept_manager" <?=$config['overdue_alert_dept_manager']?'checked':''?>> <?php print translate("DEPARTMENT_MANAGER");?>
              <input type="checkbox" name="overdue_alert_dept_members" <?=$config['overdue_alert_dept_members']?'checked':''?>> <?php print translate("DEPARTMENT_MEMBERS");?>
            </td>
        </tr>
        <tr><th valign="top"><?php print translate("SYSTEM_ERRORS");?>:</th>
            <td><i><?php print translate("SYSTEM_ERRORS_NOTE");?></i><br>
              <input type="checkbox" name="send_sys_errors" <?=$config['send_sys_errors']?'checked':'checked'?> disabled><?php print translate("SYSTEM_ERRORS");?>
              <input type="checkbox" name="send_sql_errors" <?=$config['send_sql_errors']?'checked':''?>><?php print translate("SQL_ERRORS");?>
              <input type="checkbox" name="send_login_errors" <?=$config['send_login_errors']?'checked':''?>><?php print translate("EXCESSIVE_LOGIN_ATTEMPTS");?>
            </td>
        </tr> 
        
    </table>
 </td></tr>
 <tr>
    <td style="padding:10px 0 10px 240px;">
        <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SAVE");?>'>
        <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
    </td>
 </tr>
 </form>
</table>
