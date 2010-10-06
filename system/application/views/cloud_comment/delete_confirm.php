<h1><?=t("Delete comment")?></h1>

<p><b><?=t("Are you sure that you want to delete the following comment?")?></b></p>
<?= $comment->body ?>

<?=form_open($this->uri->uri_string(), array('id' => 'comment-delete-form'))?>
	<input type="submit" name="submit" id="submit" value="<?=t("Delete comment")?>" />   
<?php form_close(); ?>