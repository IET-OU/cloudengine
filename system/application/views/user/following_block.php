<div class="avatars">

<h2><?=t("Following (!count)", array('!count'=>count($following)))?></h2>
<?php if(count($following) > 0):?>
<ul class="clouds">
    <?php  foreach (array_slice($following, 0, 100) as $follows):?>
     <?php if ($follows->picture): ?>
        <a href="<?= base_url() ?>user/view/<?=$follows->followed_user_id?>" title="<?= $follows->fullname ?>">
       
            <img src="<?= base_url() ?>image/user_16/<?= $follows->followed_user_id ?>" alt="<?= $follows->fullname ?>" class="go2" />
        </a>
             <?php endif; ?>
        
    <?php endforeach; ?>
<p><?=anchor("user/following/{$user->id}", t("View all following")) ?></p>
<?php else: ?>
<p><?=t("Not following anybody yet")?></p>
<?php endif; ?>
</div>