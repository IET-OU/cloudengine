<h2><?=t("Tags")?></h2>
<?php if ($tags):?>
    <p class="tags">
    <?php $row_num = 1; ?>
    <?php  foreach ($tags as $tag):?>	
       <span style="white-space: nowrap;"> <?= anchor('tag/view/'.urlencode($tag->tag), $tag->tag) ?>
<?php if ($edit_permission): ?>      
        <a href="<?= base_url() ?>tag/delete/<?= $tag->tag_id ?>" class="remove-tag" title="Remove tag">Remove <?= $tag->tag ?> tag</a>
        <?php endif; ?>
        </span>
        <?php if ($row_num < count($tags)):?>&nbsp;<?php endif; ?>
        <?php $row_num++; ?> 
    <?php endforeach; ?>
    </p>
<?php endif; ?>


