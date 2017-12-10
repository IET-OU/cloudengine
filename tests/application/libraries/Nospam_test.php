<?php namespace IET_OU\CloudEngine\Tests\Libraries;

/**
 * Test the Anti-spam library.
 *
 * @copyright 2017 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @author  Nick Freear, 2017-12-08.
 */

use PHPUnit\Framework\TestCase;
use Nospam;

class Nospam_test extends TestCase {
// class Nospam_test extends PHPUnit_Framework_TestCase {
// class Driver_test extends CI_TestCase {

    public function setUp()
    {
        $this->nospam = new Nospam();
    }

    public function test_load_file()
    {
        $this->assertFileExists( Nospam::EMAIL_NOSPAM_FILE );
        $this->assertFileIsReadable( Nospam::EMAIL_NOSPAM_FILE );

        $server_list = $this->nospam->load_nospam_file(); // 48679 bytes ??

        $this->assertEquals( 49645, filesize( Nospam::EMAIL_NOSPAM_FILE ));
        $this->assertEquals( 3768, substr_count( $server_list, "\n" ));
    }

    public function test_file_contains()
    {
        $server_list = $this->nospam->load_nospam_file();

        $this->assertContains( ".com\n", $server_list );
        $this->assertContains( ".ru\n", $server_list );
    }

    public function test_is_disposable()
    {
        $this->assertTrue( $this->nospam->check_email( 'bad.hat@yopmail.com' )->fail );
        $this->assertFalse( $this->nospam->check_email( 'bad.hat@getnada.com' )->ok );
        $this->assertFalse( $this->nospam->check_email( 'bad.hat@spambog.ru' )->ok );
        $this->assertFalse( $this->nospam->check_email( 'dodu@p33.org' )->ok );
    }

    public function test_not_disposable()
    {
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@gmail.com' )->email_ok );
        $this->assertTrue( $this->nospam->check_email( 'jane.bloggs@yahoo.co.uk' )->ok );
        $this->assertTrue( $this->nospam->check_email( 'jane.bloggs@outlook.com' )->ok );
    }

    public function test_academic_openuniv()
    {
        $this->assertTrue( $this->nospam->check_email( 'jane.bloggs@open.ac.uk' )->ok );
    }

    public function test_academic_world()
    {
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@u-tokyo.ac.jp' )->ok );
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@ouchn.edu.cn' )->ok );
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@open.edu.au' )->ok );
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@out.ac.tz' )->ok );
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@mit.edu' )->ok );
        $this->assertTrue( $this->nospam->check_email( 'joe.bloggs@ou.nl' )->ok );
    }
}
