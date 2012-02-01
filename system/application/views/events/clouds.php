
<?php $month = $first_month; $year = $first_year; ?>
<?php  $no_months = 12; if ($archive) { $no_months = 80;} ?>
<?php $event_exists = FALSE; ?>
<?php for($i = 0; $i < $no_months; $i++) { ?>
<?php if ($events[$month][$year]): ?>
<?php $event_exists = TRUE; ?>
<h2><?= format_date('!month-year!', mktime(0, 0, 0, $month, 1, $year)) ?></h2>

<ul class="clouds">
<?php foreach($events[$month][$year] as $event): ?>
<li>
  <?=anchor("cloud/view/$event->cloud_id", format_date('!date!', $event->event_date).": $event->title") ?>
</li>
<?php endforeach; ?>
</ul>

<?php endif; ?>
<?php if ($archive) {
        $month--; if ($month == 0) { $month = 12; $year--; }
      } else {
        $month++; if ($month == 13) { $month = 1; $year++; } 
      }
?>
<?php } ?>

<?php if (!$event_exists): ?>
<p><?= t('No workshops, seminars or talks to display.') ?>
<?php endif; ?>

<?php if (!$archive): ?>
<p>
<span class="ical"><?= anchor('events/ical/clouds', t("iCal")) ?></span>
&nbsp;
<span class="rss"><?= anchor('events/rss/clouds', t("RSS feed")) ?></span>
</p>

<?php endif; ?>
