<?php namespace IET_OU\CloudEngine\Tests\Helpers;

/**
 * Test the Nofollow helper class.
 *
 * @copyright 2017 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @author  Nick Freear, 2017-12-08.
 */

use PHPUnit\Framework\TestCase;
use Nofollow;

class Nofollow_helper_test extends TestCase {

    const HTML_1 = 'Hi. <a href="http://example.org">Hello world</a>! <a href=\'mailto:joe@example.org\'>Email</a>';

    public function test_nofollow() {
        $this->assertContains( ' href="http://', Nofollow::f( self::HTML_1 ));
        $this->assertContains( ' rel="nofollow"', Nofollow::f( self::HTML_1 ));

        $this->assertEquals( 4, Nofollow::get_count() );
    }
}
