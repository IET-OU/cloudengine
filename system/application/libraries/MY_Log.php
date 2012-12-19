<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Log extends CI_Log {

    public function __construct() 
    {
        parent::__construct();

        // Change log levels so INFO higher priority than DEBUG
        $this->_levels    = array('ERROR' => '1', 'INFO' => '2',  
                                  'DEBUG' => '3', 'ALL' => '4');
    }
}