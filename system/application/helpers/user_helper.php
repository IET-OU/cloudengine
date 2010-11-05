<?php 
/**
 * Helper file for user related functions
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Format
 */

/**
 * Takes some text and truncates it to at most a hundred characters, first stripping out 
 * any HTML and making sure the truncation happens in a space between words
 *
 * @param string $text
 * @return string The truncated string 
 */
function get_user_full_name($user_id) {
    $text = strip_tags($text);
    // Strip HTML
    $max_chars = 100;
    $text = $text." ";
    // Truncate the string
    $text = substr($text, 0, $max_chars);
    // Truncate again to the last space
    $text = substr($text,0, strrpos($text,' '));
    $text = $text."...";
    return $text; 
}

