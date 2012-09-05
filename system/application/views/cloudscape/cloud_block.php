<div class="box">
<h2 id="clouds-in-cloudscape"><?=t("Clouds in this Cloudscape")?></h2>
<?php if ($sections): ?>
    <ul class="cloudstream-filter">
    <li><?php if($section_id ==''): ?><strong><?=t("Show all")?></strong><?php else: ?><a href="<?= base_url() ?>cloudscape/view/<?= $cloudscape->cloudscape_id?>/<?= $type ?>/#clouds-in-cloudscape"><?=t("Show all")?></a><?php endif; ?></li>
    <?php foreach($sections as $section): ?>
    <li><?php if($section_id ==$section->section_id): ?><strong>
    <?= $section->title ?></strong>
    <?php else: ?>
    <a href="<?= base_url() ?>cloudscape/view/<?= $cloudscape->cloudscape_id?>/<?= $type ?>/<?= $section->section_id ?>#clouds-in-cloudscape">
    <?= $section->title ?></a>
    <?php endif; ?>
    <?php if($section_id ==$section->section_id): ?></strong>
    <?php endif; ?></li>
    <?php endforeach; ?>
    </ul>
    <br />
    <br />
<?php endif; ?>

<?php if (count($clouds) > 0):?>
    <ul class="clouds">
        <?php  foreach ($clouds as $cloud):?>
            <li>
                <a href="<?= base_url() ?>cloud/view/<?= $cloud->cloud_id ?>" ><?= $cloud->title ?> 
                <?php if ($cloud->total_comments != 0): ?>
                    (<?=plural(_("!count comment"), _("!count comments"), $cloud->total_comments) ?>)
                <?php endif; ?>
                <?php if (isset($cloud->total_content) && $cloud->total_content != 0): ?>
                    (<?= $cloud->total_content ?> extra content item<?php if ($cloud->total_content != 1): ?>s<?php endif; ?>)
                <?php endif; ?>
                </a> 
            </li>
            <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?=t("No clouds yet")?></p>
<?php endif; ?>
</div>
