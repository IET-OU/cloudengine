<?php $navigation = isset($navigation) ? $navigation : false; ?>

<?php
  @header("Content-Type: text/html; charset=".config_item("charset"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
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