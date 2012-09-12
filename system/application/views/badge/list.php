<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Badges")?></h1>
 </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
<p><?= anchor("badge/add", t("Create a badge")) ?></p>
            <table>
                <caption class="hidden">List of Badges</caption>
                <thead>            
                    <tr>
                        <th scope="col"><?=t("Name")?></th>
                        <th scope="col"><?=t("Description")?></th>
                    </tr>     
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php  foreach ($badges as $badge):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td><?=anchor("badge/view/$badge->badge_id", $badge->name) ?></td>
                            <td><?= $badge->description ?></td>
                            
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
</div>