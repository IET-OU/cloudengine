<div id="region1">
<h1><?=t("Application recieved")?></h1>
<p><?=t("Your application for the badge !name has been received. You will receive email 
notification when it has been approved or rejected.",
    array('!name' => anchor("badge/view/$badge->badge_id", $badge->name)))?></p>
    
    <p><?= anchor('badge/badge_list', t("Back to list of badges")) ?></p>

</div>