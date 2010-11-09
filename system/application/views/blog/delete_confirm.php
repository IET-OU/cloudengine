<h1><?=t("Delete the blog post !title", 
    array('!title'=>"<a href='".base_url()."blog/view/$news->post_id'>$news->title</a>"))?></h1>
<p><?=t("Are you sure that you want to delete this blog post? Deleting a blog post deletes it permanently and cannot be undone.")?></p>

<?=form_open($this->uri->uri_string(), array('id' => 'news-delete-form'))?>
    <p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete blog post")?></button></p>
<?php form_close(); ?>