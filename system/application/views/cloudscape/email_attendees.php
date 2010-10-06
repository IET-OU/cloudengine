<?php $this->load->view('layout/tinymce.php'); ?>
<h1><?=t("Email attendees of !title", array('!title'=>anchor("cloudscape/view/$cloudscape->cloudscape_id", $cloudscape->title)))?></h1>


<?php if($total_attendees > 0): ?>
<p><?= t('This event has '.plural('!count person', '!count people', $total_attendees).' marked as attending. Submitting this form will send an e-mail to all 
of these attendees who have not opted out of such e-mails') ?></p>
<p><?= t('Please bear in mind that complicated formatting in the message of the email, such as tables, may
not display correctly when the e-mail is sent out.')?></p>

<?= '<b>'.validation_errors().'</b>' ?>
<?=form_open($this->uri->uri_string(), array('id' => 'cloudscape-email-attendees-form'))?>
     <label for="subject"><?= t('Subject !required!') ?></label>
    <input type="text" size="78" name="subject" id="subject" value="<?= $email->subject ?>" />
      <label for="body"><?= t('Message !required!') ?></label>
    <textarea name="body" id="body" cols="60" rows="20"><?= $email->body ?></textarea>
    <p><button type="submit" name="submit" value="Send" class="form-submit"><?=t("Send e-mail")?></button></p>
    <?= form_close(); ?>
<?php else: ?>
    <p><?= t('This event has no attendees yet') ?></p>
<?php endif; ?>