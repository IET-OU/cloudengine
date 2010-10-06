<div class="avatars">
<h2><?=t("Followers (!count)", array('!count'=>count($followers)))?></h2>
<?php if(count($followers) > 0):?>
    <?php  foreach (array_slice($followers, 0, 100) as $follower):?>
        <?php if ($follower->picture): ?>
    	   <a href="<?= base_url() ?>user/view/<?= $follower->id ?>" title="<?= $follower->fullname ?>">
           <img src="<?= base_url() ?>image/user_16/<?= $follower->id ?>" alt="<?= $follower->fullname ?>" class="go2" /></a>
        <?php endif; ?>

    <?php endforeach; ?>
    <p><a href="<?= base_url() ?>cloudscape/followers/<?= $cloudscape->cloudscape_id ?>"><?=t("View all followers")?></a></p>
<?php else: ?>
    <p><?=t("No followers yet")?></p>
<?php endif; ?>
</div>