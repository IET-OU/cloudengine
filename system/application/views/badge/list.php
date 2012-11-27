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
    <div class="box">
    <h2><?= t("What is a badge?") ?></h2>
    <p><?= t("A badge is a way of recognising skills and achievements. Any registered 
    Cloudworks user can issue, earn and display a badge in their profile page.") ?> 
    </p>
<p>
<?= t("The badges used in Cloudworks support [link-openbadges]Mozilla Open Badges[/link]
and can be added to your [link-backpack]Mozilla Open Badge Backpack[/link].", 
array('[link-openbadges]'=>t_link('http://openbadges.org/en-US/faq.html', FALSE), 
'[link-backpack]'=>t_link('http://beta.openbadges.org', FALSE))) ?>
</p>
<h2><?= t("What do I have to do?") ?></h2>
<p><?= t("Click on a badge title to find out what criteria has been set for any 
one of the badges in the list.</p><p>Once you have decided that you want to work 
towards a particular badge, you need to start work to meet these criteria - and 
gathering together your evidence. Depending on the criteria set, your evidence 
might consist of a reflective blog post, or a photo, a document, a diagram, a 
screen shot, or an activity stream and you will need to be able to link to all 
your evidence via one URL - so it will need to be held online somewhere.") ?></p>
    </div>
</div>