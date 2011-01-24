<?php
  $navigation = isset($navigation) ? $navigation : false;

  @header("Content-Type: text/html; charset=".config_item("charset"));
  // We're moving to HTML5 (BB issue #62).

?><!DOCTYPE html><html lang="en"><head>
<?php $this->load->view('layout/_site-head.php'); ?>
</head>
<body id="<?= $navigation ?>">
<?php $page = $_SERVER['REQUEST_URI']; ?>
<div id="site-header">
<?php $this->load->view('layout/_site-header'); ?>   
</div>

<div id="site">
    <div id="site-body">
        <div id="page">
        <div id="content"></div>
        <?php if (!config_item('x_live')): ?>
            <p class="test_install warn"> <?= $this->config->item('test_install_message') ?></p>
        <?php endif; ?>
        <?php //if the site is in offline mode, show a message to remind admin users ?>
        <?php if (!config_item('site_live')): ?>
            <p class="test_install warn"> <strong><?= $this->config->item('offline_message_admin') ?></strong></p>
        <?php endif; ?>        
        