#!/usr/bin/env php
<?php

/**
 * Commandline script to fix paths to Javascript, CSS, etc. in Cloudworks archive HTML.
 *
 * @author NDF, 05-July-2019
 * @copyright 2009-2019 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 */

define( 'DIR', 'cloud' ); // <<< TODO: edit me !! (about, auth, badge, blog, cloud, cloudscape )

define( 'CLOUDWORKS_ARCH', __DIR__ . '/../../cloudworks-ac-uk');

require_once __DIR__ . '/../vendor/autoload.php';

use Nette\Utils\Finder;

$_REPLACE = [
  '@https\:\/\/cloudworks\.ac\.uk\/\/?_design@' => '/_design',   // CSS
  '@https\:\/\/cloudworks\.ac\.uk\/them@'       => '/them',      // CSS etc. ~~ Was a BUG ?!
  '@https\:\/\/cloudworks\.ac\.uk\/_scripts@'   => '/_scripts',  // Javascripts.
  '@custom\.js"><\/script>\r?\n@' => 'custom.js"></script><script src="/_scripts/archive-fix.js"></script><!--AF-->',
  // '@archive-fix\.js"><\/script><!--AF--><script src="\/_scripts\/archive-fix.js"><\/script>@' => 'archive-fix.js"></script><!--AF-->', // TODO: whoops!
  '@<meta name="keywords" content="" \/>@'      => '',
  '@<script src="https:\/\/unp.+?gaad-widget.min.js"\r?\n\s+.+\}\'><\/script>@' => '<!--GAAD-->',
  "@<script> console.warn\('rel=nofollow count:', 0\) <\/script>@" => '<!--RNF-->',
];

foreach (Finder::findFiles(DIR . '/*/*.html', DIR . '/*.html')->from(CLOUDWORKS_ARCH) as $htmlFile) {
	echo ">> " . $htmlFile . PHP_EOL;

    $input = file_get_contents($htmlFile);

    $output = $input;

    foreach ($_REPLACE as $regex => $replace) {
        $output = preg_replace($regex, $replace, $output);
    }

    $bytes = file_put_contents($htmlFile, $output);
}

// End.
