<?php
/**
 * reCAPTCHA library.
 *
 * @copyright 2009, 2018 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @author  Nick Freear, 2018-01-09.
 *
 * @link    https://gist.github.com/nfreear/8f74ab520daf8e42b289b04af9aa4f87
 * @link    https://developers.google.com/recaptcha/docs/verify
 */

class Recaptcha {

    const URL = 'https://www.google.com/recaptcha/api/siteverify';

    public static function verify( $recap_response = null ) {

        $recap_response = $recap_response ? $recap_response : filter_input( INPUT_POST, 'g-recaptcha-response' );
        $recap_secret = config_item( 'recaptcha_secret_key' );

        $server_response = json_decode(file_get_contents( self::URL, false, self::httpPostContext([
            'secret' => $recap_secret,
            'response' => $recap_response,
            // 'remoteip' => ?
        ]) ));
        // $server_response->ok = $server_response->success;

        header( 'X-recaptcha-verify: ' . json_encode([ 'rc_resp' => $recap_response, 'srv_resp' => $server_response ]));

        return $server_response;
    }

    protected static function httpPostContext( $postdata ) {
        $postdata = is_array( $postdata ) ? http_build_query( $postdata ) : $postdata;

        return stream_context_create([
            'http' => [
                'method' => 'POST',
                'user_agent' => 'CloudEngine/1.0-beta +https://github.com/nfreear',
                'proxy' => config_item( 'proxy' ), // . config_item( 'proxy_port' ),
                'header' => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: ' . strlen( $postdata ),
                ],
                'content' => $postdata,
            ]
        ]);
    }
}
