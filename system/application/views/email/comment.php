<font style="font: normal 0.8em Helvetica">
<p>Dear <?= $recipient->fullname ?></p>
<p>The following comment has been posted to the cloud 
<?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?> 
by <?= anchor('user/view/'.$commenter->id, $commenter->fullname) ?>

<?= $comment->body ?>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', base_url().'user/preferences') ?></small></p>
</font>