<h1><?=t("Delete content")?></h1>
<p><b><?=t("Are you sure that you want to delete the following content?")?></b></p>
<?= $content->body ?>

<?=form_open($this->uri->uri_string(), array('id' => 'content-delete-form'))?>
	<p><button type="submit" name="submit" value="Delete"><?=t("Delete content")?></button></p>
<?php form_close(); ?>