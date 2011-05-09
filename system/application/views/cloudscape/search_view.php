<div class="grid headline block cloudscape">
    <div class="headline-wrap">
        <div class="c1of2">
            <h1><?= $cloudscape->title ?> </h1>
            
            <!-- Event info if the cloudscape is an event -->
            <p>
                <?= $cloudscape->dates ?>
            
                <?php if ($cloudscape->location): ?><br /> <?= $cloudscape->location ?><?php endif; ?>                
                
            <br /><br />        
            <?php if($cloudscape->summary): ?><?=$cloudscape->summary ?><br /><br /><?php endif; ?>
        
        </div>
        <div class="c2of2">
            <p class="created-by"><?=t(" !person",
            array('!definition'=>"Cloudscapes are collections of clouds about a certain topic", '!person'=>''))?></p>
    
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
        <?php $this->load->view('cloudscape/cloud_block_search_view'); ?>
    </div>
</div> 


<div id="region2">
    <?php $this->load->view('tag/tag_block_search_view'); ?>
</div>

