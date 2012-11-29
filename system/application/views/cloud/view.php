<script type="text/javascript" src="<?=base_url()?>_scripts/iframe_strip.js"></script>
<div class="grid headline">
    <div class="c1of2">
        <h1><?=$cloud->title ?></h1>
        <?php $this->load->view('cloud/options_block'); ?>
        <?php if ($cloud->event_date): ?>
            <?=format_date(_("!date"), $cloud->event_date) ?><br /><br />
        <?php endif; ?>  
        <?php if ($cloud->call_deadline): ?>
            <?=format_date(_("Deadline: !date"), $cloud->call_deadline) ?><br /><br />
        <?php endif; ?>
        
        <?php if($cloud->summary): ?>
            <p><?=$cloud->summary ?></p>
        <?php endif; ?>
        
        <?php if ($cloud->primary_url): ?>
            <div class="box" style="margin:30px 0 10px 0">
            
                <a href="<?= $cloud->primary_url ?>"><?= $cloud->primary_url ?></a>
            </div>
        <? endif; ?>
    </div>

    <div class="c2of2">
        <p class="created-by">
        <?=t("<abbr title='!definition'>Cloud</abbr> created by: !person-date",
          array('!person-date'=>NULL /*Hint - recurse. */, 
                '!definition' =>t("Clouds can be anything of relevance to learning and teaching"))) ?></p>

            <?php if ($cloud->picture): ?>
                <img src="<?= base_url() ?>image/user_32/<?= $cloud->user_id ?>" class="go2" alt=""/>
            <?php else: ?>
                <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" alt=""/>
            <?php endif; ?>
        <p><?=anchor("user/view/$cloud->id", $cloud->fullname, array(
                    'class' => 'author rdfa',
                    'xmlns:cc' =>'http://creativecommons.org/ns#',
                    'property' => 'cc:attributionName',
                    'rel' => 'cc:attributionURL',
        )) ?><br />
        <?= format_date("!date!", $cloud->created); ?></p>
    </div>
</div>

<div id="region1">

    <div class="user-entry">

        <?=$cloud->body?>
    </div> 

    <?php $this->load->view('content/content_block.php'); ?>
    <?php $this->load->view('embed/embed_block.php'); ?>
    

    <div class="grid">
        <h2><?= t("Contribute") ?></h2>
        <a name="contribute"></a> 
        <ul class="cloudstream-filter">
            <li>
            <?php if ($view == 'comments'): ?>
                <strong><?= t("Discussion") ?> (<?= count($comments) ?>)</strong>
            <?php else: ?>
                <?= anchor('cloud/view/'.$cloud->cloud_id.'/comments#contribute', 
                       t("Discussion").' ('.count($comments).')')?>
            <?php endif; ?>
            </li>
            <li>            
            <?php if ($view == 'links'): ?>
                <strong><?= t("Links") ?> (<?= count($links) ?>)</strong>
            <?php else: ?>
                <?= anchor('cloud/view/'.$cloud->cloud_id.'/links#contribute', 
                       t("Links").' ('.count($links).')')?>
            <?php endif; ?>
            </li>
            <li>            <?php if ($view == 'references'): ?>
                <strong><?= t("Academic References") ?> (<?= count($references) ?>)</strong>
            <?php else: ?>
                <?= anchor('cloud/view/'.$cloud->cloud_id.'/references#contribute', 
                       t("Academic References").' ('.count($references).')')?>
            <?php endif; ?></li>
        </ul>
    </div>

    <?php 
    switch ($view) {
        case 'comments'   : $this->load->view('cloud_comment/cloud_comments.php'); break;
        case 'links'      : $this->load->view('link/link_block.php'); break;
        case 'references' : $this->load->view('reference/references_block.php'); break; 
        default           : $this->load->view('cloud_comment/cloud_comments.php');
    } ?>
    
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <?php $this->load->view('tag/tag_block'); ?>
      <p class="add-link"><?= anchor('tag/add_tags/cloud/'.$cloud->cloud_id, t("Add a tag")) ?></p>
    <?php $this->load->view('cloud/cloudscapes_block'); ?>
    <?php $this->load->view('cloud/improve_this_cloud_block'); ?>
    <?php if ($this->config->item('x_gadgets')): ?>
        <?php $this->load->view('gadget/gadget_block.php'); ?> 
    <?php endif; ?>    
</div>
