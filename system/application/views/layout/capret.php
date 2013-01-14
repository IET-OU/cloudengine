<?php
/*
 CaPRéT/ Track OER plugin.

 GA: https://gist.github.com/4071270
 Piwik: https://gist.github.com/4066841
*/
?>
<?php if ($this->config->item('x_capret')):

    $capret_analytics_id = $this->config->item('capret_analytics_id');
    $capret_variant = $this->config->item('capret_variant');
    $capret_variant = str_replace(array('google', 'utm'), 'ga', $capret_variant);
    ?>
    <?= anchor($this->config->item('capret_about_url'), t('About CaPRéT'), array(
            'class' => 'capret-link',
            'title' =>
                t('This site uses cut and paste reuse tracking')
                ." (capret-$capret_variant)",
	)) ?> |
    <?php if (! preg_match('/view|about|support|blog/', $this->uri->uri_string())) { ?><span id="capret-no-js" title="NOT this page."></span><?php return; } ?>

    <?php if ('piwik' == $capret_variant): ?>
        <script
        src="http://track.olnet.org/capret/build/capret-piwik.min.js"
        data-piwik_idsite="<?=$capret_analytics_id ?>"
        data-piwik_debug="1"
        ></script>
    <?php elseif ('ga' == $capret_variant): ?>
        <script
        src="http://track.olnet.org/capret/build/capret-ga.min.js"
        data-utm_ac="<?=$capret_analytics_id ?>"
        data-utm_debug="1"
        ></script>
    <?php else: //'classic' ?>
        <script src="http://track.olnet.org/capret/build/capret.min.js"></script>
    <?php endif; ?>
<?php endif; ?>
