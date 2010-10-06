<font style="font: normal 0.8em Helvetica">
<p>Dear <?= $followed_user->fullname ?></p>
<p><?= anchor('user/view/'.$following_user->id, $following_user->fullname) ?> is now following you on <?= config_item('site_name') ?>. 

<p>The <?= config_item('site_name') ?> Team</p>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', base_url().'user/preferences') ?></small></p>
</font>