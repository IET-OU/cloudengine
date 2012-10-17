<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Badges")?></h1>
 </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
<p><?= anchor("badge/add", t("Create a badge"), array('class' =>'buttonlink')) ?> <?= anchor('badge/applications', t("Pending badge applications"), array('class' =>'buttonlink')) ?>
 <?= anchor('badge/user_applications', t("Your badge applications"), array('class' =>'buttonlink')) ?>
</p>
<?php if (count($badges ) > 0): ?>          
          <table>
                <caption class="hidden">List of Badges</caption>
                <thead>            
                    <tr>
                        <th>&nbsp;</th>
                        <th scope="col"><?=t("Name")?></th>
                        <th scope="col"><?=t("Description")?></th>
                    </tr>     
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php  foreach ($badges as $badge):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td>
                                  <a class="badge" href="<?= base_url().'badge/view/'.$badge->badge_id ?>"><img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" width="45px" height="45px" alt="" /></a> 
     
                            </td>
                            <td><?=anchor("badge/view/$badge->badge_id", $badge->name) ?></td>
                            <td><?= $badge->description ?></td>
                            
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
<?php else: ?>
<?= t("There are no badges yet.") ?>
<?php endif; ?>
        </div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
</div>