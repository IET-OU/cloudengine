<?php
  $site_name = $this->config->item('site_name');
  if ($debug) {
    header("Content-Type: text/plain; charset=".config_item("charset"));
  } else {
    header("Content-Type: text/calendar; charset=".config_item("charset"));
  }
  @header("Content-Disposition: inline; filename=".
    str_replace(' ','-', strtolower($site_name)) ."-$view-calendar.ics");
?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//CloudEngine/<?= $site_name ?>//EN
X-WRCALNAME:<?= ical_escape_text(t('!site-name! events')) ?>

<?php foreach($events as $event): ?>
<?php
  #var_dump($event); exit; ?>

BEGIN:VEVENT
<?php //Bug #206, Clouds use a Unix epoch, Cloudscapes use SQL datetime :(. ?>
DTSTAMP:<?= date('Ymd\THi00',
  /*isset($event->cloudscape_id) ?*/
  preg_match('/^\d{4}-\d{2}/', $event->created) ? strtotime($event->created) : $event->created) ?>

DTSTART:<?= date('Ymd\THi00',
  isset($event->event_date) ? $event->event_date : $event->start_date) ?>
<?php if (isset($event->end_date) && $event->end_date): ?>

DTEND:<?= date('Ymd\THi00', $event->end_date) ?>
<?php endif; ?>

SUMMARY:<?= ical_escape_text($event->title) ?>

<?php if (isset($event->cloudscape_id)): ?>
DESCRIPTION:<?php if ($event->summary): ?><?=
  ical_escape_text($event->summary) ?>\n\n<?php endif; ?><?=ical_escape_text(t('View on !site-name!')) ?>: <?=
  site_url('cloudscape/view/'. $event->cloudscape_id) ?>
<?php else: ?>
DESCRIPTION:<?=ical_escape_text($event->body) ?>\n\n<?=
  ical_escape_text(t('View on !site-name!')) ?>: <?= site_url('cloud/view/'. $event->cloud_id) ?>
<?php endif; ?>
<?php if ($event->location): ?> 
LOCATION:<?= ical_escape_text($event->location) ?>
<?php endif; ?>

URL;VALUE=URI:<?= isset($event->cloud_id) ? site_url('cloud/view/'.$event->cloud_id) 
  : site_url('cloudscape/view/'. $event->cloudscape_id) ?>

<?php /*X-ORGANIZER:<NAME user=<?= $event->user_id ?> > (author):MAILTO:<?=str_replace('@', '-noreply@', config_item('site_email'))*/ ?>
<?php if ($extended && isset($event->email)): ?>
X-CREATOR;CN=unknown:MBOX-SHA1SUM:<?=mbox_sha1sum($event->email) ?>

<?php endif; ?>
END:VEVENT

<?php endforeach; ?>
END:VCALENDAR