<div class="grid headline">
<div class="c1of2"> 
<img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" style="float:left;"/>
<h1> <?= t('Badge: ') ?><?=$badge->name ?></h1>
<p> <?= $badge->description ?></p>
</div>
<div class="c2of2"></div>
</div>
<div id="region1">


        <h2><?= t("Badge criteria") ?></h2>
        <?=$badge->criteria?>
    

        <h2><?= t("Apply for this badge") ?></h2>
            <?='<b>'.validation_errors().'</b>'; ?>
            <?=form_open($this->uri->uri_string(), array('id' => 'badge-apply-form'))?>
        <label for="evidence_url"><?= t("Evidence - please provide a single URL with evidence that you
        meet the criteria for this badge.") ?>
        <input type="url" id="evidence_url" name="evidence_url" required="" size="80" />
        <input type="submit" name="submit" id="submit" 
        class="submit" value="<?= t("Apply for badge")?>" />
        <?=form_close()?>


   
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?> 
<div class="box">
<h2><?= t("How do I apply for a badge?") ?></h2>
<p><?= t("When you are ready to submit your evidence, just click on the red 
 'Apply for badge' button.") ?>
</p>
<h2><?= t("Providing evidence") ?></h2>
<p><?= t("First make sure that your evidence is openly accessible via a URL link. Then 
add the link in the box provided. If you have a number of different pieces of 
evidence you want to submit, the easiest way of doing this is to set up a 
Cloud. Use the text box to describe your evidence and how it meets the criteria 
and then 'Add content', 'Add embedded content' or 'Add link' for as many pieces 
of evidence as you need. Then copy and paste the URL for you evidence Cloud 
into this page.") ?></p>
<h2><?= t("What if I don't meet the criteria?") ?></h2>
<p><?= t("Someone will check that the evidence that you have provided meets the 
criteria. If it doesn't, they will give you feedback to say why, and you will 
get as many chances to resubmit new or revised evidence as you need.") ?>
</p>
<h2><?= t("What happens when I am awarded a badge?") ?></h2>
<p><?= t("
When your badge application has been approved you will receive an email letting
 you know. You will also be given the opportunity to add the badge 
[link-backpack]Mozilla Open Badge Backpack[/link] if you have one or 
want to set one up. You will need to use the same   email address when you set up your Backpack as you used to set up your 
 Cloudworks profile.",
 array('[link-backpack]'=>t_link('http://beta.openbadges.org/', FALSE))) ?>
</p>
<h2><?= t("Displaying your badge") ?></h2>
<p><?= t("Your badge will automatically be displayed on your Cloudworks profile 
page and you can add your badge to your [link-backpack]Mozilla Open Badge Backpack[/link]. Mozilla Open Badges is 
quite a new concept but in the future there are likely to be far more places you can 
display your badges from your Backpack.",
 array('[link-backpack]'=>t_link('http://beta.openbadges.org/', FALSE))) ?>
</p>


</div>    
</div>
