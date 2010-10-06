<div id="region1">
<h1><?=t("Manage Google Gadgets") ?></h1>
<?php t("Use this page to manage the Google Gadgets that are added to all your clouds") ?>
<p><?= anchor('gadget/add_to_user', t("Add a Google Gadget to all your clouds")); ?></p>
<?php if ($gadgets): ?>
<ul>
<?php foreach($gadgets as $gadget): ?>
<li><?= $gadget->title ?> &nbsp;&nbsp;<small><?= anchor('gadget/delete_from_user/'.$gadget->gadget_id, t("delete")) ?></small></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p><?= t("You have not added any gadgets to all your clouds yet") ?></p>
<?php endif; ?>
</div>

<div id="region2">
<div class="box">
<p><?= t("You can also add and delete gadgets for individual clouds. To do this go to the page for the cloud") ?>
</div>
</div>