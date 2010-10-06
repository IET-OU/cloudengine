<div class="avatars">
<h2><?=t("Followers (!count)", array('!count'=>count($followers)))?></h2>
<?php if(count($followers) > 0):?>
    <?php  foreach (array_slice($followers, 0, 100) as $follower):?>
            <?php if ($follower->picture): ?>
    	<a href="<?= base_url() ?>user/view/<?= $follower->following_user_id ?>" title="<?= $follower->fullname ?>">

            <img src="<?= base_url() ?>image/user_16/<?= $follower->following_user_id?>" alt="<?= $follower->fullname ?>" class="go2" />
        </a>
        <?php endif; ?>   
    <?php endforeach; ?>
<p><?=anchor("user/followers/{$user->id}", t("View all followers"))?></p>
<?php else: ?>
    <p><?=t("No followers yet")?></p>
<?php endif; ?>
</div>