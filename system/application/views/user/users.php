<div class="userlist">  
<?php $row = 1; ?>

<?php  foreach ($users as $user):?>
    <div class="user<?php if ($row % 2 == 0): ?> even<?php endif; ?>">
        <?php if (property_exists($user, 'picture')): ?>
            <img src="<?= base_url() ?>image/user_32/<?= $user->id ?>" class="go2" title="<?= $user->fullname ?>"/>
        <?php else: ?>
            <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" title="<?= $user->fullname ?>"/>
        <?php endif; ?>
        <p><a href="<?= base_url() ?>user/view/<?= $user->id ?>" title="<?= $user->fullname ?>"><?= $user->fullname ?></a><br />
        <p><?= $user->institution ?></p>
    </div>
    <?php $row++; ?>
<?php endforeach; ?>
</div>