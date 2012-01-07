<?php
if(!defined('OSTADMININC') || !$thisuser->isadmin()) die(print translate("TEXT_ACCESS_DENIED"));
//Get the config info.
$config=($errors && $_POST)?Format::input($_POST):$cfg->getConfig();
?>
<table width="100%" border="0" cellspacing=0 cellpadding=0>
    <form action="admin.php?t=attach" method="post">
    <input type="hidden" name="t" value="attach">
    <tr>
      <td>
        <table width="100%" border="0" cellspacing=0 cellpadding=2 class="tform">
          <tr class="header">
            <td colspan=2>&nbsp;<?php print translate("ATTACHMENT_SETTINGS");?></td>
          </tr>
          <tr class="subheader">
            <td colspan=2">
                <?php print translate("ATTACHMENTS_NOTE");?></td>
          </tr>
          <tr>
            <th width="165"><?php print translate("ALLOW_ATTACHMENTS");?>:</th>
            <td>
              <input type="checkbox" name="allow_attachments" <?=$config['allow_attachments'] ?'checked':''?>>
                &nbsp;<font class="error">&nbsp;<?=$errors['allow_attachments']?></font>
            </td>
          </tr>
          <tr>
            <th><?php print translate("EMAILED_ATTACHMENTS");?>:</th>
            <td>
                <input type="checkbox" name="allow_email_attachments" <?=$config['allow_email_attachments'] ? 'checked':''?> > <?php print translate("ACCEPT_EMAILED_ATTACHMENTS");?>
                    &nbsp;<font class="warn">&nbsp;<?=$warn['allow_email_attachments']?></font>
            </td>
          </tr>
         <tr>
            <th><?php print translate("ONLINE_ATTACHMENTS");?>:</th>
            <td>
                <input type="checkbox" name="allow_online_attachments" <?=$config['allow_online_attachments'] ?'checked':''?> >
                    <?php print translate("ONLINE_ATTACHMENTS_NOTE");?><br/>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="allow_online_attachments_onlogin" <?=$config['allow_online_attachments_onlogin'] ?'checked':''?> >
                    <?php print translate("ONLINE_ATTACHMENTS_AUTHENTICATED_USERS_ONLY");?>
                    <font class="warn">&nbsp;<?=$warn['allow_online_attachments']?></font>
            </td>
          </tr>
          <tr>
            <th><?php print translate("STAFF_RESPONSE_FILES");?>:</th>
            <td>
                <input type="checkbox" name="email_attachments" <?=$config['email_attachments']?'checked':''?> ><?php print translate("EMAIL_ATTACHMENTS_TO_USER");?>
            </td>
          </tr>
          <tr>
            <th nowrap><?php print translate("MAXIMUM_FILE_SIZE");?>:</th>
            <td>
              <input type="text" name="max_file_size" value="<?=$config['max_file_size']?>"> <i><?php print translate("BYTES");?></i>
                <font class="error">&nbsp;<?=$errors['max_file_size']?></font>
            </td>
          </tr>
          <tr>
            <th><?php print translate("ATTACHMENT_FOLDER");?>:</th>
            <td>
                <?php print translate("ATTACHMENT_FOLDER_NOTE");?> &nbsp;<font class="error">&nbsp;<?=$errors['upload_dir']?></font><br>
              <input type="text" size=60 name="upload_dir" value="<?=$config['upload_dir']?>"> 
              <font color=red>
              <?=$attwarn?>
              </font>
            </td>
          </tr>
          <tr>
            <th valign="top"><br/><?php print translate("ACCEPTED_FILE_TYPES");?>:</th>
            <td>
	    <?php print translate("FILE_EXTENSION_NOTES");?>
                <textarea name="allowed_filetypes" cols="21" rows="4" style="width: 65%;" wrap=HARD ><?=$config['allowed_filetypes']?></textarea>
            </td>
          </tr>
        </table>
    </td></tr>
    <tr><td style="padding:10px 0 10px 200px">
        <input class="button" type="submit" name="submit" value='<?php print translate("LABEL_SAVE");?>'>
        <input class="button" type="reset" name="reset" value='<?php print translate("LABEL_RESET");?>'>
    </td></tr>
  </form>
</table>
