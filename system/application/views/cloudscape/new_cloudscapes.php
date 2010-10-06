<div class="box">
    <h2><?=t("New Cloudscapes")?></h2>
    <ul class="cloudscapes">
        <?php if(count($new_cloudscapes) > 0):?>
            <?php  foreach ($new_cloudscapes as $row):?>	
                    <li>
                    <?= anchor('cloudscape/view/'.$row->cloudscape_id, $row->title) ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>