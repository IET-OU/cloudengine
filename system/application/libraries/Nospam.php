<?php
/**
 * Anti-spam library. Check if an email address is disposable, etc.
 *
 * @copyright 2017 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @author  Nick Freear, 2017-12-06, 2017-12-08.
 * @link  https://codeigniter.com/userguide2/libraries/form_validation.html#validationrules
 * @link  https://gist.github.com/adamloving/4401361#-temporary-email-address-domains
 */

class Nospam {

    const EMAIL_NOSPAM_FILE = __DIR__ . '/../../../vendor/wesbos/burner-email-providers/emails.txt';
    // const EMAIL_NOSPAM_FILE = __DIR__ . '/../config/email-nospam.php';

    /**
     * Custom rule. Check if an email address is disposable.
     *
     * @param string $email  Email address.
     * @return bool          Is the email address acceptable?
     */
    public function check_email( $email ) {

        $email_server_list = $this->load_nospam_file();

        $lines = substr_count( $email_server_list, "\n" );

        $email_parts = explode( '@', $email );
        $email_host = strtolower($email_parts[ 1 ]);

        $email_pattern = "\n" . $email_host . "\n";

        // If there is no match, the email server is NOT in the list of burner providers.
        $pos = strpos( $email_server_list, $email_pattern );
        $email_ok = ( false === $pos );

        return (object) [
            'lines'=> $lines,
            'host' => $email_host,
            'pos'  => $pos,
            'email_ok' => $email_ok,
            'ok'   => $email_ok,
            'fail' => ! $email_ok,
            'file' => self::EMAIL_NOSPAM_FILE,
        ];
    }

    public function load_nospam_file() {
        return file_get_contents( self::EMAIL_NOSPAM_FILE );
    }
}
