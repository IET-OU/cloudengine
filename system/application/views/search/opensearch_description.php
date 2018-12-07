<?php
/**
 * OpenSearch description XML - cross-browser search plugin.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Search
 */

$site_name = config_item('site_name');

?>
<?='<'?>?xml version="1.0" encoding="utf-8"<?='?'?>>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
  <ShortName><?=$site_name ?></ShortName>
  <Description><?= config_item('tag_line') ?></Description>
  <Contact><?= config_item('site_email') ?></Contact>
  <Image type="image/x-icon" width="16" height="16"><?= base_url() . config_item('theme_favicon') ?></Image>
  <Url rel="results" type="text/html" template="<?=site_url('search/result') ?>?q={searchTerms}"/>
  <LongName></LongName>
  <Query role="example" searchTerms="OULDI" />
  <Developer>IET / OULDI development team at The Open University (https://iet.open.ac.uk)</Developer>
  <Attribution>Search data Copyright 2009-<?= date('Y') ?> The Open University and Web-site contributors.</Attribution>
  <SyndicationRight>open</SyndicationRight>
  <Language><?= config_item('default_language') ?></Language>
  <InputEncoding>utf-8</InputEncoding>
  <OutputEncoding>utf-8</OutputEncoding>
  <moz:SearchForm><?=site_url('search') ?></moz:SearchForm>
</OpenSearchDescription>
