<?php $this->load->view('layout/tinymce.php'); ?>

<h1>Update Site News</h1>
<p><?= t("To remove the site news block, leave empty, making sure that the HTML for the news is also empty") ?></p>
<?=form_open($this->uri->uri_string(), array('id' => 'add-site_news-form'))?>
<p><label for="body"><?=t("News content to appear on box on home page")?>:</label></p>
<textarea cols="61" rows="10" name="body" id="body"><?= $site_news?></textarea>       
<p><button type="submit" name="submit" class="submit" value="Add"><?=t("Update Site News")?></button></p>
<?=form_close()?>