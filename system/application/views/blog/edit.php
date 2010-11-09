<?php $this->load->view('layout/tinymce.php'); ?>
<?php if ($new): /*@i18n: Edit news item. */ ?>
	<h1><?=t("Create a new blog post")?></h1>
<?php else: ?>
    <h1><?=t("Edit blog post")?>
    	<a href="<?= base_url() ?>blog/delete/<?= $news->post_id ?>" class="button" 
           title="<?=t("Delete this Blog Post")?>"><?=t("delete")?></a>
    </h1>
<?php endif; ?>

<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'news-add-form'))?>
<?php if (!$new): ?>
    <input type="hidden" id="post_id" name="post_id" value="<?=$news->post_id ?>" ?>
<?php endif; ?>

 <label for="title"><?=t("Title")?>:</label>
 <input type="text" maxlength="128" name="title" id="title"  size="95" 
        value="<?= $news->title ?>" />
 <label for="body"><?=t("Body")?>: </label>
 <textarea cols="60" rows="20" name="body" id="body"  ><?= $news->body ?></textarea>
       
  <?php $button = ($new) ? t("Create blog post") : t("Save blog post"); ?>
  <p><button type="submit" name="submit" class="submit" value="Save"><?=$button ?></button></p>      
<?=form_close()?>
