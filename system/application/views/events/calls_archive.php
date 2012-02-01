<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Past Deadlines")?></h1>
    </div>
</div>

<div id="region1">
<p><a href="<?= base_url(); ?>events/calls"><?=t("Upcoming deadlines")?></a></p>
<?php $month = $first_month; $year = $first_year; ?>
<?php for($i = 0; $i < 80; $i++) { ?>
<?php if ($events[$month][$year]): ?>
<h2><?= format_date('!month-year!', mktime(0, 0, 0, $month, 1, $year)) ?></h2>

<ul class="cloudscapes">
<?php foreach($events[$month][$year] as $event): ?>
<li>
  <?=anchor("cloud/view/$event->cloud_id", format_date('!date!', $event->call_deadline).": $event->title") ?>
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
<h2><?=t("How can I add a deadline?")?></h2>
<p><?=t("To add a deadline to this list, [link-add]create a cloud[/link] for the item with a deadline, then edit the cloud to add the deadline.",
    array('[link-add]' => t_link('cloud/add')))?></p>
</div>
    <div class="box">
<h2><?=t("Events")?></h2>
<p><?=t("You can also view [link-ev]current and upcoming events here[/link] and [link-ep]past events[/link].",
    array('[link-ev]' => t_link('events/view'), '[link-ep]' => t_link('events/events_list_past')))?></p>
</div>
</div>