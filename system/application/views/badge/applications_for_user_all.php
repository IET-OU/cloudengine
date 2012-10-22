<h1><?= t("Your Badge Applications") ?></h1>
<h2><?= t("Awarded badges") ?></h2>
<?php if (count($approved_applications) >0 ): ?>
<?php foreach($approved_applications as $application): ?>
<img src="<?= base_url() ?>image/badge/<?= $application->badge_id ?>" alt=""/> 
<p>
<?= anchor('badge/view/'.$application->badge_id, $application->name) ?> 
</p>
<p>
<strong><?= t("Evidence URL") ?>: </strong>
<?= $application->evidence_URL ?>
</p>
<p><?= anchor('badge/application/'.$application->application_id, t("View application status and feedback")) ?>
<p>
<p><?= anchor('badge/issue/'.$application->application_id, t("Add to your Mozilla Backpack")); ?>
<br />    
<?php endforeach; ?>
<?php else: ?>
<p><?= t("You have not been awarded any badges yet") ?>
<?php endif; ?>

<h2><?= t("Pending Applications") ?></h2>
<?php if (count($pending_applications) >0 ): ?>
<?php foreach($pending_applications as $application): ?>
<img src="<?= base_url() ?>image/badge/<?= $application->badge_id ?>" alt

<?= anchor('badge/view/'.$application->badge_id, $application->name) ?>    
<p><?= t("Evidence URL: ") ?>:  
<?= anchor($application->evidence_URL, $application->evidence_URL) ?>
</p>
<p><?= anchor('badge/application/'.$application->application_id, t("View application status and feedback")) ?>
<p>
<?= anchor('badge/delete_application/'.$application->application_id, t('Delete application')) ?>
</p>
<br />
<?php endforeach; ?>
<?php else: ?>
<p><?= t("You have no pending applications.") ?>
<?php endif; ?>

<h2><?= t("Rejected Applications") ?></h2>
<?php if (count($rejected_applications) >0 ): ?>
<?php foreach($rejected_applications as $application): ?>
<img src="<?= base_url() ?>image/badge/<?= $application->badge_id ?>" alt="" style="float: left;"/> 
<p>
<?= anchor('badge/view/'.$application->badge_id, $application->name) ?> 
</p>
<p><?= anchor('badge/application/'.$application->application_id, t("View application status and feedback")) ?>
<p>
<p>
<strong><?= t("Evidence URL") ?>: </strong>
<?= $application->evidence_URL ?>
</p>
<br />      
<?php endforeach; ?>
<?php else: ?>
<p><?= t("You have no rejected applications.") ?>
<?php endif; ?>

            
<p><a href="<?= base_url() ?>badge/badge_list" class="buttonlink"><?=t("Back to all badges")?></a></p>
  


