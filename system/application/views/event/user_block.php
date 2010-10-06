<div class="grid">
<h2 id="cloudstream"><?=t("Cloudstream")?></h2>

<?php $this->load->view('event/header'); ?>
</div>
<div class="grid">
<?php $this->load->view('event/event_stream'); ?>
   
</div>
<p>
<?= anchor('event/user/'.$user->id.'/'.$type, t("More")) ?> | 
<a href="<? base_url() ?>event/user_rss/<?= $user->id  ?>/<?= $type ?>" class="rss"><?=t("RSS")?></a></p>