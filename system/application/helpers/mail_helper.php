<?php
/**
 * Mail helper - the send_mail function.
 *
 * Uses PHPMailer plugin, or built-in PHP 'mail' function, depending
 * on whether $config[smtp_host] is set.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU General Public License v2.
 */

// Note, legacy calls to send_mail will be preceded by ..CI->load->plugin(phpmailer)
// This is redundant, but not a problem.

if (!function_exists('send_mail')) {
    function send_email($recipient, $sender, $subject, $message) {
        $result = true;
        if (config_item('smtp_host')) {
            // Use PHPMailer.
            $CI =& get_instance();
            $CI->load->plugin('phpmailer');
            $result = phpmailer_email($recipient, $sender, $subject, $message);
            log_message('info', 
                        "MAIL: $result $recipient $sender $subject $message");
        }
        else {
            // Or use the default PHP mail function.

            // Note, all our emails are HTML.
            // Should we be sending a plain-text version too? Or strip tags?
            $headers = 'MIME-Version: 1.0' ."\r\n"
                .'Content-type: text/html; charset='.config_item('charset') ."\r\n"
                // Additional headers
                //."To: $recipient\r\n";
                ."From: $sender\r\n"
                .'X-Mailer: PHP/' .phpversion();

            $result = mail($recipient, $subject, $message, $headers);
        }
        return $result;
    }
}
