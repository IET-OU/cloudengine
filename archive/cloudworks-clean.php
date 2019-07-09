#!/usr/bin/env php
<?php

/**
 * Commandline script to fix paths to Javascript, CSS, etc. in Cloudworks archive HTML.
 *
 * @author NDF, 05-July-2019
 * @copyright 2009-2019 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 */

// (Tried :~ https://stackoverflow.com/questions/11968244/reading-line-by-line-from-stdin )

define( 'DIR', trim($argv[ $argc - 1], ' /') ); // <<< TODO: edit me !! (about, auth, badge, blog, cloud, cloudscape )
define( 'CLOUDWORKS_ARCH', __DIR__ . '/../../cloudworks-ac-uk/');

$_REPLACE = [
  '@https\:\/\/cloudworks\.ac\.uk\/\/?_design@' => '/_design',   // CSS
  '@https\:\/\/cloudworks\.ac\.uk\/them@'       => '/them',      // CSS etc. ~~ Was a BUG ?!
  '@https\:\/\/cloudworks\.ac\.uk\/_scripts@'   => '/_scripts',  // Javascripts.

  // '@custom\.js"><\/script>\r?\n@' => 'custom.js"></script><script src="/_scripts/archive-fix.js"></script><!--AF-->',
  // '@archive-fix\.js"><\/script><!--AF--><script src="\/_scripts\/archive-fix.js"><\/script>@' => 'archive-fix.js"></script><!--AF-->', // TODO: whoops!
  '@<meta name="keywords" content="" \/>@'      => '',
  '@<script src="https:\/\/unp.+?gaad-widget.min.js"\r?\n\s+.+\}\'><\/script>@' => '<!--GAAD-->',
  "@<script> console.warn\('rel=nofollow count:', 0\) <\/script>@" => '<!--RNF-->',

  '@rel="(stylesheet|shortcut icon)"@' => 'rel="X-$1"',
  '@rel="X-stysheet"@' => 'rel="X-stylesheet"',
  '@<script src="\/_scripts\/custom.js"><\/script>(<script src="\/_scripts\/archive-fix.js"><\/script>)?(<!--AF-->)?@' => '<script src="/cloudworks-ac-uk/loader.js"></script>',
];

require_once __DIR__ . '/../vendor/autoload.php';

use Nette\Utils\Finder;

if ($argc < 2) {
    exit( 1 );
}
printf("Cloudworks archive\n\n> Input directory:  %s\n\nContinue cleaning? y or n:\n", DIR);

if (trim(fgets(STDIN)) !== 'y') {
    echo "Exiting\n";
    exit( 1 );
}

$count = 0;

foreach (Finder::findFiles('*.html')->from(CLOUDWORKS_ARCH . DIR) as $htmlFile) {
    // echo ">> " . $htmlFile . PHP_EOL;
    echo '.';

    $input = file_get_contents($htmlFile);

    $output = $input;

    foreach ($_REPLACE as $regex => $replace) {
        $output = preg_replace($regex, $replace, $output);
    }

    $bytes = file_put_contents($htmlFile, $output);

    $count++;
}
echo "\n\n";

echo "Count of HTML files processed:  $count\n";

// End.
