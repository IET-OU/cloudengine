<font style="font: normal 0.8em Helvetica">
<p>Dear <?= $application->fullname ?></p>


<p>You have been awarded the following badge:on <?= config_item('site_name') ?>. 

<img src="<?= base_url() ?>image/badge/<?= $application->badge_id ?>" alt="" style="float: left;"/> 
        <h1><?= t('Badge: ') ?><?=$application->name ?></h1>

<p>You can see feedback on your application <?= anchor('badge/application'.$application->application_id, 'here') ?></p>.

<p>The <?= config_item('site_name') ?> Team</p>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', base_url().'user/preferences') ?></small></p>
</font>