

<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" />

<link
  rel="search" title="<?= $this->config->item('site_name') ?> search"
  href="<?=base_url() ?>/search/opensearch_desc.xml"
  type="application/opensearchdescription+xml"/>


<script>
  (function() {
    var cx = '<?= config_item('x_google_site_search_cx') ?>';
    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  })();
</script>
<div
 class="gcse-search"
 data-enableAutoComplete="true"
 data-resultSetSize="large"
 data-linkTarget=""
></div>




