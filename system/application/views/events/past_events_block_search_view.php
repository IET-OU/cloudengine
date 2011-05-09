<?php if ($past_events): ?>
<ul class="events">
<?php foreach ($past_events as $event): ?>
<li><a class="cloudscape" href="<?= base_url(); ?>cloudscape/view/<?= $event->cloudscape_id ?>"><?= $event->title ?></a></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>