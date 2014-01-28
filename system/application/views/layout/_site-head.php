<?php $rss = isset($rss) ? $rss : false; ?>

<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
<?php if (!config_item('x_live') || strrpos(current_url(),'search_view') ) : ?>
<meta name="ROBOTS" content="noindex,nofollow" />
<?php endif; ?>
<meta charset="<?=config_item("charset") ?>" />
<?php /*$this->lang->content_lang()*/ ?>

    <title><?= isset($title) ? $title : '' ?> - <?= $this->config->item('site_name') ?></title>
    <link rel="stylesheet" href="<?=base_url() ?>_design/styles_1_1.css" />
    <?php if(config_item('theme_stylesheet')): ?>
    <link rel="stylesheet" href="<?=base_url().config_item('theme_stylesheet') ?>" />
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

<?php if (! isset($no_javascript)): ?>
	<script src="<?=base_url()?>_scripts/jquery/js/jquery-1.4.2.min.js"></script>
    <?php if ($this->config->item('x_message')): ?>
        <script src="<?=base_url()?>_scripts/jquery/js/jquery-ui-1.8.6.custom.min.js"></script>
        <link href="<?=base_url()?>_scripts/jquery/css/redmond/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <?php endif; ?>
    <?php if ($this->uri->segment(1) == 'search'): ?>
      <script src="<?=base_url()?>_scripts/buildpager.jquery.js"></script>
      <script src="<?=base_url()?>_scripts/search.js"></script>
    <?php endif; ?>    
  <script src="<?=base_url()?>_scripts/custom.js"></script>
<?php endif; ?>
