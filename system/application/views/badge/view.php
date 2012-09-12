<script type="text/javascript" src="<?=base_url()?>_scripts/iframe_strip.js"></script>
<div class="grid headline">
    <div class="c1of2">
    
<img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" style="float: left;"/> 
        <h1><?= t('Badge: ') ?><?=$badge->name ?></h1>
        <?php $this->load->view('badge/options_block'); ?>
        <p><?= $badge->description ?></p>
    </div>

    <div class="c2of2">
        <p class="created-by">
        <?=t("<abbr title='!definition'>Badge</abbr> created by: !person-date",
          array('!person-date'=>NULL /*Hint - recurse. */, 
                '!definition' =>t("Badges can be anything of relevance to learning and teaching"))) ?></p>

            <?php if ($badge->picture): ?>
                <img src="<?= base_url() ?>image/user_32/<?= $badge->user_id ?>" class="go2" alt=""/>
            <?php else: ?>
                <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" alt=""/>
            <?php endif; ?>
        <p><?=anchor("user/view/$badge->id", $badge->fullname) ?><br />
        <?= format_date("!date!", $badge->created); ?></p>
    </div>
</div>

<div id="region1">
    <div class="user-entry">
        <?=$badge->criteria?>
        
        <?php if ($can_apply): ?>
        <h2><?= t("Apply for this badge") ?></h2>
            <?=form_open($this->uri->uri_string(), array('id' => 'badge-apply-form'))?>
        <label for="evidence"><?= t("Evidence - please select cloud which provides evidence") ?>
        <?= form_dropdown('evidence', $options) ?>
        <input type="submit" name="submit" id="submit" 
        class="submit" value="<?= t("Apply for badge")?>" />
        <?=form_close()?>

        <?php endif; ?>
    </div>    
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>  
</div>
