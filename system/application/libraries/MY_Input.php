<?php
/**
* MY_Input
*
* Allows CodeIgniter to be passed Query Strings
* NOTE! You must add the question mark '?' to the allowed URI chars,
* and set the URI protocol to PATH_INFO
*
* @package Flame
* @subpackage Hacks
* @copyright 2009, Jamie Rumbelow
* @author Jamie Rumbelow <http://www.jamierumbelow.net>
* @version 1.0.0
* 
* Distributed with the permission of the author. 
*/
class MY_Input extends CI_Input {

   function _sanitize_globals()
   {
    $this->allow_get_array = TRUE;
    parent::_sanitize_globals();
  }
}