<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Past workshops, seminars and talks")?></h1>
    </div>
</div>

<div id="region1">
<p><a href="<?= base_url(); ?>events/cloud_events"><?=t("Upcoming orkshops, seminars and talks")?></a></p>
<?php $month = $first_month; $year = $first_year; ?>
<?php for($i = 0; $i < 80; $i++) { ?>
<?php if ($events[$month][$year]): ?>
<h2><?= format_date('!month-year!', mktime(0, 0, 0, $month, 1, $year)) ?></h2>

<ul class="cloudscapes">
<?php foreach($events[$month][$year] as $event): ?>
<li>
  <?=anchor("cloud/view/$event->cloud_id", format_date('!date!', $event->event_date).": $event->title") ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php $month--; if ($month == 0) { $month = 12; $year--; } ?>
<?php } ?>
</div> 


</div>