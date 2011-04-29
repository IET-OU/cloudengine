<?php
/* Javascript for browser search plugin.
   Source: http://mycroft.mozdev.org/developer/hosting.html
   Experimental:  /search?plugin=1
   Supported on:  MSIE 7+, Firefox 2+, Chrome.
*/
if ($this->input->get('plugin')):
?>
<p class="search-plugin">
<input type="hidden" name="plugin" value="1" />
<link
 rel="search" type="application/opensearchdescription+xml"
 title="<?=$this->config->item('site_name') ?>"
 href="<?=site_url('search/opensearch_desc.xml') ?>" />
<script>
function addOpenSearch(name,ext,meth) {
  if ((typeof window.external == "object") && ((typeof window.external.AddSearchProvider == "unknown") || (typeof window.external.AddSearchProvider == "function"))) {
    if ((typeof window.external.AddSearchProvider == "unknown") && meth == "p") {
      alert("This plugin uses POST which is not currently supported by Internet Explorer's implementation of OpenSearch.");
    } else {
      window.external.AddSearchProvider(
        "<?=site_url('search/opensearch_desc.xml')?>");
        //"http://mycroft.mozdev.org/externalos.php/" + name + ".xml");
    }
  } else {
    alert("<?=t('You will need a browser which supports OpenSearch to install this plugin.') ?>");
  }
  return false;
}
</script>
<a role="button" onclick="return addOpenSearch();" href="<?=site_url('search/opensearch_desc.xml') ?>" title=
 "<?=t('Add the !site-name! search plugin to your browser') ?>" style="display:inline-block; background:#D63333; color:#eee; padding:4px; margin:4px 0; --moz-appearance:button;"
 ><?=t('Add browser search plugin') ?></a>
<br /><small>(Supported in Internet Explorer 7+, Firefox and Chrome.)</small>
</p>
<? endif; ?>
