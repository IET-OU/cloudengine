<div class="grid headline">
        <h1><?=t("Pending badge applications")?></h1>
    <div class="c1of2">
 </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
            <h2>Badges for which  you are a named verifier</h2>
            <?php if(count($badges) >0): ?>
            <table>
                <caption class="hidden">List of Badges</caption>
                <thead>            
                    <tr>
                        <th scope="col"><?=t("Name")?></th>
                        <th scope="col"><?=t("Number of applications")?></th>
                    </tr>     
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php  foreach ($badges as $badge):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td><?=anchor("badge/applications/$badge->badge_id", $badge->name) ?></td>
                            <td><?= anchor("badge/applications/$badge->badge_id", $badge->total_applications)?></td>
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p><?= t("There are no pending applications.") ?></p>
            <?php endif; ?>
            <br />
            <h2><?= t("Badges which require a specified number of approvals from users of the site") ?></h2>
            <?php if(count($crowdsourced_badges) >0): ?>
            <table>
                <caption class="hidden">List of Badges</caption>
                <thead>            
                    <tr>
                        <th scope="col"><?=t("Name")?></th>
                        <th scope="col"><?=t("Number of applications")?></th>
                    </tr>     
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php  foreach ($crowdsourced_badges as $badge):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td><?=anchor("badge/applications/$badge->badge_id", $badge->name) ?></td>
                            <td><?= anchor("badge/applications/$badge->badge_id", $badge->total_applications)?></td>
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p><?= t("There are no pending applications") ?></p>
            <?php endif; ?>
        </div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
</div>