<h1><?=t("Your !item is being moderated", array('!item'=>$item)) /*Moderate !item. */ ?></h1>

<p><?=t(
"Apologies, your post is being moderated. This is likely to be a one-off occurrence and should usually only affect new users to the site. From time to time,
the filter will mistakenly identify a message as spam and put it forward for moderation. Please do not post the item or message again. During working hours we will quickly pick these up, check and clear
them - although this might take longer at the weekends or in the evenings (!timezone).",
    array('!timezone' => date('T'))
) ?></p> 

<p><a href="<?= $continuelink ?>" class="buttonlink"><?=t("Continue")?></a></p>
