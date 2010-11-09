<h1><label for="body"><?=t("Add extra content to the cloud !title",
    array('!title'=>"<a href='".base_url()."cloud/view/$cloud->id'>$cloud->title</a>")) ?></a></label></h1>

<?php $this->load->view('layout/tinymce.php'); ?>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'add-link-form'))?>
	<textarea cols="70" rows="30" name="body" id="body"></textarea>
	<p><button type="submit" name="submit" class="submit" value="Add"><?=t("Add content")?></button></p>
<?=form_close()?>