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
        <table>
            <tbody>
                <?php $row = 1; ?>
                <?php  foreach ($users as $user):?>
                    <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                        <td><a href="<?= base_url() ?>user/view/<?= $user->id?>"><?= $user->fullname ?></a></td>
                        <td><?= $user->institution ?></td></td> 
                        </tr>
                    <?php $row++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        </div>
    </div>
</div> 

