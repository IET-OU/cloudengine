<div id="region1">
	<div class="grid g1">
		<div class="c1of1">
		    <h1><?= t("!site-name! home page") ?></h1>
		    <?php if ($this->config->item('tag_line')): ?>
		        <p class="welcome">
                <?= t($this->config->item('tag_line')) ?></p>
            <?php endif; ?>

            <?php /* @i18n. Please leave this placeholder for 'tag_line'! */
_('Welcome to !site-name!, a place to share, find and discuss learning and teaching ideas and experiences.'); ?>

		    <?php $this->load->view('cloudscape/featured_cloudscapes'); ?>
		</div>

        <div class="grid">
            <div class="c1of2">
            <?php $this->load->view('events/events_block'); ?>
            </div>
            <div class="c2of2">
            <?php $this->load->view('cloud/popular_block'); ?>
            </div>
        </div>

	</div>
</div>
<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <?php $this->load->view('site_news_block'); ?>
    <?php $this->load->view('event/cloudstream_block'); ?>
    <?php $this->load->view('cloud/active'); ?>
</div> 





