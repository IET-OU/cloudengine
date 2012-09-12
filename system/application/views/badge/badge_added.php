<div id="region1">
<h1><?=t("Badge created")?></h1>
<p><?=t("Your badge !name has been created!",
    array('!name' => anchor("badge/view/$badge->badge_id", $badge->name)))?></p>

</div>