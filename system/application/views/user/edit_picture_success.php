<h1><?=t("Profile picture edited")?></h1>
<p><?=t("Your new profile picture was uploaded") ?></p>
<img src="<?= base_url() ?>image/user/<?= $user_id ?>" alt="<?=t("New profile picture")?>" />
<br />
<br />
<p><a href="<?= base_url() ?>user/view/<?= $user_id ?>" class="buttonlink"><?=t("Go to your profile")?></a></p>
