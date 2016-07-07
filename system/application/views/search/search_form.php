<?php if(config_item('x_search')): ?>
<form id="form-search" action="<?=site_url('search/result') ?>/" method="get">
  <p>
    <label for="query"><?=t("Search")?></label>
    <input id="query" name="q" type="search" size="50" maxlength="30" />
    <button type="submit" id="search-submit"><?=t("Search")?></button>
  </p>
<?php $this->load->view('search/plugin'); ?>
</form>
<?php endif; ?>
