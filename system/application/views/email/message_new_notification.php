<font style="font: normal 0.8em Helvetica">
<p>Dear <?= $recipient->fullname ?></p>
<p>You have received a new message on Cloudworks from  <?= $thread->author->fullname ?> titled "<?= $thread->subject ?>". </p>
<p>Once logged in you can view the new message by following this link: <?= anchor('message/thread/' .$thread->thread_id, base_url().'message/thread/' .$thread->thread_id) ?></p>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', base_url().'user/preferences') ?></small></p>
</font> 