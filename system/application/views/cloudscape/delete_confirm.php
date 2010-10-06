<h1><?=t("Delete the cloudscape !title",
  array('!title'=> anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title))) ?>
  </h1>

<p><?=t("Are you sure that you want to delete this cloudscape? Deleting a cloudscape deletes it permanently and cannot be undone.")?></p>

<?=form_open($this->uri->uri_string(), array('id' => 'cloudscape-delete-form'))?>
	<p><button type="submit" name="submit" value="Delete"><?=t("Delete Cloudscape")?></button></p>
<?php form_close(); ?>