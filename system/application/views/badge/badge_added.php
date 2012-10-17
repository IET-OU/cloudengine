<div id="region1">
<h1><?=t("Badge created")?></h1>
<p><?=t("Your badge !name has been created!",
    array('!name' => anchor("badge/view/$badge->badge_id", $badge->name)))?></p>
<p><?= anchor('badge/add', t('Create another badge')) ?></p>
            
<p><a href="<?= base_url() ?>badge/badge_list" class="buttonlink"><?=t("Back to all badges")?></a></p>
  
</div>