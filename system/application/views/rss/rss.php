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
    xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title><?php echo xml_convert($feed_name); ?></title>
        <link><?php echo $feed_url; ?></link>
        <description><?php echo xml_convert($page_description); ?></description>
        <dc:language><?php echo $page_language; ?></dc:language>
        <dc:creator><?php echo $creator_email; /*/Translators: In case we want to translate the copyright statement.. */ ?></dc:creator>
        <dc:rights><?=t("Copyright !date !organization", array('!date'=> gmdate("Y", time()), '!organization'=>NULL)) ?></dc:rights>
        <admin:generatorAgent rdf:resource="http://www.codeigniter.com/" />
    
        <?php foreach($clouds as $entry): ?>
            <item>
              <title><?php echo xml_safe(xml_convert($entry->title)); ?></title>
              <link><?php echo base_url().'cloud/view/' . $entry->cloud_id; ?></link>
              <guid><?php echo base_url().'cloud/view/' . $entry->cloud_id; ?></guid>
              <description><![CDATA[<?= $entry->body ?>]]></description>
          <pubDate><?php echo date ('r', $entry->timestamp);?></pubDate>
            </item>   
        <?php endforeach; ?>
    </channel>
</rss>