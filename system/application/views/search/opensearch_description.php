<?php
/**
 * OpenSearch description - cross-browser search plugin.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Search
 */
$site_name = $this->config->item('site_name');
?>
<?='<'?>?xml version="1.0" encoding="utf-8"<?='?'?>>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
                       xmlns:moz="http://www.mozilla.org/2006/browser/search/">
  <ShortName><?=$site_name ?></ShortName>
  <Description><?=t($this->config->item('tag_line')) ?></Description>
  <Contact><?=$this->config->item('site_email') ?></Contact>
  <Image type="image/vnd.microsoft.icon" width="16" height="16"><?=base_url(). $this->config->item('theme_favicon') ?></Image>
  <Url type="text/html" template="<?=site_url('search/result') ?>?q={searchTerms}"/>
  <LongName></LongName>
  <Query role="example" searchTerms="OULDI" />
  <Developer>CloudEngine/OULDI development team at The Open University.</Developer>
  <Attribution>Search data Copyright 2011 <?=$site_name ?>.</Attribution>
  <SyndicationRight>open</SyndicationRight>
  <Language><?=$this->config->item('default_language') ?></Language>
  <InputEncoding>UTF-8</InputEncoding>
  <OutputEncoding>UTF-8</OutputEncoding>
  <moz:SearchForm><?=site_url('search') ?></moz:SearchForm>
</OpenSearchDescription>
