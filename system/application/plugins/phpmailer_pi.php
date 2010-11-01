<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Use PHPMailer to send an email. Don't use directly, see helpers/mail_helper.php.
 *
 * @author The Open University
 * @author Thorpe Obazee
 * @see http://pinoysmartlife.com/phpmailer-plugin-for-codeigniter/112/ Pinoy Smart Life
 * @see http://codeigniter.com/wiki/PHPMailer/
 */
function phpmailer_email($recipient, $sender, $subject, $message)
{
    require_once("phpmailer/class.phpmailer.php");

    $mail = new PHPMailer();
    $body = $message;
    $mail->IsSMTP();
    //$mail->SetLanguage('es', APPPATH.'plugins/phpmailer/language/'); 
    $mail->CharSet  = config_item('charset');
    $mail->Host     = config_item('smtp_host');
    $mail->FromName = config_item('site_name');
    $mail->From = $sender;
    $mail->Subject = $subject;
    $mail->AltBody = strip_tags($message);
    $mail->MsgHTML($body);
    $mail->AddAddress($recipient);
    if ( ! $mail->Send())
    {
        return false;
    }
    else
    {
        return true;
    }
}
