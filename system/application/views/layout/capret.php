<?php
/*
 GA: https://gist.github.com/4071270
 Piwik: https://gist.github.com/4066841
*/
?>
<?php if ($this->config->item('x_capret')): ?>
    <?= anchor('about/capret', t('About CaPRéT'), array(
            'class' => 'capret-link',
            'title' =>
                t('This site uses cut and paste reuse tracking')
                .' (capret-piwik)',
	)) ?> |
    <?php if('piwik' == $this->config->item('capret_variant')): ?>
        <script
        src="http://track.olnet.org/capret/build/capret-piwik.min.js"
        data-piwik_idsite="<?=config_item('capret_id') ?>"
        data-piwik_debug="1"
        ></script>
    <?php else: ?>
        <script
        src="http://track.olnet.org/capret/build/capret-ga.min.js"
        data-utm_ac="<?=config_item('capret_id') ?>"
        data-utm_debug="1"
        ></script>
    <?php endif; ?>
<?php endif; ?>
