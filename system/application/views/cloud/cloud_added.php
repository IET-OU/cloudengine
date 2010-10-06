<h1><?=t("Cloud created")?></h1>
<p><?=t("Your cloud !title has been created!",
    array('!title' => anchor("cloud/view/$cloud->cloud_id", $cloud->title)))?></p>
<p><?=t("You might like to [link-tag]add some tags to the cloud[/link] to help people find it or [link-view]view the cloud[/link]",
    array('[link-tag]' => t_link("tag/add_tags/cloud/$cloud->cloud_id"),
          '[link-view]'=> t_link("cloud/view/$cloud->cloud_id")))?></p>
