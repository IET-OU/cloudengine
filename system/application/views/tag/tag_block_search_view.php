
<?php if ($tags):?>
    <p class="tags">
    <?php $row_num = 1; ?>
    <?php  foreach ($tags as $tag):?>	
       <span style="white-space: nowrap;"> <?= anchor('tag/view/'.urlencode($tag->tag), $tag->tag) ?>
        </span>
        <?php if ($row_num < count($tags)):?>&nbsp;<?php endif; ?>
        <?php $row_num++; ?> 
    <?php endforeach; ?>
    </p>
<?php endif; ?>


