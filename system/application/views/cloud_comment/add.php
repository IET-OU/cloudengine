<?php $this->load->view('layout/tinymce.php'); ?>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'comment-add-form'))?>
    <input type="hidden" id="cloud_id" name="cloud_id" value="<?=$cloud->cloud_id?>" />
    <textarea cols="74" rows="15" name="body" id="body"></textarea>
    <button type="submit" name="submit" value="Post"><?=t("Post comment")?></button>
<?=form_close()?>
<p><?=t("[link-mail]Change new comment e-mail notification preferences[/link].",
    array('[link-mail]' => t_link('user/preferences')))?></p>