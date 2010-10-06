<h1><?=t("Email sent to attendees of !title", array('!title'=>anchor("cloudscape/view/$cloudscape->cloudscape_id", $cloudscape->title)))?></h1>

<p><?= t('Your email has been successfully sent') ?></p>
<p><?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, t('Return to cloudscape')); ?></p>