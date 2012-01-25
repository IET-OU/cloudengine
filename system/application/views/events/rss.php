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
    xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?= xml_convert($feed_name); ?></title>
    <link><?= base_url() ?></link>
	<description><?= xml_convert($page_description); ?></description>
	<dc:language><?= $page_language; ?></dc:language>
	<dc:creator><?= $creator_email; ?></dc:creator>
	<dc:rights><?=t("Copyright !date !organization", array('!date'=>gmdate("Y"), '!organization'=>NULL)) ?></dc:rights>
	<admin:generatorAgent rdf:resource="http://getcloudengine.org/"/>
    <atom:link href="<?= $feed_url ?>" rel="self" type="application/rss+xml" />

	<?php foreach($events as $event): ?>
    
	    <item>
	      <title><?= xml_safe(xml_convert(strip_tags($event->title))); ?></title>
	      <link><?= base_url().'cloudscape/view/'.$event->cloudscape_id  ?></link>
	      <guid><?= base_url().'cloudscape/view/'.$event->cloudscape_id  ?></guid>
	      <description><![CDATA[<?= $event->body ?>]]></description>
	  	  <pubDate><?= date('D, d M Y H:i:s O', strtotime($event->created)) ?></pubDate>
          <ev:location><?= xml_safe(xml_convert(strip_tags($event->location))) ?></ev:location>
          <ev:startdate><?= date('Y-m-d', $event->start_date);?></ev:startdate>
          <ev:enddate><?= date('Y-m-d', $event->end_date);?></ev:enddate>
        </item>  
	<?php endforeach; ?>
</channel>
</rss>