<h1><?=t("Deletion successful")?>/h1>

<p><?=t("You have successfully deleted the blog post '!title'.", 
array('!title'=>$news->title))?></p>

<p><a href="<?= base_url() ?>blog/news_list" class="buttonlink"><?=t("Go to all blog posts")?></a></p>