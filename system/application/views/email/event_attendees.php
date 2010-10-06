<font style="font: normal 0.8em Helvetica">
<p>The following message has been sent via <?= anchor('', config_item('site_name')) ?> by
<?= anchor('user/view/'.$sender_id, $sender->fullname) ?> because you marked that you are attending the 
event <?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title) ?>:<br /><br />

<?= $body ?>

<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', base_url().'user/preferences') ?></small></p>
</font>