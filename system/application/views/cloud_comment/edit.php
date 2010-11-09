<?php $this->load->view('layout/tinymce.php'); ?>

<h1><?php if ($new): ?>
    <?=t("Create a comment item")?>
<?php else: ?>
    <?=t("Edit Comment")?>
	    <?php if($admin): ?>
	    	<a href="<?= base_url() ?>comment/delete/<?= $comment->comment_id ?>" class="button" title="<?=t("Delete this comment")?>"><?=t("delete")?></a>
	    <?php endif; ?>
<?php endif; ?>
</h1>

<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'comment-add-form'))?>
	<?php if (!$new): ?>
	<input type="hidden" id="comment_id" name="comment_id" value="<?=$comment->comment_id ?>" ?>
	<?php endif; ?>
	<label for="body"><?=t("Body")?>: </label>
	<textarea cols="60" rows="20" name="body" id="body"  ><?= $comment->body ?></textarea>
	<?php $button = ($new) ? t("Create comment") : t("Save comment"); ?>
	<input type="submit" name="submit" id="submit" class="submit" value="<?=$button ?>" />      
<?=form_close()?>
