<div class="grid">
<h2>Popular</h2>

<ul class="cloudstream-filter">
    <?php if ($popular_type == 'cloud'): ?>
        <li><strong>Clouds</strong></li>
        <li><?= anchor('/'.$month.'/cloudscape#popular', t("Cloudscapes")) ?></li>
    <?php elseif ($popular_type == 'cloudscape'): ?>
        <li><?= anchor('/'.$month.'/cloud#popular', t("Clouds")) ?></a></li>
        <li><strong>Cloudscapes</strong></li>
    <?php endif; ?>
</ul>
</div>

<div class="grid" id="popular">
<?php if ($popular_type == 'cloud'): ?>
    <ul class="clouds">
        <?php foreach($popular_clouds as $cloud): ?>
            <li><?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?></li>
        <?php endforeach; ?>
    </ul>
        
<?php elseif ($popular_type = 'cloudscape'): ?>
   <ul class="cloudscapes">
        <?php foreach($popular_cloudscapes as $cloudscape): ?>
        <li><?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>