<h1><?=t("Deletion successful")?></h1>

<p><?=t("You have successfully deleted the badge '!name'.",
 array('!name'=>$badge->name))?></p>

<p><a href="<?= base_url() ?>badge/badge_list" class="buttonlink"><?=t("Go to all badges")?></a></p>