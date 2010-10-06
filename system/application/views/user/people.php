<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Find People")?></h1>
          </div>
    <div class="c2of2">
        &nbsp;
    </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
        <p>
<?=t('You can search here for people who already have an account, [link-ui]browse by institution[/link] or [link-uu]browse alphabetically[/link].',
    array('[link-ui]' => t_link('user/institution_list'), '[link-uu]' => t_link('user/user_list'))) ?>
        </p>

        <?=form_open($this->uri->uri_string(), array('id' => 'user-search-form'))?>
            <p><label for="name"><?=t("Who are you looking for?")?></label></p>
            <p><input type="text" maxlength="128" name="name" id="name"  size="80" value="" /></p>
            <input type="submit" name="submit" id="submit" value="<?=t("Search")?>" />
        <?= form_close() ?>

        <?php if($users): ?>
            <h2><?=t("Results for !search", array('!search'=>"'$query_string'"))?></h2>
            <?php $this->load->view('user/users'); ?>
        <?php endif; ?>
        </div>
    </div>
</div> 
<div id="region2">
&nbsp;
</div>