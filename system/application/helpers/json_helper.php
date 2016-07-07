<?php

// Based on Evan Baliatico's JSON Helper:
// http://www.codeigniter.com/wiki/JSON_Helper/

if(!defined('BASEPATH'))
        exit('No direct script access allowed');


function json_init() {
        // Load Services_JSON code
        if(!class_exists('Services_JSON'))
                require_once(BASEPATH.
                        'application/helpers/JSON.php');

        // Check/create/return JSON Service.
        if(!isset($GLOBALS['JSON_SERVICE_INSTANCE']))
                $GLOBALS['JSON_SERVICE_INSTANCE'] =
                        new Services_JSON();
        return $GLOBALS['JSON_SERVICE_INSTANCE'];
} # end json_init

if (! function_exists('json_encode')):

function json_encode($data = null) {
        if($data == null) return false;
        $json = json_init();
        return $json->encode($data);
} # end json_encode

function json_decode($data = null) {
        if($data == null) return false;
        $json = json_init();
        return $json->decode($data);
} # end json_decode

endif;
