<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("!name's Clouds", array('!name'=>$profile->fullname))?></h1>
        <p><?=anchor("user/view/$profile->user_id", t("Back to !name's profile", array('!name'=>$profile->fullname)))?></p>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
            <table>
                <thead>            
                    <tr>
                        <th><?=t("Title")?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>     
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php  foreach ($clouds as $cloud):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td>
                            <?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?></td>
                            <td><?= $cloud->summary ?></td>
                            <td><?= $cloud->total_comments ?> comment<?php if ($cloud->total_comments != 1):?>s<?php endif; ?></td>
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
</div> 

