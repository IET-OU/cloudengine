<?php if ($is_admin) : ?>
<h1><?= t("Badge Application for '!name' Badge ", array('!name'=>$application->name)) ?></h1>
<?php else: ?>
<h1><?= t("Your Application for '!name' Badge ", array('!name'=>$application->name)) ?></h1>
<?php endif; ?>
<img src="<?= base_url() ?>image/badge/<?= $application->badge_id ?>" alt="" />

<?= anchor('badge/view/'.$application->badge_id, $application->name) ?>   

<p><strong><?= t("Applicant") ?>: </strong>
<?= anchor('user/view/'.$application->user_id, $application->fullname) ?>
</p>
<p><?= t("Evidence URL") ?>:  
<?= anchor($application->evidence_URL, $application->evidence_URL) ?>
</p>
<p><strong><?= t('Application status') ?>: <?= $application->status ?>
</p>
<p>
<?= anchor('badge/delete_application/'.$application->application_id, t('Delete application')) ?>
</p>
<h2><?= t("Decisions and feedback on this badge") ?></h2>
<?php if (count($decisions) > 0): ?>
<?php foreach($decisions as $decision): ?>
<h3> <?= t("Decision by !fullname", array('!fullname' => $decision->fullname)) ?></h3>
<p><strong><?= t("Decision") ?>: <?= $decision->decision ?></strong></p> 
<?= $decision->feedback ?> 
<br />
<?php endforeach; ?>
<?php else: ?>
<p>
<?= t("There have been no decisions made on the awarding of this badge yet") ?>
</p>
<?php endif; ?>


