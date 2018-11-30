<?php /*-/-Translators: ** Start of the VIEWS / ABOUT section. ** */ ?>
<div class="grid headline">
    <div class="c1of1">
        <h1><?= $page->title ?></h1>
    </div>
</div>
<div id="region1">
    <div class="grid g1">
        <div class="c1of1 <?= $page->class_name ?>">
            <?= $page->body ?>
        </div>
    </div>
</div>
<div id="region2">
<?php $this->load->view('search/search_box'); ?>
<?php $this->load->view('user/user_block'); ?>
<p>
<?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?>
</p>

</div>
