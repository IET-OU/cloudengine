<?php
/** An application config / phpinfo page, for administrators
*
* Note, we need ob_start to make the phpinfo output appear at the correct place on the page.
* While we're at it, we'll remove some of the 'page-level' tags from the output.
*/
ob_start();

phpinfo();

$page = ob_get_clean();
$page = str_ireplace(array('<html>','<head>','<body>', '</html>','</head>','</body>'), '', $page);
$phpinfo = preg_replace('#<!DOCTYPE.*?>#', '', $page);
?>

<p><?= anchor('admin/panel', 'Return to admin panel') ?>
 &bull; <a href="#CI">Site config</a> &bull; <a href="?name=value" title=
  "Add a GET parameter">[get]</a> &bull; <a href="#PHP">PHP info</a>  
</p>


<h2 id="CE">Versions</h2>
<ul class="CE version">
  <li>CloudEngine version:
<abbr title="Version in code"><?=APP_VERSION; ?></abbr>.
<?php
if ($hg) {
    echo "<abbr title='Mercurial tag / changeset'>".anchor($hg['url'],
        $hg['tag'].' / '.$hg['changeset'].' ('.$hg['date'].')').'</abbr>';
}
?></li>
  <li>CodeIgniter version: <?= CI_VERSION ?></li>
</ul><p></p>


<h2 id="CI">Site configuration</h2>
<ul class="CI config">
<?php
    $all_config = $this->config->config; 
    foreach ($all_config as $item => $value): ?>
	<li><?=$item ?> <span class="val">= <?=xml_convert($value) ?></span></li>
<?php endforeach; ?>
</ul><p></p>


<div id="PHP" class="cw_phpinfo" style="font-size:1.4em;">

<?= $phpinfo; ?>

</div>
