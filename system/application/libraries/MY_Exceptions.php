<?php 
/**
 * Override Exceptions.php
 * 
 * @copyright See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Exceptions
 */
class MY_Exceptions extends CI_Exceptions {

   /**
    * Overrride show_404 in the Exceptions.php library to deal with 404 pages differently 
    *
    * @param string $page 
    */
   function show_404($page = '') {
   	
		if (function_exists('get_instance')) {
			$CI =& get_instance();
			if ('api' == $CI->uri->segment(1) && config_item('x_api')) {
				$CI->load->library('Api_error_lib');
				$CI->api_error_lib->process_error_404();
			}
		}
		// The following code is written by Kelvin Luck http://www.kelvinluck.com/	taken from 
		// http://www.kelvinluck.com/2009/04/custom-404-error-messages-with-codeigniter/
		// and used and distributed with permission. 
		$code = '404';
		$text = 'Page not found';
		
		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
		
		if (substr(php_sapi_name(), 0, 3) == 'cgi') {
		 	header("Status: {$code} {$text}", TRUE);
		} elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0') {
		 	header($server_protocol." {$code} {$text}", TRUE, $code);
		} else {
			 header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 
		           'http://'.$_SERVER['HTTP_HOST'] .'/error_page/error_404/');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
		            'originalURL='.urlencode($_SERVER['REQUEST_URI']));
		curl_exec($ch); 
		curl_close($ch);
	}
	
	/**
	 * Override show_error in the Exceptions.php library to deal with error_general 
	 * differently. 
	 *
	 * @param string $heading The error heading
	 * @param array $message The error message
	 * @param string $template The error template to use e.g. 'error_general', 'error_db'
	 * @param integer $status_code The HTTP error status code
	 * @return string The page to display (unless redirects to an error page as part of the 
	 * function processing)
	 */
	function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
				
		if ($template == 'error_general') {
			// Deal with API errors 
			$CI =& get_instance();
			if ('api'==$CI->uri->segment(1) && config_item('x_api')) {
				$CI->load->library('Api_error_lib');
				$CI->api_error_lib->process_error_general();
			}
			
			// For error_general and non-API errors, display the error page using 
			// Kelvin Luck's code as above, tweaked to send the message through
			// so we can display it
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 
			           'http://'.$_SERVER['HTTP_HOST'] .'/error_page/error_general/');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, 
			            'message='.urlencode($message));
			curl_exec($ch); 
			curl_close($ch);
			exit();
			
		}
		
		if ($template == 'error_db') {
			// Deal with API errors 
			$CI =& get_instance();
			if ('api' == $CI->uri->segment(1) && config_item('x_api')) {
				$CI->load->library('Api_error_lib');
				$CI->api_error_lib->process_error_db();
			}
			
			// For error_general and non-API errors, display the error page using 
			// Kelvin Luck's code as above
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 
			           'http://'.$_SERVER['HTTP_HOST'] .'/error_page/error_db/');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_exec($ch); 
			curl_close($ch);
			exit();
		}
		
		// Otherwise carry on as before 
		set_status_header($status_code);
		
		$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();	
		}
		ob_start();
		include(APPPATH.'errors/'.$template.EXT);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}