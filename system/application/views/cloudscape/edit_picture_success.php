<h1><?=t("Cloudscape picture edited")?></h1>
<p><?=t("Your new Cloudscape picture was uploaded")?></p>

<p><img src="<?= base_url() ?>image/cloudscape/<?= $cloudscape->cloudscape_id ?>" alt="<?=t("Cropped picture")?>" /></p>

<?php if ($image_attr_name): ?><p><?=t("Attribution")?>: <a href="<?=$image_attr_link ?>"><?=$image_attr_name ?></a></p>
<?php endif; ?>

<?=$error ?>
<br />
<br />
<p><a href="<?=base_url() ?>cloudscape/view/<?= $cloudscape_id ?>" class="buttonlink"><?=t("Go to the Cloudscape")?></a></p>
