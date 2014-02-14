<font style="font: normal 0.8em Helvetica">
<p><?=t('Dear !fullname,', array('!fullname' => $application->fullname))?></p>

<p><?=t('On this ocassion, you have not been awarded the following badge on !site-name!.')?> 

<img src="<?= site_url('image/badge/'. $application->badge_id) ?>" alt="" style="float: left;"/> 
        <h1><?=$application->name ?></h1>

<p><?=t('Feedback:')?></p>
<?= $feedback ?>


<p><?=t('You can see any feedback on your application [link]here[/link].', array('[link]'=>t_link('badge/application/'.$application->application_id)))?></p>


<p><?=t("The !site-name! Team")?></p>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', site_url('user/preferences')) ?></small></p>
</font>