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
$page = preg_replace('#<!DOCTYPE.*?>#', '', $page);
?>

<p><?= anchor('admin/panel', 'Return to admin panel') ?>
 &bull; <a href="?name=value" title="Add a GET parameter">[get]</a>
 &bull; <a href="#PHP">PHP info</a> &bull; <a href="#CI">Site config</a> 
</p>

<div id="PHP" class="cw_phpinfo" style="font-size:1.4em;">

<?= $page; ?>

</div>


<h2 id="CI">Site configuration</h2>
<ul class="CI config">
<?php
    $all_config = $this->config->config; 
    foreach ($all_config as $item => $value): ?>
	<li><?=$item ?> <span class="val">= <?=xml_convert($value) ?></span></li>
<?php endforeach; ?>
</ul><p></p>

</ul>
