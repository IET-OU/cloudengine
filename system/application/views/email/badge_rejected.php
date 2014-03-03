<style>
.cloudengine-email { font: 0.95em Helvetica,sans-serif; }
.cloudengine-email div,
.cloudengine-email p { margin: 1.1em 0 !important; }
.cloudengine-email h1{ font-size: 1.6em; }
.cloudengine-email blockquote { margin: 1.1em; }
</style>
<div class="cloudengine-email">
<p><?=t('Dear !fullname,', array('!fullname' => $application->fullname))?></p>

<p><?=t('On this occasion, you have not been awarded the following badge on !site-name!.')?></p>

<p><img src="<?= site_url('image/badge/'. $application->badge_id) ?>" alt="" /></p>

        <h1><?=$application->name ?></h1>

<p><?=t('Feedback:')?></p>
<blockquote>
<?= $feedback ?>
</blockquote>

<p><?=t('Visit !site-name! to [link]review your application[/link].', array('[link]'=>t_link('badge/application/'.$application->application_id)))?></p>

<p><?=t("The !site-name! Team")?></p>
<p><small>You can turn off these e-mails at 
<?= anchor('user/preferences', site_url('user/preferences')) ?></small></p>
</div>
