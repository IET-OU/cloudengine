<?php
/**
 * Helper for commandline tools.
 *
 * @copyright 2009, 2017 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @author  Nick Freear, 2017-11-29.
 * @link https://github.com/bcit-ci/CodeIgniter/blob/master/system/core/Common.php#L369-L383
 */

if ( ! function_exists('is_cli'))
{
	/**
	 * Is CLI?
	 *
	 * Test to see if a request was made from the command line.
	 *
	 * @return 	bool
	 */
	function is_cli()
	{
		return (PHP_SAPI === 'cli' OR defined('STDIN'));
	}
}
