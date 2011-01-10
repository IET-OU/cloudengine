<?php // need to be separately enclosed like this
  header("Content-Type: application/rss+xml; charset=".config_item("charset"));
  echo '<?xml version="1.0" encoding="'.config_item("charset").'"?>'.PHP_EOL;
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">

    <channel>

    <title><?php echo $feed_name; ?></title>

    <link><?php echo $feed_url; ?></link>
    <description><?php echo $page_description; ?></description>
    <dc:language><?php echo $page_language; ?></dc:language>
    <dc:creator><?php echo $creator_email; ?></dc:creator>

    <dc:rights><?=t("Copyright !date !organization", array('!date'=>gmdate("Y"), '!organization'=>NULL)) ?></dc:rights>
    <admin:generatorAgent rdf:resource="http://getcloudengine.org/"/>

    <?php foreach($news as $entry): ?>
    
        <item>
          <title><?php echo xml_convert($entry->title); ?></title>
          <link><?php echo base_url().'blog/view/' . $entry->post_id; ?></link>
          <guid><?php echo base_url().'blog/view/' . $entry->post_id; ?></guid>
          <description><![CDATA[<?= $entry->body ?>]]></description>
      <pubDate><?php echo date ('r', $entry->created);?></pubDate>
        </item>
        
    <?php endforeach; ?>

    </channel></rss>  