<div class="box">
    <h2><?=t("New Clouds")?></h2>
        <ul class="clouds">
            <?php if(count($new_clouds) > 0):?>
                <?php foreach ($new_clouds as $row): ?>	
                    <li><?=anchor("cloud/view/$row->cloud_id", $row->title) ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
</div>