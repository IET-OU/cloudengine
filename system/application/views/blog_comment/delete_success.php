<h1><?=t("Deletion successful")?></h1>
<p><?=t("You have successfully deleted the comment.")?></p>
<p><a href="<?= base_url() ?>blog/view/<?= $comment->post_id ?>" class="buttonlink"><?=t("Go to the blog post")?></a></p>