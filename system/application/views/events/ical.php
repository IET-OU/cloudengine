BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//CloudEngine/<?= $this->config->item('site_name') ?>//EN
<?php foreach($events as $event): ?>
BEGIN:VEVENT
DTSTAMP:<?= date('Ymd\THi00', $event->created) ?>

DTSTART:<?= date('Ymd\THi00', $event->start_date) ?>

DTEND:<?= date('Ymd\THi00', $event->end_date) ?>

SUMMARY:<?= ical_escape_text($event->title) ?>

<?php if ($event->summary) {?>
DESCRIPTION:<?= ical_escape_text($event->summary) ?>

<?php } ?>
<?php if ($event->location) {?> 
LOCATION: <?= ical_escape_text($event->location) ?>

<?php } ?>
URL;VALUE=URI:<?= base_url(); ?>cloudscape/view/<?= $event->cloudscape_id ?>

END:VEVENT
<?php endforeach; ?>
END:VCALENDAR