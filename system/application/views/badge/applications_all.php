<div id="region1">
<h1><?= t("All Badge Applications") ?></h1>
<p><a href="#awarded-badges"><?= t('Awarded badges') ?></a>
 | <a href="#pending-applications"><?= t('Pending applications') ?></a>
 | <a href="#rejected-applications"><?= t('Rejected applications') ?></a></p>

<h2 id="awarded-badges"><?= t("Awarded badges") ?></h2>
<?php if (count($approved_applications) >0 ): ?>
<?php foreach($approved_applications as $application): ?>
<img src="<?= site_url('image/badge/'. $application->badge_id) ?>" alt=""/> 
<p>
<?= anchor('badge/view/'.$application->badge_id, $application->name) ?> 
</p>
<p><strong><?= t("Applicant") ?>: </strong>
<?= anchor('user/view/'.$application->user_id, $application->fullname) ?>
</p>
<p>
<strong><?= t("Evidence URL") ?>: </strong>

<?= $application->evidence_URL ?>
</p>
<p><?= anchor('badge/application/'.$application->application_id, t("View application status and feedback")) ?>
 | <span class="time" datetime="<?= date('c', $application->created) ?>"
    >Created <?=format_date('!date-time-abbr!', $application->created) ?></span>
 | <span class="time" datetime="<?= date('c', $application->issued) ?>"
    >Issued <?=format_date('!date-time-abbr!', $application->issued) ?></span><?php /* #323, Should be HTML5 <time> element. */ ?>
</p>
<br />    
<?php endforeach; ?>
<?php else: ?>
<p><?= t("You have not been awarded any badges yet") ?>
<?php endif; ?>

<h2 id="pending-applications"><?= t("Pending Applications") ?></h2>
<?php if (count($pending_applications) >0 ): ?>
<?php foreach($pending_applications as $application): ?>
<img src="<?= site_url('image/badge/'. $application->badge_id) ?>" alt=""/>

<?= anchor('badge/view/'.$application->badge_id, $application->name) ?> 
<p><strong><?= t("Applicant") ?>: </strong>
<?= anchor('user/view/'.$application->user_id, $application->fullname) ?>
</p>   
<p><?= t("Evidence URL: ") ?>:  
<?= anchor($application->evidence_URL, $application->evidence_URL) ?>
</p>
<p><?= anchor('badge/application/'.$application->application_id, t("View application status and feedback")) ?>
 | <span class="time" datetime="<?= date('c', $application->created) ?>"
    >Created <?=format_date('!date-time-abbr!', $application->created) ?></span><?php /* #323, Should be HTML5 <time> element. */ ?>
<p>
<?= anchor('badge/delete_application/'.$application->application_id, t('Delete application')) ?>
</p>
<br />
<?php endforeach; ?>
<?php else: ?>
<p><?= t("You have no pending applications.") ?>
<?php endif; ?>

<h2 id="rejected-applications"><?= t("Rejected Applications") ?></h2>
<?php if (count($rejected_applications) >0 ): ?>
<?php foreach($rejected_applications as $application): ?>
<img src="<?= site_url('image/badge/'. $application->badge_id) ?>" alt="" style="float: left;"/> 
<p>
<?= anchor('badge/view/'.$application->badge_id, $application->name) ?> 
</p>
<p><?= anchor('badge/application/'.$application->application_id, t("View application status and feedback")) ?>
 | <span class="time" datetime="<?= date('c', $application->created) ?>"
    >Created <?=format_date('!date-time-abbr!', $application->created) ?></span><?php /* #323, Should be HTML5 <time> element. */ ?>
</p>
<p><strong><?= t("Applicant") ?>: </strong>
<?= anchor('user/view/'.$application->user_id, $application->fullname) ?>
</p>
<p>
<strong><?= t("Evidence URL") ?>: </strong>
<?= $application->evidence_URL ?>
</p>
<br />      
<?php endforeach; ?>
<?php else: ?>
<p><?= t("You have no rejected applications.") ?>
<?php endif; ?>

            
<p><a href="<?= site_url('badge/badge_list') ?>" class="buttonlink"><?=t("Back to all badges")?></a></p>
  
</div>
<div id="region2">
<div class="box">
<h2><?= t("What if my badge application is rejected?") ?></h2>
<p>
<?= t("If your badge application has been rejected, you should have received 
feedback to let you know what you need to do to improve your application. You 
can reapply as many times as you need to.") ?>
</p>
<h2>
<?= t("What happens when I am awarded a badge?") ?>
</h2>
<p>
<?= t("You can add any badge you have been awarded to your [link-backpack]Mozilla Open Badge Backpack[/link] 
if you have one or want to set one up.",
 array('[link-backpack]'=>t_link(BADGE_BACKPACK_URL, FALSE))) ?>
<?= t(
'You will need to use the same email address when you set up your Backpack as you used to set up your Cloudworks profile.') ?>

</p>
<h2><?= t("Displaying your badge") ?></h2>
<p><?= t("Your badge will automatically be displayed on your Cloudworks profile 
page. You can also add your badge to your [link-backpack]Mozilla Open Badge Backpack[/link]. 
Mozilla Open Badges is quite a new concept but in the future there are likely to be far more places you 
can display your badges",
 array('[link-backpack]'=>t_link(BADGE_BACKPACK_URL, FALSE))) ?>
</p>
</div>

</div>



