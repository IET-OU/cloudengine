#!/usr/bin/env php
<?php
/**
 * CLI run by Mercurial's update hook (or as a crontab).
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 */
/* Configuration - add this to ~/.hgrc

[hooks]
;;update = ~/work/cloudengine/system/application/cli/update-hook.php
update = php /var/www/cloudengine/system/application/cli/update-hook.php

*/
if('cli'!=php_sapi_name()) die(basename(__FILE__).": Must run as cli."); #Security.

echo 'update-hook'.PHP_EOL;

define('APP_CLI', basename(__FILE__));
define('APPPATH', dirname(__FILE__).'/../');

require_once APPPATH.'libraries/Hglib.php';

echo Hglib::write_revision();

?>