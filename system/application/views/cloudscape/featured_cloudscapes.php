<div class="box clearfix">
    <h2><?=t("Featured Cloudscapes")?></h2>
    <div class="featured-cloudscapes">

        <div class="featured-pic">
            <a href="<?= base_url() ?>cloudscape/view/<?= $default_cloudscape->cloudscape_id ?>">
                <img src="<?= base_url() ?>image/cloudscape/<?= $default_cloudscape->cloudscape_id ?>" alt="<?=t("View Cloudscape: ".$default_cloudscape->title)?>" width="240" height="180" />
            </a>
            
            <?php if ($default_cloudscape->image_attr_name): ?>
            <p>	<?=t("image by !person", array('!person'=>"<a href='$default_cloudscape->image_attr_link'>$default_cloudscape->image_attr_name</a>"))?></p>
            <?php endif; ?> 
                     
       </div>
       


        <div class="featured-links">
        <h3>
        <a href="<?= base_url() ?>cloudscape/view/<?= $default_cloudscape->cloudscape_id ?>"><?= $default_cloudscape->title ?> </a></h3>

<p><?= $default_cloudscape->summary ?> </p>

<h3><?= t("More featured Cloudscapes") ?></h3>
            <ul>
                <?php  foreach ($featured_cloudscapes as $cloudscape):?>
	                <?php if ($cloudscape->cloudscape_id != $default_cloudscape->cloudscape_id) : ?>                         <li> <?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title)?>
	                </li>


	                   <?php endif; ?>

                <?php endforeach; ?>
            </ul>      
        </div>   
    </div>
</div>









