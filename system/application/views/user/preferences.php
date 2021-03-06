<h1><?=t("Preferences")?></h1>
<?php if ($message): ?>
	<h3><?= $message ?></h3>
<?php endif; ?>

<?= form_open($this->uri->uri_string());?>
<table>
  <tr>
    <th></th>
    <th><?=t("On")?></th>
    <th><?=t("Off")?></th>
 </tr>

<?php $rows = array(
  'email_follow'           => t("Notify me by e-mail when somebody follows me"),
  'email_comment'          => t("Notify me by e-mail when someone comments on one of my clouds"),
  'email_comment_followup' => t("Notify me by e-mail when someone comments on a cloud on which I have already commented"),  
  'email_news'             => t("Receive occasional e-mails from the !site-name! team")
  );

  if ($this->config->item('x_message')) {
    $rows['email_message_notify'] = t("Notify me by e-mail when I receive a new message");
  }

  if ($this->config->item('x_email_events_attending')) {
    $rows['email_events_attending'] =  t("Allow organisers of events I have marked that I am attending to e-mail me");
  }
  
  $rows['display_email' ]    = t("Display my e-mail address on my profile"); 
  ///Translators: WYSIWYG, what you see is what you get/ rich-text editor.
  $rows['do_not_use_editor'] = t('Do not use a <abbr>WYSIWYG</abbr> text editor',
                                array('<abbr>' => '<abbr title="'.t('What you see is what you get').'">'));
  ?>


<?php foreach ($rows as $key => $label): ?>  
    <tr>
        <td><?=$label ?></td>
        <?php $data = array('name' => $key, 'id' => $key."-1", 'value'  => "1", 
                            'checked'=> $profile->{$key}, ); ?>
        <td>
            <!-- Accessibility: hidden labels -->
            <?= form_radio($data); ?>
            <?= form_label(t("On"), $data['id'], array('class'=>'accesshide')) ?>
        </td>
        <td>
            <?php $data['id']   = $key."-0";
                  $data['value']= "0";
                  $data['checked']= !$data['checked'];
            ?>
            <?= form_radio($data) ?>
            <?= form_label(t("Off"), $data['id'], array('class'=>'accesshide')) ?>
        </td>
  </tr>
<?php endforeach; ?>
</table>
<p><button type="submit" name="submit" class="submit" value="Save"><?=t("Save")?></button></p>
</form>