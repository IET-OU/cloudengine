
<?php if ($cloudscape->start_date && $attendees): ?>
<div class="avatars">
<h2><?php if ($past_event): ?>Attended<?php else: ?>Attending<?php endif; ?> (<?= count($attendees) ?>)</h2>
    <?php  foreach (array_slice($attendees, 0, 100) as $attendee):?>
    	   <?php if ($attendee->picture): ?>
                <a href="<?= base_url() ?>user/view/<?= $attendee->id ?>" title="<?= $attendee->fullname ?>">
           <img src="<?=base_url() ?>image/user_16/<?= $attendee->id ?>" alt="<?= $attendee->fullname ?>" class="go2" /> </a>
            
            <?php endif; ?>
           
          
    <?php endforeach; ?>
        <p><a href="<?= base_url() ?>cloudscape/attendees/<?= $cloudscape->cloudscape_id ?>"><?=t("View all attendees")?></a></p>
</div>
<?php endif; ?>
