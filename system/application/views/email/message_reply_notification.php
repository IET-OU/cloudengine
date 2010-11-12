<font style="font: normal 0.8em Helvetica">
<p>Dear <?= $recipient->fullname ?></p>
<p>You have received a new message on <?= anchor('', config_item('site_name')) ?> from  <?= $message->author->fullname ?> as a reply in the following conversation "<?= $message->thread_subject ?>". </p>
<p>Once logged in you can view the conversation by following this link: <?= anchor('message/thread/' .$message->thread_id, base_url().'message/thread/' .$message->thread_id) ?></p>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', base_url().'user/preferences') ?></small></p>
</font> 