<h1><?=t("Deletion successful")?></h1>

<p><?=t("You have successfully deleted the cloud '!title'.",
 array('!title'=>$cloud->title))?></p>

<p><a href="<?= base_url() ?>cloud/cloud_list" class="buttonlink"><?=t("Go to all clouds")?></a></p>