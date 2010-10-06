<?php $omit_cloudscapes = isset($omit_cloudscapes) ? $omit_cloudscapes : false; ?>
<ul class="cloudstream-filter">
<li><?php if($type ==''): ?><strong><?=t("Show all")?></strong><?php else: ?><a href="<?= $basepath ?>/#cloudstream" title="<?= t('Show all activity') ?>"> <?=t("Show all")?></a><?php endif; ?></li>
<li><?php if($type =='cloud'): ?><strong>Clouds</strong><?php else: ?><a href="<?= $basepath ?>/cloud#cloudstream">Clouds</a><?php endif; ?><?php if($type =='cloud'): ?></strong><?php endif; ?></li>
<?php if (!$omit_cloudscapes): ?>
<li><?php if($type =='cloudscape'): ?><strong>Cloudscapes</strong><?php else: ?><a href="<?= $basepath ?>/cloudscape#cloudstream">Cloudscapes</a><?php endif; ?></li>
<?php endif; ?>
<li><?php if($type =='comment'): ?><strong><?=t("Discussion")?></strong><?php else: ?><a href="<?= $basepath ?>/comment#cloudstream"><?=t("Discussion")?></a><?php endif; ?></li>
<li><?php if($type =='link'): ?><strong><?=t("Links")?></strong><?php else: ?><a href="<?= $basepath ?>/link#cloudstream"><?=t("Links")?></a><?php endif; ?></li>
<li><?php if($type =='reference'): ?><strong><?=t("References")?></strong><?php else: ?><a href="<?= $basepath ?>/reference#cloudstream"><?=t("References")?></a><?php endif; ?></li>
<li><?php if($type =='content'): ?><strong><?=t("Extra content")?></strong><?php else: ?><a href="<?= $basepath ?>/content#cloudstream"><?=t("Extra content")?></a><?php endif; ?></li>
</ul>