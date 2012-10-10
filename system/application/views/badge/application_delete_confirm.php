<h1><?=t("Delete the application for badge !name",
    array('!name'=>"<a href='".base_url()."badge/view/$badge->badge_id'>$badge->name</a>"))?></h1>
<p><?=t("Are you sure that you want to delete this application? Deleting an application deletes it permanently and cannot be undone.")?></p>

<?=form_open($this->uri->uri_string(), array('id' => 'application-delete-form'))?>
	<p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete Application")?></button></p>
<?php form_close(); ?>