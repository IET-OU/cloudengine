<div class="grid headline">
    <div class="c1of2">
        <h1><?=$cloud->title ?></h1>

        
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
        <p><?=anchor("user/view/$cloud->id", $cloud->fullname) ?><br />
        <?= format_date("!date!", $cloud->created); ?></p>
    </div>
</div>

<div id="region1">

    <div class="user-entry">

        <?=$cloud->body?>
    </div> 

    <?php $this->load->view('content/content_block_search_view.php'); ?>
    <?php $this->load->view('embed/embed_block_search_view.php'); ?>

    <?php 
        $this->load->view('cloud_comment/cloud_comments_search_view.php');
        $this->load->view('link/link_block_search_view.php'); 
        $this->load->view('reference/references_block_search_view.php');  
    ?>
    
</div> 

<div id="region2">
    <?php $this->load->view('tag/tag_block_search_view'); ?>
</div>
