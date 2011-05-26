<?php $rss = isset($rss) ? $rss : false; ?>

<?php if (!config_item('x_live') || strrpos(current_url(),'search_view') ) : ?>
<meta name="ROBOTS" content="noindex,nofollow" />
<?php endif; ?>
<meta http-equiv="Content-Type" content="text/html; charset=<?=config_item("charset") ?>" />
<?=$this->lang->content_lang() ?>

    <title><?= $title ?> - <?= $this->config->item('site_name') ?></title>
    <link rel="stylesheet" href="<?=base_url() ?>_design/styles_1_1.css" type="text/css" />
    <?php if(config_item('theme_stylesheet')): ?>
    <link rel="stylesheet" href="<?=base_url().config_item('theme_stylesheet') ?>" type="text/css" />
    <?php endif; ?>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
<?=$this->lang->meta_link() ?>
    <link rel="shortcut icon" href="<?=base_url().config_item('theme_favicon') ?>" />
    <?php if($rss): ?>
    <link rel="alternate" type="application/rss+xml" title="<?= $this->config->item('site_name') ?> - <?= $title ?>" href="<?= $rss ?>" />
    <?php endif; ?>
    
    <!--[if IE 6]>
    <style type="text/css">
    div#site {width:940px; /* Because IE6 doesn't support max-width */ }
    div#site-header-content {width:940px;}
    </style>
    <![endif]-->

	<script type="text/javascript" src="<?=base_url()?>_scripts/jquery/js/jquery-1.4.2.min.js"></script>
    <?php if ($this->config->item('x_message')): ?>
        <script type="text/javascript" src="<?=base_url()?>_scripts/jquery/js/jquery-ui-1.8.6.custom.min.js"></script>
        <link type="text/css" href="<?=base_url()?>_scripts/jquery/css/redmond/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <?php endif; ?>
  <script type="text/javascript" src="<?=base_url()?>_scripts/buildpager.jquery.js"></script>
  <script type="text/javascript" src="<?=base_url()?>_scripts/search.js"></script>
  <script src="<?=base_url()?>_scripts/custom.js" type="text/javascript"></script>