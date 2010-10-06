<h1><?=t("Manage clouds for the cloudscape !title", 
    array('!title'=>"<a href='".base_url()."cloudscape/view/$cloudscape->cloudscape_id'>$cloudscape->title</a>"))?></h1>
<p><?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, t("Back to cloudscape")) ?>
</p>
<?php if (count($sections) > 0):?>
        <?php  foreach ($sections as $section):?>
            <h3><?= $section->title ?></h3>
        <p><small>
        <?= anchor('cloudscape/section_remove/'.$cloudscape->cloudscape_id.'/'.$section->section_id,
        t("Delete section")) ?></small>
        <small>
        <?= anchor('cloudscape/section_rename/'.$cloudscape->cloudscape_id.'/'.$section->section_id, 
        t("Rename section")) ?></small></p>
             
             <?php if ($cloud_sections[$section->section_id]): ?>
                 <ul>
                 <?php foreach ($cloud_sections[$section->section_id] as $cloud): ?>
              <li><?= $cloud->title ?> <small>
              <?= anchor('cloudscape/cloud_section_remove/'.$cloudscape->cloudscape_id.'/'.$section->section_id.'/'.$cloud->cloud_id,t("remove") ) ?>
</small></li>
                 <?php endforeach; ?>
                 </ul>
             <?php else: ?>
             <p><?=t("No clouds in this section.")?></p>
             <?php endif; ?>
             <br />
       <?php endforeach; ?>
<?php else: ?>
    <p><?=t("No sections yet")?></p>
<?php endif; ?>
<p><?= anchor('cloudscape/section_add/'.$cloudscape->cloudscape_id, t("Add a section")) ?></p>
<p><?= anchor('cloudscape/cloud_section_add/'.$cloudscape->cloudscape_id, t("Add clouds to a section")) ?>
</p>
</div>