<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * API/ RSS/ iCal helper functions.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 */


/**
 * Bug #206, Clouds use a Unix epoch, Cloudscapes use SQL datetime :(.
 *
 * @return integer A Unix timestamp.
 */
function safe_date($time) {
  return preg_match('/^\d{4}-\d{2}/', $time) ? strtotime($time) : $time;
}


/**
 * Generate a FOAF sha1 sum of an email address.
 * http://xmlns.com/foaf/spec/#term_mbox_sha1sum
 *
 * @return string
 */
function mbox_sha1sum($email) {
  return sha1(strtolower('mailto:'. $email));
}

