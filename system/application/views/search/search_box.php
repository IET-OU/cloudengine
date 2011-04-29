<?php if(config_item('x_search')):
  $query = isset($query_string) ? $query_string : '';
?>
<div id="search-site">
    <form id="form-search" action="<?=site_url('search/result') ?>" method="get">
      <p>
        <label for="query"><?=t("Search")?></label>
        <input id="query" name="q" type="search" value="<?=$query; ?>" maxlength="30" />
        <input id="search-button" type="image" src="<?=base_url() ?>_design/search-button.gif" alt="<?=t("Search")?>" />
      </p>
    <?php $this->load->view('search/plugin'); ?>
    </form>
</div>
<?php endif; ?>