<h2><?=t("!person's Cloudscapes", array('!person' => $user->fullname)) ?></h2>

<?php if(count($cloudscapes) > 0):?>
    <ul class="cloudscapes">
	    <?php  foreach ($cloudscapes as $row):?>
	    	<li><a href="<?=base_url() ?>cloudscape/view/<?= $row->cloudscape_id ?>"><?= $row->title ?></a></li>
	    <?php endforeach; ?>
    </ul>
<?php else: ?>
	<p><?=t("No cloudscapes yet") ?></p>
<?php endif; ?>


