<font style="font: normal 0.8em Helvetica">

<p>The following <?= $item_type ?> requires moderation. You can approve or delete it here:
</p>
<p>
<?= anchor('admin/moderate', 'Moderation Panel') ?> 
<h1><?= $title ?></h1>
<?= $body ?>

</font>