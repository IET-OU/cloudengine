<?php if(count($cloudscapes) > 0):?>
    <ul class="cloudscapes">
	    <?php  foreach ($cloudscapes as $row):?>
	    	<li><a href="<?=base_url() ?>cloudscape/view/<?= $row->cloudscape_id ?>"><?= $row->title ?></a></li>
	    <?php endforeach; ?>
    </ul>
<?php endif; ?>


