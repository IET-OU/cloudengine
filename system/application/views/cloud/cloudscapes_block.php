<div class="box">
<h2><?=t("In Cloudscapes")?></h2>
<?php if(count($cloudscapes) > 0):?>
    <ul class="cloudscapes">
        <?php  foreach ($cloudscapes as $cloudscape):?>
            <li>
                <?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title) ?>
            </li>
         <?php endforeach; ?>
    </ul>
<?php endif; ?>
<p class="add-link"><?= anchor('cloudscape/add_cloud/'.$cloud->cloud_id, t("Add to a Cloudscape")) ?></p>
</div>



            
               
            
        