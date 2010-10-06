<h1><?=t("Deletion successful")?></h1>

<p><?=t("You have successfully deleted the cloudscape '!title'.", array('!title'=>$cloudscape->title))?></p>

<p><a href="<?= base_url() ?>cloudscape/cloudscape_list" class="buttonlink"><?=t("Go to all cloudscapes")?></a></p>