

    
<img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" style="float: left;"/> 
        <h1><?= t('Badge: ') ?><?=$badge->name ?></h1>





<div id="region1">

        <h2><?= t("Badge criteria") ?></h2>
        <?=$badge->criteria?>
        

        <h2><?= t("Apply for this badge") ?></h2>
            <?=form_open($this->uri->uri_string(), array('id' => 'badge-apply-form'))?>
        <label for="evidence_url"><?= t("Evidence - please provide a URL with evidence that you
        meet the criteria for this badge.") ?>
        <input type="text" name="evidence_url" size="80" />
        <input type="submit" name="submit" id="submit" 
        class="submit" value="<?= t("Apply for badge")?>" />
        <?=form_close()?>


   
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>  
</div>
