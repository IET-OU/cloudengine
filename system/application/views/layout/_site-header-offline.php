<?php $current_page = str_replace(base_url(), '', current_url()); ?> 
<div id="site-header-content">
        <div id="skip">
        <a href="#content"><?=t('Skip navigation') ?></a> </div>
        <?php if ($current_page != ''): ?>
        <a rel="home" href="<?=base_url()?>">
        <?php endif; ?>
        <img id="link-home" src="<?=base_url()?><?= $this->config->item('theme_logo') ?>" alt="<?=t("!site-name! home page") ?>" />
        <?php if ($current_page  != '/'): ?>
        </a>
        <?php endif; ?>
 
    </div>