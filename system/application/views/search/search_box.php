<?php if(config_item('x_search')): ?>
<div id="search-site">
    <form id="form-search" action="<?=site_url('search/result') ?>" method="get">
      <p>
        <label for="query"><?=t("Search")?></label>
        <input id="query" name="q" type="search" value="<?=$query_string ?>" maxlength="30" />
        <input id="search-button" type="image" src="<?=base_url() ?>_design/search-button.gif" alt="<?=t("Search")?>" />
      </p>
    </form>
</div>
<?php endif; ?>