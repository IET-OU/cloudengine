<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper to add anti-spam 'rel="nofollow"' to links.
 *
 * @copyright 2009, 2017 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 *
 * @link  http://www.barattalo.it/coding/how-to-add-rel-nofollow-to-links-with-preg_replace/
 */

class Nofollow {

  protected static $total = 0;

  /** Filter input adding 'rel="nofollow"' to all links.
   *
   * @param string HTML
   * @return string HTML
   */
  public static function f($html, $skip = null) {
    return preg_replace_callback(
        "/(<a[^>]+?)>/is", function ($matches) use ($skip) {
            return (
                !($skip && strpos($matches[ 1 ], $skip) !== false)
                // && strpos($matches[1], 'rel=') === false
            ) ? $matches[ 1 ] . self::attr() . '>' : $matches[ 0 ];
        },
        $html
    );
  }

  public static function attr() {
    self::$total += 1;

    return ' rel="nofollow"';
  }

  public static function get_count() {
    return self::$total;
  }
}
