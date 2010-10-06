<h1><?=t("Delete the cloud !title",
    array('!title'=>"<a href='".base_url()."cloud/view/$cloud->id'>$cloud->title</a>"))?></h1>
<p><?=t("Are you sure that you want to delete this cloud? Deleting a cloud deletes it permanently and cannot be undone.")?></p>

<?=form_open($this->uri->uri_string(), array('id' => 'cloud-delete-form'))?>
	<p><button type="submit" name="submit" value="Delete"><?=t("Delete Cloud")?></button></p>
<?php form_close(); ?>