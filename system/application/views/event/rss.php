<?php // need to be separately enclosed like this
  header("Content-Type: application/rss+xml; charset=".config_item("charset"));
  echo '<?xml version="1.0" encoding="'.config_item("charset").'"?>'.PHP_EOL;
  $this->load->helper('xml');  
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title><?php echo xml_convert($feed_name); ?></title>
    <link><?php echo base_url() ?></link>
	<description><?php echo xml_convert($page_description); ?></description>
	<dc:language><?php echo $page_language; ?></dc:language>
	<dc:creator><?php echo $creator_email; ?></dc:creator>
	<dc:rights><?=t("Copyright !date !organization", array('!date'=>gmdate("Y"), '!organization'=>NULL)) ?></dc:rights>
	<admin:generatorAgent rdf:resource="http://getcloudengine.org/"/>
    <atom:link href="<?php echo $feed_url ?>" rel="self" type="application/rss+xml" />
    <?php if ($events) : ?>
	<?php foreach($events as $event): ?>
	    <item>
	      <title><?php echo xml_safe(xml_convert(strip_tags($event->title))); ?></title>
	      <link><?php echo $event->link ?></link>
	      <guid><?php echo $event->link ?></guid>
	      <description><![CDATA[<?php echo $event->description; ?>]]></description>
	  	  <pubDate><?php echo date ('r', $event->timestamp);?></pubDate>
          <category domain="<?php echo base_url().'#event-'.$event->category ?>"><?php echo $event->category ?></category>
	    </item>  
	<?php endforeach; ?>
    <?php endif; ?>
</channel>
</rss>