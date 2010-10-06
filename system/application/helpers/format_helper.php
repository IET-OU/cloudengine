<?php 
/**
 * Helper file for formatting text for the site. 
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
function truncate_content($text) {
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

/**
 * Takes a timestamp and returns a string indicating how many days, hours or minutes ago the time was 
 * (or returns 'moments ago' if it is less than a minute ago). 
 *
 * @param int $timestamp A Unix timestamp
 * @return string The string saying how long ago the time was
 */
function time_ago($timestamp) {
    $timespan = time() - $timestamp;
    $seconds_in_a_day = 86400;
    $seconds_in_an_hour = 3600;
    $seconds_in_a_minute = 60;
    if ($timespan > $seconds_in_a_day) {
        $daysago = floor($timespan/$seconds_in_a_day);
        if ($daysago > 6) {
            $timeago = format_date(_("!event on !date!"), $timestamp);
        } else {
            $timeago = plural(_("!count day ago"), _("!count days ago"), $daysago);
        }
    } elseif ($timespan > $seconds_in_an_hour ) {
        $hoursago = floor($timespan / $seconds_in_an_hour);

        $timeago = plural(_("!count hour ago"), _("!count hours ago"), $hoursago);
    } elseif ($timespan > $seconds_in_a_minute) {
        $minutesago = floor($timespan / $seconds_in_a_minute);

        $timeago = plural(_("!count minute ago"), _("!count minutes ago"), $minutesago);
    } else {
        $timeago = t("moments ago");
    }

    return $timeago;
}