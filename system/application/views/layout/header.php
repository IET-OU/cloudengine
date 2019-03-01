<?php
  $navigation = isset($navigation) ? $navigation : false;

  @header("Content-Type: text/html; charset=".config_item("charset"));
  // We are using the HTML5 Doctype below, but not ready to use all HTML5
  // elements yet as have not included javascript shim for IE fallback

  $this->lang->content_lang($header = TRUE);

?><!DOCTYPE html><html <?=$this->lang->lang_tag()?>><head>
<?php $this->load->view('layout/_site-head.php'); ?>
</head>
<body id="<?= $navigation ?>" class="<?php echo config_item('readonly') ? 'readonly' : ''; ?>">
<?php $page = $_SERVER['REQUEST_URI']; ?>
<div id="site-header">
<?php $this->load->view('layout/_site-header'); ?>
</div>

<div id="site">
    <div id="site-body">
        <div id="page">
        <div id="content">
        <?php if (config_item('readonly')): ?>
            <p class="warn readonly-message"> The web-site is currently in readonly mode. Login and registration are disabled. </p>
        <?php endif; ?>
        <?php if (!config_item('x_live')): ?>
            <p class="test_install warn"> <?= $this->config->item('test_install_message') ?></p>
        <?php endif; ?>
        <?php //if the site is in offline mode, show a message to remind admin users ?>
        <?php if (!config_item('site_live')): ?>
            <p class="test_install warn"> <strong><?= $this->config->item('offline_message_admin') ?></strong></p>
        <?php endif; ?>

        <?php if (config_item( 'gaad_widget' )): ?>
            <div id="id-gaad-widget"></div>
        <?php endif; ?>
