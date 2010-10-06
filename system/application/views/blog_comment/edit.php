<?php $this->load->view('layout/tinymce.php'); ?>
<?php if ($new): ?>
	<h1><?=t("Create a comment item")?></h1>
<?php else: ?>
    <h1><?=t("Edit Comment")?>
    	<a href="<?= base_url() ?>blog/comment_delete/<?= $comment->comment_id ?>" class="button" title="<?=t("Delete this comment")?>"><?=t("delete")?></a>
    </h1>
<?php endif; ?>

<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'comment-add-form'))?>
	<?php if (!$new): ?>
    	<input type="hidden" id="comment_id" name="comment_id" value="<?=$comment->comment_id ?>" ?>
	<?php endif; /*/Translators: Comment/item body. */ ?>

	<label for="body"><?=t("Body")?>: </label>
 	<textarea cols="60" rows="20" name="body" id="body"  ><?= $comment->body ?></textarea>
    <?php $button = ($new) ? t("Create comment") : t("Save comment"); ?>
    <p><button type="submit" name="submit" value="Save"><?=$button?></button></p>
<?=form_close()?>
