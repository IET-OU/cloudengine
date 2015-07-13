<div class="grid">
    <h2 id="cloudstream">Cloudstream</h2>
    <?php $this->load->view('event/header'); ?>
</div>
<div class="grid">
    <?php $this->load->view('event/event_stream'); ?>
    <p>
    <?= anchor('event/cloudscape/'.$cloudscape->cloudscape_id.'/'.$type, t("More activity")) ?>
    | <a class="rss" href="<?= $rss ?>"><?php /*/Translators: RSS Really simple syndication.*/ ?><?=t("RSS")?></a></p>
</div>
