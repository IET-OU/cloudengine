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
            <?php $this->load->view('cloudscape/options_block'); ?>
            
            <!-- Event info if the cloudscape is an event -->
            <p>
                <?= $cloudscape->dates ?>
            
                <?php if ($cloudscape->location): ?><br /> <?= $cloudscape->location ?><?php endif; ?>                
                
            <br /><br />        
            <?php if($cloudscape->summary): ?><?=$cloudscape->summary ?><br /><br /><?php endif; ?>
        
            <?php $this->load->view('cloudscape/cloudscape_image'); ?>
        
            <ul class="skip-links">
                <li><a href="#user-entry"><?=t("Content")?></a></li>
                <li><a href="#cloudstream">Cloudstream</a></li>
                <li><a href="#clouds-in-cloudscape">Clouds</a></li>       
                <?php if ($tweets): ?><li><a href="#tweets"><?=t("Tweets")?></a></li><?php endif; ?>
            </ul>
        </div>
        <div class="c2of2">
            <p class="created-by"><?=t("<abbr title='!definition'>Cloudscape</abbr> created by: !person",
            array('!definition'=>"Cloudscapes are collections of clouds about a certain topic", '!person'=>''))?></p>
    
            <?php if ($cloudscape->picture): ?>
                <img src="<?=base_url() ?>image/user/<?= $cloudscape->user_id ?>" alt="" class="go2" />
            <?php else: ?>
                <img src="<?=base_url() ?>_design//avatar-default-32.jpg" alt="" class="go2" />
            <?php endif; ?>
                <p><a href="<?=base_url() ?>user/view/<?= $cloudscape->id ?>"><?= $cloudscape->fullname ?></a><br />
            <?= format_date("!date!", $cloudscape->created) ?></p>
    
        </div>
    </div>
</div>

<div id="region1">
    <div class="grid user-entry" id="user-entry">
        <?= $cloudscape->body ?>
    </div>
    <div class="grid">
        <?php $this->load->view('cloudscape/cloud_block'); ?>
    </div>
   <?php $this->load->view('event/cloudscape_block'); ?> 
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <?php $this->load->view('tag/tag_block'); ?>
    <p class="add-link"><?= anchor('tag/add_tags/cloudscape/'.$cloudscape->cloudscape_id, t("Add a tag")) ?></p>

    <?php if ($post_permission): ?>
    	<p class="create-cloud">
    	<?= anchor('cloud/add/'.$cloudscape->cloudscape_id, t("Create Cloud in this Cloudscape")) ?>
        </p>
    <?php endif; ?>
    
    <ul class="cloudscapes">
    <?php if ($admin_permission): ?>
        <li><?= anchor('cloudscape/permissions/'.$cloudscape->cloudscape_id, t("Permissions"))?></li>
        <li><?= anchor('cloudscape/manage_clouds/'.$cloudscape->cloudscape_id, t("Manage clouds")) ?></li>
    	<li><?= anchor('cloudscape/manage_sections/'.$cloudscape->cloudscape_id, t("Manage sections")) ?></li>       
    	<?php if ($cloudscape->start_date && $this->config->item('x_email_events_attending')): ?>
    	<li><?= anchor('cloudscape/email_attendees/'.$cloudscape->cloudscape_id, t('Email attendees')) ?></li>
    	<?php endif; ?>
    <?php endif; ?>
    <li><?= anchor('event/cloudscape/'.$cloudscape->cloudscape_id, t("Cloudstream for this cloudscape")) ?></li> 
    </ul>  
    <?php $this->load->view('events/attendees_block'); ?>
    <?php $this->load->view('cloudscape/followers_block'); ?>
    <?php $this->load->view('cloudscape/tweets.php'); ?>
</div>
