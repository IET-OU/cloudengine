<h1><?= t("Manage pages") ?></h1>

<p><?= anchor('admin/add_page', t("Add new page")) ?></p>

<p><?= t("To view pages not in the current language, change the current language to the language of the page that you wish to view") ?></p>



<h2><?= t("Support Pages") ?></h2>
<?php if ($support_pages): ?>
<ul class="arrows">
<?php foreach($support_pages as $page): ?>
<li><?=  $page->title ?> (<?= t("language") ?>:<?= $page->lang ?>) 

<?php if ($this->lang->lang_code() == $page->lang) :?><?= anchor('support/'.$page->name, t('View')) ?><?php endif; ?>&nbsp; &nbsp;<?= anchor('admin/edit_page/'.$page->section.'/'.$page->name.'/'.$page->lang, t('Edit')) ?>&nbsp; &nbsp; <?= anchor('admin/delete_page/'.$page->section.'/'.$page->name.'/'.$page->lang, t('Delete')) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>



<h2><?= t("About Pages") ?></h2>

<?php if ($about_pages): ?>
<ul class="arrows">
<?php foreach($about_pages as $page): ?>
<li><?=  $page->title ?> (<?= t("language") ?>:<?= $page->lang ?>) 

<?php if ($this->lang->lang_code() == $page->lang) :?><?= anchor('support/'.$page->name, t('View')) ?><?php endif; ?>&nbsp; &nbsp;<?= anchor('admin/edit_page/'.$page->section.'/'.$page->name.'/'.$page->lang, t('Edit')) ?>&nbsp; &nbsp; <?= anchor('admin/delete_page/'.$page->section.'/'.$page->name.'/'.$page->lang, t('Delete')) ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>