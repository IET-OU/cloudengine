<?php $this->load->view('layout/tinymce.php'); ?>
<h1><label for="body">
<?php if ($new): ?>
    <?=t("Create content")?>
<?php else: ?>
    <?=t("Edit Content")?>
    <?= anchor('content/delete/'.$content->content_id, t('delete'), 'class="button"') ?>  
<?php endif; ?>
</label></h1>

<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'content-add-form'))?>
    <?php if (!$new): ?>
        <input type="hidden" id="content_id" name="content_id" value="<?=$content->content_id ?>" ?>
    <?php endif; ?>
     <textarea cols="60" rows="20" name="body" id="body"  ><?= $content->body ?></textarea>
     
     <?php $button = ($new) ? t("Save content") : t("Add content"); ?>
     <p><button type="submit" name="submit" value="Save"><?=$button ?></button></p>
<?=form_close()?>
