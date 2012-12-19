<h1><?= t("Applications for !name badge", array('!name'=>$badge->name)) ?></h1>
<h2><?= t("Criteria for this badge") ?></h2>
<?= $badge->criteria ?>

<?php if (count($applications) >0): ?>

<?php $application_number = 1 ?>
<?php foreach($applications as $application): ?>
<?php if ($application->user_id != $user_id): ?>
<h2><?= t("Application !application_number", array('!application_number'=>$application_number)) ?></h2>
<ul class="arrows">
<li><strong><?= t("Submitted by:") ?></strong> <?= anchor('user/view'.$application->user_id, 
$application->fullname) ?></li>
<li><strong><?= t("Evidence:") ?></strong><?= anchor($application->evidence_URL, $application->evidence_URL); ?>
</ul>
</li>
  <?='<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'application-approve-form-'.$application->application_id))?>
<input type="hidden" id="application_id" name="application_id" value="<?= $application->application_id ?>" />
<fieldset>
<legend>Decision:</legend>
<input type="radio" name="decision" id="accept" value="approved"><label class="radio" for="approve" /><?= t("Approve") ?></label>
<input type="radio" name="decision" id="reject" value="rejected"><label class="radio" for="reject" /><?= t("Reject") ?></label>
</fieldset>
<p>
<label for="body"><?=t("Feedback")?>:</label>
<textarea cols="117" rows="3" name="feedback" id="feedback"></textarea>
</p>
<p><button type="submit" name="submit" class="submit" value="Submit"><?=t("Submit")?></button></p>
<?php form_close(); ?>
<?php $application_number++ ?>

<?php endif; ?>
<?php endforeach; ?>
<?php else: ?>
<h2><?= t("Applications") ?></h2>
<p><?= t("No more pending applications for this badge.") ?></p>
<p><?= anchor('badge/applications', t("Back to all pending applications."), array('class' =>'buttonlink')) ?>
<?php endif;?>