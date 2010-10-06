<?php if ($cloudscape->image_path):?>
<div class="cloudscape-image">
<img src="<?= base_url() ?>image/cloudscape/<?= $cloudscape->cloudscape_id ?>" alt="" class="cloudscape"/> 
<?php if ($cloudscape->image_attr_name):?> 
  <p><?=t("Image by !person", array('!person'=> 
    ($cloudscape->image_attr_link
      ? "<a href='$cloudscape->image_attr_link'>$cloudscape->image_attr_name</a>"
      : $cloudscape->image_attr_name) ));?></p>
<?php endif; ?>
</div>
<?php endif; ?>