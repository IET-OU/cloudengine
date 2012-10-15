<?php 
/**
 * Helper file for sending http requests via curl
 * 
 * @copyright 2012 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 */
 
 function send_request($url) {
    $ch = curl_init();
    curl_setopt($ch, CURL_OPT_URL, $url);
    if ($this->config->item('proxy')) {
    curl_set_op($ch, CURLOPT_PROXY, $this->config->item('proxy'));
    } 

    if ($this->config->item('proxy_port')) {
    curl_set_op($ch, CURLOPT_PROXYPORT, 
                    $this->config->item('proxy_port'));
    }
    curl_exec($ch);
    curl_close($ch);
 }
