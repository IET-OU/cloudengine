<script type="text/javascript" src="<?=base_url()?>_scripts/iframe_strip.js"></script>
<div class="grid headline">
    <div class="c1of2">
    
<img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" style="float: left;" class="badge"/> 
        <h1><?= t('Badge: ') ?><?=$badge->name ?></h1>
        <?php $this->load->view('badge/options_block'); ?>
        <p><?= $badge->description ?></p>
        <?php if ($badge->verifiers): ?>
            <p>
            <strong><?= t("Verifiers:") ?></strong>
            <?php foreach($badge->verifiers as $verifier): ?>
            <?= anchor('user/view/'.$verifier->user_id, $verifier->fullname) ?>
            <?php endforeach;?>
            </p> 
        <?php elseif ($badge->type == 'crowdsource'): ?>
           <p><?= t("This badge will be awarded when !num_approves users have 
                  approved the badge application", 
                  array('!num_approves' => $badge->num_approves)) ?></p>           

        <?php endif; ?>
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
        <?= anchor('badge/apply/'.$badge->badge_id, t("Apply for badge"), array('class'=>'buttonlink')) ?>

        <?php endif; ?>
    </div>    
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>  
<div class="box">
<h2><?= t("Applying for a badge") ?></h2>
<p><?= t("Once you have decided that you want to work towards a particular 
badge, make a note of the criteria and begin work.") ?></p>
<p>
<?= t("This may mean engaging in a course or activity. The badge issuer may have 
been quite specific about how you could provide evidence, but if they haven't, 
you must decide how best to do it. Your evidence might consist of a reflective 
blog post, or a photo, a document, a diagram, a screen shot, or an activity 
stream and you will need to be able to link to all your evidence via one URL - 
so it needs to be held online somewhere.") ?> </p>
<p><?= t("If you have a number of different pieces of evidence that you want to 
submit, the easiest way of doing this is to set up a Cloud. Use the text box to 
describe your evidence and how it meets the criteria and then 'Add content', 
'Add embedded content' or 'Add link' for as many pieces of evidence as you 
need. Then copy and paste the URL for you evidence Cloud into this page.") ?></p>
</div>
</div>
