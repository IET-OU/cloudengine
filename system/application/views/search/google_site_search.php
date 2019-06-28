

<link rel="stylesheet" href="https://www.google.com/cse/style/look/default.css" type="text/css" />

<link
  rel="search" title="<?= $this->config->item('site_name') ?> search"
  href="<?=base_url() ?>/search/opensearch_desc.xml"
  type="application/opensearchdescription+xml"/>


<script>
  (function() {
    var cx = '<?= config_item('x_google_site_search_cx') ?>';
    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
    gcse.src = 'https://cse.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  })();
</script>


<div class="grid headline">
  <div class=c1of2 >
    <h1><label for=gsc-i-id1 ><?= $title ?></label></h1>
  </div>
</div>


<div
 class="gcse-search"
 data-enableAutoComplete="true"
 data-resultSetSize="large"
 data-linkTarget=""
></div>
