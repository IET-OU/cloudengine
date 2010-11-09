<h1><?=t("Delete page")?></h1>
<p><?=t("Are you sure that you want to delete the page !title? Deleting a page deletes it permanently and cannot be undone.", array('!title' => $page->title))?></p>

<?=form_open($this->uri->uri_string(), array('id' => 'page-delete-form'))?>
	<p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete Page")?></button></p>
<?php form_close(); ?>