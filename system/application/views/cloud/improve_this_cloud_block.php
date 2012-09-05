<div class="box">
<h2><?= t("Improve this cloud") ?></h2>


<p class="add-link"><?= anchor('tag/add_tags/cloud/'.$cloud->cloud_id, t("Add a tag")) ?></p>

<p class="add-link"><?= anchor('content/add/'.$cloud->cloud_id, t("Add extra content")) ?></p>

<p class="add-link"><?= anchor('embed/add/'.$cloud->cloud_id, t("Add embedded content")) ?></p>

<p class="add-link"><?= anchor('cloud/add_link/'.$cloud->cloud_id, t("Add link")) ?></p>

<p class="add-link"><?= anchor('cloud/add_reference/'.$cloud->cloud_id, t("Add reference")) ?></p>

<?php if ($this->config->item('x_gadgets') && $edit_permission): ?>
<p class="add-link"><?= anchor('gadget/add_to_cloud/'.$cloud->cloud_id, t("Add gadget")) ?></p>
<?php endif; ?>
</div>