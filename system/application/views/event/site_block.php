            <div class="grid">
            <h2 id="cloudstream"><?=t("Cloudstream")?></h2>
            <?php $this->load->view('event/home_header'); ?>
            </div>
            
            <div class="grid">
            <?php $this->load->view('event/event_stream'); ?>
      <p><a href="<?=base_url() ?>event/site/"><?=t("More activity")?></a>
        <?php if ($this->auth_lib->is_logged_in()): ?>| <a href="<?=base_url() ?>event/following"><?=t("Your cloudstream")?></a><?php endif; ?>
      | <a href="<?=base_url() ?>event/site_rss/<?= $type ?>" class="rss"><?=t("RSS")?></a></p>
            </div>