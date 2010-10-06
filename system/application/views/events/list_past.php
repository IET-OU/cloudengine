<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Past Events")?></h1>
    </div>
</div>

<div id="region1">

<?php $month = $first_month; $year = $first_year; ?>
<?php for($i = 0; $i < 80; $i++) { ?>
<?php if ($events[$month][$year]): ?>
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
    $link_text .= '</em><br/>';
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
<?php $month--; if ($month == 0) { $month = 12; $year--; } ?>
<?php } ?>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>

    <div class="box">
<h2><?=t("How can I add an event?")?></h2>
<p><?=t("To add an event to this list, [link-add]create a cloudscape[/link] for the event and add the dates and location when you create the cloudscape.",
    array('[link-add]' => t_link('cloudscape/add')))?></p>
</div>
    <div class="box">
<h2><?=t("Current and upcoming events")?></h2>
<p><?=t("You can [link-ev]view current and upcoming events here[/link].",
    array('[link-ev]' => t_link('events/events_list')))?></p>
</div>
</div>