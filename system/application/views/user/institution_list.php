<div class="grid headline">
        <h1><?=t("Institutions")?></h1>
</div>
<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
        <ul class="a-z">
            <?php for ($character = 65; $character < 91; $character++) {?>
                                <?php if (chr($character) != $alpha): ?>
	          <li><a href="<?=base_url() ?>user/institution_list/<?= chr($character) ?>"><?= chr($character) ?></a>&nbsp;</li> 
	                            <?php else: ?>
                   <li> <strong><?= chr($character) ?></strong></li>
                    <?php endif; ?>
	           <?php }?>
	    </ul>
	    <?php if($institutions): ?>
        <table>
            <tbody>
                <?php $row = 1; ?>
                <?php  foreach ($institutions as $institution):?>
                    <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                        <td><a href="<?=base_url() ?>user/institution/<?= urlencode(trim($institution->institution)) ?>"><?= $institution->institution ?></a></td>
                        <td><?= $institution->total_users ?> users</td>
                         </tr>
                    <?php $row++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        </div>
    </div>
</div> 

