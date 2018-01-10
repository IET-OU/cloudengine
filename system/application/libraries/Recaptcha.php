<?php
/**
 * reCAPTCHA library.
 *
 * @copyright 2009, 2018 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @author  Nick Freear, 2018-01-09.
 *
 * @link    https://gist.github.com/nfreear/8f74ab520daf8e42b289b04af9aa4f87
 * @link    https://developers.google.com/recaptcha/
 */

class Recaptcha {

    const URL = 'https://www.google.com/recaptcha/api/siteverify';

    /** Server-side verification via the reCAPTCHA service.
     *
     * @param string $recap_response  The value of the 'g-recaptcha-response' POST field.
     * @return object  A stdClass object containing 'success', 'error-codes', etc.
     * @link  https://developers.google.com/recaptcha/docs/verify#api-response
     */
    public static function verify( $recap_response = null ) {

        $recap_response = $recap_response ? $recap_response : filter_input( INPUT_POST, 'g-recaptcha-response' );
        $recap_secret = config_item( 'recaptcha_secret_key' );

        $raw_response = file_get_contents( self::URL, false, self::httpPostContext([
             'secret' => $recap_secret,
             'response' => $recap_response,
             // 'remoteip' => ?
        ]) );

        $srv_response = $raw_response ? json_decode( $raw_response ) : $raw_response;

        if ( $srv_response && isset( $srv_response->success ) ) {
            log_message('debug', 'reCAPTCHA: ' . json_encode( $srv_response ));
            header( 'X-recaptcha-verify: ' . json_encode([ 'rc_resp' => $recap_response, 'srv_resp' => $srv_response ]));
        } else {
            log_message('error', 'reCAPTCHA: ' . json_encode([ $srv_response, $http_response_header ]));
            header( 'X-recaptcha-v-error: ' . json_encode([ $srv_response, $http_response_header ]));
        }

        return $srv_response;
    }

    protected static function httpPostContext( $postdata ) {
        $postdata = is_array( $postdata ) ? http_build_query( $postdata ) : $postdata;

        return stream_context_create([
            'http' => [
                'method' => 'POST',
                'user_agent' => 'CloudEngine/1.4 +https://github.com/IET-OU/cloudengine',
                'proxy' => config_item( 'http_proxy' ),
                'header' => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Content-Length: ' . strlen( $postdata ),
                ],
                'content' => $postdata,
                'timeout' => 5, // Seconds.
            ]
        ]);
    }
}
