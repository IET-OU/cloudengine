<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Current and Upcoming Events")?></h1>
    </div>
</div>

<div id="region1">

<?php $month = $current_month; $year = $current_year; ?>
<?php for($i = 0; $i < 12; $i++) { ?>
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
<?php $month++; if ($month == 13) { $month = 1; $year++; } ?>
<?php } ?>
<p>
<span class="ical"><?= anchor('events/ical', t("iCal")) ?></span>
&nbsp;
<span class="rss"><?= anchor('events/rss', t("RSS feed")) ?></span>
</p>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <div class="box">
<h2><?=t("How can I add an event?")?></h2>
<p><?=t("To add an event to this list, [link-add]create a cloudscape[/link] for the event and add the dates and location when you create the cloudscape.",
    array('[link-add]' => t_link('cloudscape/add')))?> </p>


<h2><?=t("Past events")?></h2>
<p><?=t("You can [link-ep]view past events here[/link].", array('[link-ep]' => t_link('events/events_list_past')))?></p>

<h2><?=t("Upcoming Deadlines")?></h2>
<p><?=t("You can [link-ec]view deadlines here[/link].", array('[link-ec]' => t_link('events/calls')))?></p>
</div>
</div>