<?php // need to be separately enclosed like this
  header("Content-Type: application/rss+xml; charset=".config_item("charset"));
  echo '<?xml version="1.0" encoding="'.config_item("charset").'"?>'.PHP_EOL;
  $this->load->helper('xml');
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:ev="http://purl.org/rss/1.0/modules/event/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:foaf="http://xmlns.com/foaf/0.1/">
<channel>
    <title><?= xml_convert($feed_name); ?></title>
    <link><?= base_url() ?></link>
	<description><?= xml_convert($page_description); ?></description>
	<dc:language><?= $page_language; ?></dc:language>
	<dc:creator><?= $creator_email; ?></dc:creator>
	<dc:rights><?=t("Copyright !date !organization", array('!date'=>gmdate("Y"), '!organization'=>NULL)) ?></dc:rights>
	<admin:generatorAgent/>
    <atom:link href="<?= $feed_url ?>" rel="self" type="application/rss+xml" />

	<?php foreach($events as $event):
	    $link = isset($event->cloud_id) ? site_url('cloud/view/'.$event->cloud_id)
	        : site_url('cloudscape/view/'.$event->cloudscape_id); ?>

	<item>
	      <title><?= xml_safe(xml_convert(strip_tags($event->title))); ?></title>
	      <link><?= $link ?></link>
	      <guid><?= $link ?></guid>
	      <description><![CDATA[<?= $event->body ?>]]></description>
          <pubDate><?= date('D, d M Y H:i:s O', safe_date($event->created)) ?></pubDate>
<?php if (isset($event->location)): ?>          <ev:location><?=
    xml_safe(xml_convert(strip_tags($event->location))) ?></ev:location><?php endif; ?>

          <ev:startdate><?= date('Y-m-d', isset($event->event_date) ?
            $event->event_date : $event->start_date) ?></ev:startdate>
<?php if (isset($event->end_date)): ?>          <ev:enddate><?=
    date('Y-m-d', $event->end_date);?></ev:enddate><?php endif; ?>
<?php if (isset($event->fullname)): ?>

          <foaf:made rdf:resource="<?= $link ?>" />
          <foaf:name><?=$event->fullname ?></foaf:name>
          <foaf:mbox_sha1sum><?=mbox_sha1sum($event->email) ?></foaf:mbox_sha1sum>
<?php endif; ?>
        </item>
	<?php endforeach; ?>

</channel>
</rss>
