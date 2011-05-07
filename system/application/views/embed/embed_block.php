
<div id="embeds">
<h2><?=t("Embedded Content")?></h2>
<?php if ($embeds): ?>
<?php $i = 0; ?>
<?php foreach($embeds as $embed): ?>

    <h3><?= $embed->title ?></h3>
  <div id="oembed<?= $i ?>"></div>

  <?php if ($embed->accessible_alternative): ?>
   <?= anchor('embed/accessible_alternative/'.$embed->embed_id, t('Accessible Alternative')); ?><br />
  <?php endif; ?>
&nbsp; <small>added by
<?= anchor('user/view/'.$embed->user_id, $embed->fullname) ?>
</small>

<?php if ($edit_permission || $embed->edit_permission): ?>
    &nbsp; <small>
    <?= anchor('embed/delete/'.$embed->embed_id, t("delete embedded content")) ?>
</small>
<?php endif; ?>
<?php if ($embed->edit_permission): ?>
    &nbsp; <small>
    <?= anchor('embed/edit/'.$embed->embed_id, t("edit embedded content")) ?></small>
<?php endif; ?>
<?php $i++; ?>
<br /><br />
<?php endforeach; ?>

<?php /*<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>*/ ?>
<script src="<?=base_url() ?>_scripts/jquery.oembed.js"></script>
<script>
$(document).ready(function() {
<?php
  $i = 0;
  foreach($embeds as $embed): ?>
  $("#oembed<?= $i ?>").oembed("<?= $embed->url ?>");
<?php
  $i++;
  endforeach; ?>
});
</script>
<?php endif; ?>

<p class="add-embed"><?= anchor('embed/add/'.$cloud->cloud_id, t("Add embedded content")) ?></p>
</div>
