<?php
    $theme_url = base_url()."themes/aurora/";
?>
<!DOCTYPE html><html lang="en"><meta charset="UTF-8" />
<title><?php echo $site_name ?> <?php if (isset($step)) { echo " - step ".($step +1); } ?></title>
<meta name="ROBOTS" content="noindex,nofollow" />

  <link rel="stylesheet" href="<?php    echo base_url() ?>_design/styles_1_1.css" type="text/css" />
  <link rel="stylesheet" href="<?php    echo $theme_url ?>styles.css" type="text/css" />
  <link rel="shortcut icon" href="<?php echo $theme_url ?>favicon-aurora.ico" />

  <!--[if IE 6]>
  <style type="text/css">
  div#site {width:940px; /* Because IE6 doesn't support max-width */ }
  div#site-header-content {width:940px;}
  </style>
  <![endif]-->

<style type="text/css">
  p,pre,li{ font-size:1.2em; }
  body h1 { font-size:1.6em; color: #fff; background:#1E466A; padding:.4em 0 .4em .4em; margin:0 0 .8em; }
  code{ font-family:monospace; }
  pre { font-family:monospace; margin:.6em 4em; }
  pre em { font-weight:bold; font-style:normal; }
  .messages li { margin:8px 0; padding:3px; border:1px solid rgb(105,176,35); }
  .error { padding:3px; border:1px solid #a33; font-size:1.3em; }
</style>

<body id="installer">
<div id="site-header">
  <div id="site-header-content">
    <img id="link-home" src="<?php echo $theme_url ?>aurora-logo.gif" alt="<?php echo $site_name ?>" />
  </div>   
</div>

<div id="site">
  <div id="site-body">
    <!--div id="page"-->
      <div id="__region1">
