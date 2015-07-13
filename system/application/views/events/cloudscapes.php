<?php $month = $first_month; $year = $first_year; ?>
<?php  $no_months = 12; if ($archive) { $no_months = 80;} ?>
<?php $event_exists = FALSE; ?>
<?php for($i = 0; $i < $no_months; $i++) { ?>
<?php if ($events[$month][$year]): ?>
<?php $event_exists = TRUE; ?>
<h2><?= format_date('!month-year!', mktime(0, 0, 0, $month, 1, $year)) ?></h2>

<ul class="events">
<?php foreach($events[$month][$year] as $event): ?>
<li>
  <?php
    $link_text = '<em>';
    $link_text .= format_date("!date!", $event->start_date);
    if ($event->end_date && ($event->end_date != $event->start_date)) {
      $link_text .= format_date(" - !date!", $event->end_date);
    }
    $link_text .= '</em><br />';
    $link_text .= $event->title;
    if ($event->location) {
      $link_text .= ", ".$event->location;
    }
  ?>
  <?=anchor("cloudscape/view/$event->cloudscape_id", $link_text) ?>
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
<p><?= t('No conferences to display.') ?>
<?php endif; ?>
<?php if (!$archive): ?>
<p>
<span class="ical"><?= anchor('events/ical', t("iCal")) ?></span>
&nbsp;
<span class="rss"><?= anchor('events/rss', t("RSS feed")) ?></span>
</p>
<?php endif; ?>
