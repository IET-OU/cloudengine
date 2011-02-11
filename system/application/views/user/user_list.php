
<pre><? //print_r($this->_ci_cached_vars); ?></pre>
<div class="grid headline">
        <h1><?=t("Users")?></h1>
</div>
<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
        <ul class="a-z">
            <?php for ($character = 65; $character < 91; $character++) {?>
                                <?php if (chr($character) != $alpha): ?>
	           <li><a href="<?= base_url() ?>user/user_list/<?= chr($character) ?>"><?= chr($character) ?></a>&nbsp;</li>
	                            <?php else: ?>
                   <li> <strong><?= chr($character) ?></strong></li>
                    <?php endif; ?>
	           <?php }?>
	    </ul>
	    <?php if ($users): ?>
        <table id="userlist">
            <tbody>
                <?php $row = 1; ?>
                <?php  foreach ($users as $user):?>
                    
                    <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                      <td <?php if ($user->banned || $user->deleted): ?>class="inactive"<?php endif; ?>>
                        <a href="<?= base_url() ?>user/view/<?= $user->id?>"><?= $user->fullname ?></a>
                        <?php if($admin) : ?>
                          <span class="user-info"> (<?= $user->user_name .' - ' .$user->email ?>)</span>
                        <?php endif; ?>
                        <?php if ($user->banned): ?> 
                          <?= t('(Banned)') ?>
                        <?php endif; ?>
                        <?php if ($user->deleted): ?> 
                          <?= t('(Deleted)') ?>
                        <?php endif; ?>
                      </td>                        
                      <td><?= $user->institution ?></td> 
                    </tr>
                    <?php $row++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        </div>
    </div>
</div> 

