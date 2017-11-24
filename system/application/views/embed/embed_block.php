

<div id="embeds" class="embed-block">
<h2><?=t("Embedded Content")?></h2>
<?php if ($embeds): ?>

<?php foreach($embeds as $idx => $embed): ?>
<a id="embed-<?= $embed->embed_id ?>"></a>
<div class="embed-wrap em-<?= str_replace('.', '-', parse_url($embed->url, PHP_URL_HOST)) ?>">
    <h3><?= $embed->title ?></h3>
  <a id="em-n<?= $idx ?>" class="em" href="<?=$embed->url ?>" rel="nofollow"><?=$embed->title ?></a>
<p><?php if ($embed->accessible_alternative): ?>
   <?= anchor('embed/accessible_alternative/'.$embed->embed_id, t('Accessible Alternative')); ?><br />
  <?php endif; ?>
  <small>added by
<?= anchor('user/view/'.$embed->user_id, $embed->fullname) ?>
</small>

<?php if ($edit_permission || $embed->edit_permission): ?>
    &nbsp; <small>
    <?= anchor('embed/delete/'.$embed->embed_id, t("delete embedded content")) ?>
</small>
<?php if ($this->auth_lib->is_logged_in()): ?>
	<?php if ($this->config->item('x_flag')): ?>
		<?php if ($embed->flagged): ?>
			<?= t("Flagged as spam")  ?>
		<?php else: ?>
			<small><?= anchor('flag/item/embed/'.$embed->embed_id, t("Flag as spam")) ?></small>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php endif; ?>
<?php if ($embed->edit_permission): ?>
    &nbsp; <small>
    <?= anchor('embed/edit/'.$embed->embed_id, t("edit embedded content")) ?></small>
<?php endif; ?>
</p>
</div>
<?php endforeach; ?>


<?php /*<script src="//ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>*/ ?>
<?php if ($this->config->item('x_live')): ?>
<script src="<?=base_url() ?>_scripts/jquery.oembed.js"></script>
<?php else: ?>
<script src="<?=base_url() ?>_scripts/jquery.oembed.test.js"></script>
<?php endif; ?>
<script>
$(document).ready(function() {
  $('.embed-block a.em').oembed(null, <?=json_encode($this->config->item('oembed_options')) ?>);
});
</script>
<?php endif; ?>

<p class="add-link add-embed"><?= anchor('embed/add/'.$cloud->cloud_id, t("Add embedded content")) ?></p>
</div>
