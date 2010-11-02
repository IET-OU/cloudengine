<div class="grid">
    <h2 id="events"><?=t('Events') ?></h2>

    <ul class="cloudstream-filter">
    <li><?php if ($month == $current_month): ?><strong><?=
        format_date(_("!month!"), mktime(0, 0, 0, $current_month, 1)) ?></strong>
    <?php else: ?>
    <?= anchor('/'.$current_month.'/'.$popular_type.'#events',
        format_date(_("!month!"), mktime(0, 0, 0, $current_month, 1))) ?>
    <?php endif; ?></li>
    <li><?php if ($month == $current_month + 1): ?><strong><?=
        format_date(_("!month!"), mktime(0, 0, 0, $current_month + 1, 1)) ?></strong>
    <?php else: ?>
    <?= anchor('/'.($current_month + 1).'/'.$popular_type.'#events',
        format_date(_("!month!"), mktime(0, 0, 0, $current_month + 1, 1))) ?>
    <?php endif; ?></li>
    <li><?php if ($month == $current_month + 2): ?><strong><?=
        format_date(_("!month!"), mktime(0, 0, 0, $current_month + 2 , 1)) ?></strong>
    <?php else: ?>
    <?= anchor('/'.($current_month + 2).'/'.$popular_type.'#events',
        format_date(_("!month!"), mktime(0, 0, 0, $current_month + 2, 1))) ?>
    <?php endif; ?></li>
    <li><?= anchor('events/events_list', t("All").' &#8250;') ?></li>
    </ul>

</div>

<div class="grid">
<ul class="events">
<?php foreach($events as $event): ?>
 <?php $link_text = format_date("!date!", $event->start_date);
    if ($event->end_date && ($event->end_date != $event->start_date)) {
      $link_text .= format_date(" - !date!", $event->end_date);
    } ?>
<li><?= anchor('cloudscape/view/'.$event->cloudscape_id, '<em>'.$link_text.'</em> <br />'.$event->title) ?></li>
<?php endforeach; ?>
</ul>
</div>

