<?php /*/Translators: ** The VIEWS section. ** */ ?>
<div id="active-clouds" class="box">
<h2><?=t("Active Clouds")?></h2>
<ul class="active-clouds">
    <?php if(count($active_clouds) > 0):?>
        <?php  foreach ($active_clouds as $row):?>	
                <li><?=anchor("cloud/view/$row->cloud_id", $row->title) ?></li>
        <?php endforeach; ?>
    <?php endif; ?>

</ul>
<p><?=anchor("cloud/cloud_list", plural(_("View all !count Clouds"), _("View all !count Clouds"), $total_clouds)) ?></p>
</div>
