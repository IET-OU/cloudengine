<?php 
/**
 * Override Exceptions.php - don't override the show_404 method.
 *
 * @copyright See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Exceptions
 */
class MY_Exceptions extends CI_Exceptions {

	/**
	 * Override show_error in the Exceptions.php library to deal with errors 
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
	    //'Success' - handle errors with branded page?
	    $success = true;

        if ('error_db'== $template) {
            $status_code = 503;
        }

        // API error handling.
        if ($template == 'error_general') {
			// Deal with API errors 
			if (function_exists('get_instance')) {
				$CI =& get_instance();
			} else {
			    @header("HTTP/1.1 $status_code", $status_code);
			    die($message);
			}
			if ('api'==$CI->uri->segment(1) && config_item('x_api')) {
				$CI->load->library('Api_error_lib');
				if (404 == $status_code) {
				    $CI->api_error_lib->process_error_404();
				} else {
				    $CI->api_error_lib->process_error_general();
				}
				exit;
			}
		}

		if ($template == 'error_db') {
			// Deal with API errors 
			$CI =& get_instance();
			if ('api' == $CI->uri->segment(1) && config_item('x_api')) {
				$CI->load->library('Api_error_lib');
				$CI->api_error_lib->process_error_db();
			}
		}

		// Otherwise carry on as before
		header("HTTP/1.1 $status_code", TRUE, $status_code);

        $message = '<p>'.implode('</p><p>', (! is_array($message)) ? array($message) : $message).'</p>';

        // Check for missing 'cloudengine.php' config. file.
        if (preg_match('#configuration file(.*)does not exist#', $message, $matches)) {
            // Not the most efficient way, but readable I hope!
            $message = str_replace($matches[1], ' <b>'.$matches[1].'</b> ', $message);

            $success = false;
        }

        // Fairly safe - load the 'branded' error view, for most error messages
        // including 404 'method not found', eg. http://cloudengine/cloud/_ERROR
        if ($success && function_exists('get_instance')) {
            $CI =& get_instance();
            $CI->load->library('layout', 'layout_main');

            $data['title']  = $heading;
            $data['message']= $message;
            echo $CI->layout->view("error/$template", $data, TRUE);

            echo " [a] ";
            $success = true;
        }
        // For 404 'controller not found' errors, use cURL to POST
        // and output the error page, eg. http://cloudengine/_ERROR
        elseif (404==$status_code && function_exists('curl_init')) {

            $ch = curl_init(config_item('base_url') ."error_page/$template/");
	        curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, 
		            'originalURL='.urlencode($_SERVER['REQUEST_URI']));
		    curl_exec($ch);
		    $success = (0 == curl_errno($ch));
		    curl_close($ch);

		    echo " [b] ";
        }

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();	
		}

        // If all else fails, safely output an unbranded error page.
        if (! $success) {
             ob_start();
             include(APPPATH.'errors/'.$template.EXT);
             $buffer = ob_get_contents();
             ob_end_clean();
             return $buffer;
		}
	}
}
