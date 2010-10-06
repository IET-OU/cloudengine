<div class="grid">
<?php if ($references): ?>
    <ul class="arrows">
    <?php foreach($references as $reference): ?>
    <li class="cloud-link"><?= $reference->reference_text ?>
    <br /><small>added by <a href="<?=base_url() ?>user/view/<?= $reference->user_id ?>"><?= $reference->fullname ?></a></small>
                <?php if ($edit_permission): ?>
                <br /><small><a href="<?=base_url() ?>cloud/delete_reference/<?= $reference->reference_id ?>"><?=t("delete")?></a></small>
            <?php endif; ?>
    </li>
    <?php endforeach; ?>
    </ul> 
<?php endif; ?>


<p class="add-link"><?= anchor('cloud/add_reference/'.$cloud->cloud_id, t("Add reference")) ?></p>
   
</div>
