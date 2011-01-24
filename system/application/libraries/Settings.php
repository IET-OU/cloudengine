<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library for autoloading settings from the database in to config
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Format
 */

class Settings {

	private $CI;
	
	private $_items = array();
	
	function __construct() {
		$this->CI =& get_instance();
		$this->CI->load->model('settings_model');
		$_items = $this->CI->settings_model->get_all();
    //add each item in to the config settings   
		foreach($_items as &$item)
		{
      $this->CI->config->set_item($item->name , $item->value);      
		}
		unset($_items);
	}
	
	function item($name = '', $use_cache = TRUE)
	{
		// Check the database for this setting
		if ($setting = $this->CI->settings_model->get($name))
		{
      $value = $setting->value;
    } 	
    // Not in the database? Try a config value instead
    else
    {
      $value = $this->CI->config->item($name);
    }
    // Save the value in the items array and return it
    return $value;
  }
    
  function set_item($name, $value)
  {
  	return $this->CI->settings_model->update($name, array('value'=>$value));
  }
    
}

?>