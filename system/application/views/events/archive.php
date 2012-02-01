<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Events Archive")?></h1>
    </div>
</div>

<div id="region1">
        <ul class="cloudstream-filter">
            <li>
            <?php if ($view == 'cloudscapes'): ?>
                <strong><?= t("Conferences") ?> </strong>
            <?php else: ?>
                <?= anchor('events/archive/cloudscapes', 
                       t("Conferences"))?>
            <?php endif; ?>
            </li>
            <li>            
            <?php if ($view == 'clouds'): ?>
                <strong><?= t("Workshops, Seminars and Talks") ?></strong>
            <?php else: ?>
                <?= anchor('events/archive/clouds', 
                       t("Workshops, Seminars and Talks"))?>
            <?php endif; ?>
            </li>
            <li>            
            <?php if ($view == 'calls'): ?>
                <strong><?= t("Deadlines") ?> </strong>
            <?php else: ?>
                <?= anchor('events/archive/calls', 
                       t("Deadlines"))?>
            <?php endif; ?></li>
        </ul>
        <br />
        <br />
         <br />
        <?php    switch ($view) {
        case 'cloudscapes' : $this->load->view('events/cloudscapes.php'); break;
        case 'clouds'      : $this->load->view('events/clouds.php'); break;
        case 'calls'       : $this->load->view('events/calls.php'); break; 
        default            : $this->load->view('events/cloudscapes.php');
    } ?>
<p><?= anchor('events/view', t('View current and upcoming events')) ?>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <?php $this->load->view('events/add_event_block'); ?>

</div>