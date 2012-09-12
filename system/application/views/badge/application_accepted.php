<div id="region1">
<h1><?=t("Application accepted")?></h1>
<p><?=t("Your application for the badge !name has been accepted. You will receive email 
notification when it has been approved or rejected.",
    array('!name' => anchor("badge/view/$badge->badge_id", $badge->name)))?></p>

</div>