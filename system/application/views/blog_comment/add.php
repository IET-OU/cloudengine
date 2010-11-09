<?php $this->load->view('layout/tinymce.php'); ?>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'comment-add-form'))?>
    <input type="hidden" id="post_id" name="post_id" value="<?=$news->post_id?>" />
    <textarea cols="70" rows="15" name="body" id="body"></textarea>
    <p><button type="submit" name="submit" class="submit" value="Post"><?=t("Post comment")?></button></p>
<?=form_close()?>