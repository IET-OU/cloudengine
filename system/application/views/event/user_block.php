<div class="grid">
<h2 id="cloudstream"><?=t("Cloudstream")?></h2>

<?php $this->load->view('event/header'); ?>
</div>
<div class="grid">
<?php $this->load->view('event/event_stream'); ?>
   
</div>
<p>
<?= anchor("event/user/$user->id/$type", t('More')) ?> | 
<?= anchor("event/user_rss/$user->id/$type", t('RSS'), 'class="rss"') ?></p>