<?php if(config_item('x_search')): ?>
<form id="form-search" action="<?=base_url() ?>search/result" method="post">
  <p>
    <label for="query_string"><?=t("Search")?></label>
    <input id="query_string" name="query_string" type="text" size="50" />
    <button type="submit" id="search-submit" value="Search"><?=t("Search")?></button>
  </p>
</form>
<?php endif; ?>