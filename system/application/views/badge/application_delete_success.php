<h1><?=t("Deletion successful")?></h1>

<p><?=t("You have successfully deleted the application for badge '!name'.",
 array('!name'=>$badge->name))?></p>

<p><a href="<?= base_url() ?>badge/user_applications" class="buttonlink"><?=t("Go to all your badge applications")?></a></p>