<div class="grid">
<?php if ($references): ?>
    <ul class="arrows">
    <?php foreach($references as $reference): ?>
    <li class="cloud-link"><?= $reference->reference_text ?>
    <br /><small><a href="<?=base_url() ?>user/view/<?= $reference->user_id ?>"><?= $reference->fullname ?></a></small>
    </li>
    <?php endforeach; ?>
    </ul> 
<?php endif; ?>
</div>
