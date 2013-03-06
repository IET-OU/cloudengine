<script type="text/javascript" src="<?=base_url()?>_scripts/iframe_strip.js"></script>
<div class="grid headline">
    <div class="c1of2">
    
<img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" style="float: left;" class="badge"/> 
        <h1><?= t('Badge: ') ?><?=$badge->name ?></h1>
		<p><?= anchor('badge/view/'.$badge->badge_id, t("Back to badge")) ?></p>
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
<h2><?= t("The following people have been awarded this badge:") ?></h2>
<?php if ($users): ?>
<?php foreach ($users as $user): ?>
<p>
            <?php if ($user->picture): ?>
                <img src="<?= base_url() ?>image/user_32/<?= $user->user_id ?>" class="go2" alt=""/>
            <?php else: ?>
                <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" alt=""/>
            <?php endif; ?>
       &nbsp; <?=anchor("user/view/$user->id", $user->fullname) ?><br />
</p>
		<?php endforeach; ?>
<?php else: ?>
<?= t("Nobody has been awarded this badge yet") ?>
<?php endif; ?>

    
</div> 


</div>
