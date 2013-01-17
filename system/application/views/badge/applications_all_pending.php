<div class="grid headline">
        <h1><?=t("Pending badge applications")?></h1>
    <div class="c1of2">
  <p><a href="#badges-named" ><?= t('Named verifier badges') ?></a>
   | <a href="#badges-number"><?= t('Crowdsourced badges') ?></a>

 </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
            <h2 id="badges-named">Badges for which  you are a named verifier</h2>
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
            <h2 id="badges-number"><?= t("Badges which require a specified number of approvals from users of the site") ?></h2>
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
            
            <p><a href="<?= base_url() ?>badge/badge_list" class="buttonlink"><?=t("Back to all badges")?></a></p>
        </div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <div class="box">
    <h2><?= t("The role of the verifier") ?></h2>
    <p>
    <?= t("The role of the verifier is a really important one. When you verify 
    an application, make sure that you carefully check that the evidence 
    provided by the applicant really does meet the badge criteria. If the 
    evidence does not yet meet the criteria you must ensure that you give the 
    applicant constructive feedback so that they are able to resubmit 
    successfully should they want to. Even if the application is successful, 
    it is good practice to give feedback.") ?>
    </p>
    
    </div>
</div>