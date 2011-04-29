<?php if(config_item('x_search')): ?>
<form id="form-search" action="<?=site_url('search/result') ?>" method="get">
  <p>
    <label for="query_string"><?=t("Search")?></label>
    <input id="query_string" name="q" type="search" size="50" maxlength="30" />
    <button type="submit" id="search-submit" value="Search"><?=t("Search")?></button>
  </p>
</form>
<?php endif; ?>