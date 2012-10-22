<?php 
/**
 * Helper file for sending http requests via curl
 * 
 * @copyright 2012 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 */
 
 function send_request($url) {
    $CI =& get_instance();
    $ch = curl_init();
    curl_setopt($ch, CURL_OPT_URL, $url);
    if ($CI->config->item('proxy')) {
    curl_setopt($ch, CURLOPT_PROXY, $CI->config->item('proxy'));
    } 

    if ($CI->config->item('proxy_port')) {
    curl_setopt($ch, CURLOPT_PROXYPORT, 
                    $CI->config->item('proxy_port'));
    }
    curl_exec($ch);
    curl_close($ch);
 }
