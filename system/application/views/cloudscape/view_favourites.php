

<?php if ($cloudscape->colour): ?>
<style type="text/css">
#page #cloudscapes .block {
background: <?= $cloudscape->colour ?>;

}

#cloudscapes h2 {
background: <?= $cloudscape->colour ?>;
}

#site #page  #region2 h2 {
background: <?= $cloudscape->colour ?>;
}

</style>
<?php endif; ?>

<script type="text/javascript" src="<?=base_url()?>_scripts/iframe_strip.js"></script>
<div class="grid headline block cloudscape">
    <div class="headline-wrap">
        <div class="c1of2">
            <h1><?= $cloudscape->title ?> </h1>
        </div>
        <div class="c2of2">
            <p class="created-by"><?=t("<abbr title='!definition'>Cloudscape</abbr> created by: !person",
            array('!definition'=>"Cloudscapes are collections of clouds about a certain topic", '!person'=>''))?></p>
    
            <?php if ($cloudscape->picture): ?>
                <img src="<?=base_url() ?>image/user/<?= $cloudscape->user_id ?>" alt="" class="go2" />
            <?php else: ?>
                <img src="<?=base_url() ?>_design//avatar-default-32.jpg" alt="" class="go2" />
            <?php endif; ?>
            <p><?=anchor('user/view/'. $cloudscape->id, $cloudscape->fullname, array(
                        'class' => 'author rdfa',
                        'xmlns:cc' =>'http://creativecommons.org/ns#',
                        'property' => 'cc:attributionName',
                        'rel' => 'cc:attributionURL',
            )) ?></a><br />
            <?= format_date("!date!", $cloudscape->created) ?></p>
    
        </div>
    </div>
</div>

<div id="region1">
    <div class="grid">
<div class="box">
<h2 id="clouds-in-cloudscape"><?=t("Clouds in this Cloudscape")?></h2>

<?php if (count($clouds) > 0):?>
    <ul class="clouds">
        <?php  foreach ($clouds as $cloud):?>
            <li>
                <a href="<?= base_url() ?>cloud/view/<?= $cloud->cloud_id ?>" ><?= $cloud->title ?> 
                <?php if ($cloud->total_favourites != 0): ?>
                    (<?=plural(_("!count favourite"), _("!count favourites"), $cloud->total_favourites) ?>)
                <?php endif; ?>
                </a> 
            </li>
            <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?=t("No clouds yet")?></p>
<?php endif; ?>
</div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
</div>
