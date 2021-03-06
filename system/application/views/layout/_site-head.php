<?php $rss = isset($rss) ? $rss : false; ?>

<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
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
    <meta name="copyright" content="© 2009-2020 The Open University (IET)." />
    <meta name="keywords" content="archive, education, e-learning, online-learning" />
    <meta name="description" content="Welcome to Cloudworks, a place to share, find and discuss learning and teaching ideas and experiences. (Archive)" />
    <meta name="archive-date" content="2019-06-28T01:00:00Z" />
    <link rel="archive-home-url" href="<?=base_url() ?>" />

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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<?php endif; ?>
