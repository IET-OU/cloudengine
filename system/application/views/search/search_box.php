<?php if(config_item('x_search')): ?>
<div id="search-site">
    <form id="form-search" action="<?=base_url() ?>search/result" method="post">
      <p>
        <label for="query_string"><?=t("Search")?></label>
        <input id="query_string" name="query_string" type="text" />
        <input id="search-button" name="search-button" type="image" value="submit" src="<?=base_url() ?>_design/search-button.gif" alt="<?=t("Search")?>" />
      </p>
    </form>
</div>
<?php endif; ?>