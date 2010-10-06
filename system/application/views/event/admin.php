<div id="region1">
    <h1><?= $title ?></h1>
    
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
     <p><a href=""><?=t("Back to cloudscape")?></a> | <a class="rss" href="<?= $rss ?>"><?=t("RSS")?></a></p>          
</div>
<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <p>You can also search for <?= anchor('user/people', 'people') ?> and <?= anchor('user/institution_list', 'institutions') ?></p>

</div> 