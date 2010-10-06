<div id="region1">
<h1><?=t("Cloudstream for !title", 
    array('!title'=>"<a href='".base_url()."cloudscape/view/$cloudscape->cloudscape_id'>$cloudscape->title</a>"))?></h1>
    
    <div class="grid">
        <?php $this->load->view('event/header'); ?>
        </div>
        <div class="grid">
        <?php if ($events): ?>
            <?php $this->load->view('event/event_stream'); ?>
        <?php else: ?>
            <p><?=t("No events in this cloudstream.")?></p>
        <?php endif; ?>
    </div>
     <p>
     <?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, t("Back to cloudscape")) ?>
     | <a class="rss" href="/event/cloudscape_rss/<?= $cloudscape->cloudscape_id ?>/<?= $type ?>"><?=t("RSS")?></a></p>          
</div>
<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <p>
<?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
    array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?></p>

</div> 