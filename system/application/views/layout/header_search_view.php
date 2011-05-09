<?php
  $navigation = isset($navigation) ? $navigation : false;

  @header("Content-Type: text/html; charset=".config_item("charset"));
  // We're moving to HTML5 (BB issue #62).

?><!DOCTYPE html><html <?=$this->lang->lang_tag()?>><head>
<?php $this->load->view('layout/_site-head.php'); ?>
</head>
<body id="<?= $navigation ?>">
<?php $page = $_SERVER['REQUEST_URI']; ?>

<div id="site">
    <div id="site-body">
        <div id="page">
        <div id="content"></div>
     
        