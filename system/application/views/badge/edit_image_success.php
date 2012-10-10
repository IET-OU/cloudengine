<h1><?=t("Badge image edited")?></h1>
<p><?=t("Your new Badge image was uploaded")?></p>

<p><img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" /></p>

<br />
<br />
<p><a href="<?=base_url() ?>badge/view/<?= $badge->badge_id ?>" class="buttonlink"><?=t("Go to the Badge")?></a></p>
