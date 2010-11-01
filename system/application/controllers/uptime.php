<?php
/**
 * Controller for a responder for athe Uptime Server Monitoring service
 * Requires the following line in application/config/routes.php
 * <code>
 *   $route['uptime.txt'] = 'uptime';
 * </code>
 * @link http://uptime.openacs.org/
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Uptime
 */
class Uptime extends Controller {

	/**
	 * The uptime page
	 *
	 */
	public function index() {
		$DB = $this->load->database('default', $return = TRUE);
		@header('Content-Type: text/plain; charset=UTF-8');
		if (!$DB) {
			  @header('HTTP/1.1 503 Service Unavailable'); #Actually, CI never reaches this point!
			  die('A Database Error Occurred'.PHP_EOL);
		}
		echo 'success'.PHP_EOL;
	}
}
