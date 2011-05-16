<?php if(count($clouds) > 0):?>
    <ul class="clouds">
        <?php  foreach ($clouds as $row):?>
	        <li>
		        <a href="<?=base_url() ?>cloud/view/<?= $row->cloud_id ?>"><?= $row->title ?> 
		       <?php if ($row->total_comments > 0): ?> <br><small><?=plural(_("!count comment"), _("!count comments"), $row->total_comments) ?></small>
		       <?php endif; ?>
		       </a>
	        </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>