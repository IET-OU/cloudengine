<?php
/**
 * Library to process API errors
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package API
 */
class Api_error_lib {
	
    function Api_error_lib() {
        $this->CI =& get_instance();
    }
    
    /**
     * Process a 404 Error for an API call
     */
    function process_error_404() {
	    switch ($this->CI->uri->segment(2)) {
	        case 'cloud': # Drop-through
	        case 'cloudscape': # Drop-through
	        case 'user': 
	          $item_type = $this->CI->uri->segment(2);
	          $message .= " Did you mean '{$item_type}s', with an 's'?";
	    }
	    
	    $data = array(
	        'stat' => 'fail',
	        'code'   => 404,
	        'message'=> strip_tags($message),
	        'request'=> $this->CI->uri->uri_string().'?'.$_SERVER['QUERY_STRING'],
	    );
	    
	    $this->CI->load->view('api/api_render', array('response' => $data));
	    exit;
    }
    
    /**
     * Process a database error for an API call
     *
     */
    function process_error_db() {
	    $data = array(
	        'stat' => 'fail',
	        'code'   => 503,
	        'message'=> strip_tags($message),
	        'request'=> $this->CI->uri->uri_string().'?'.$_SERVER['QUERY_STRING'],
	    );
	    $this->CI->load->view('api/api_render', array('response'=>$data));
	    exit;
    }
    
    /**
     * Process a general error for an API call
     */
    function process_error_general() {
	    $data = array(
	        'stat' => 'fail',
	        'code'   => 500,
	        'message'=> $message,
	        'request'=> $this->CI->uri->uri_string().'?'.$_SERVER['QUERY_STRING'],
	    );
    	$format =  $this->CI->uri->file_extension() ? $CI->uri->file_extension() : 'json';
	    switch ($format) {
		    case 'xml':
		        @header('Content-Type: application/xml; charset=utf-8');
		        echo '<?xml version="1.0" encoding="utf-8"?>';
		        echo '<rsp>';
		        foreach ($data as $key=>$value) {
	            echo '<<'.$key.'>'.htmlentities($value).'</'.$key.'>';
				}
				echo '</rsp>';
	        	break;
    		case 'json':
    		default:
	        echo json_encode($data);
	        break;
	    }

    	exit;
    }
    
    /**
     * Process a PHP error for an API call
     */
    function process_error_php() {
	    $data = array(
	        'stat'   => 'fail',
	        'code'    => 500,
	        'severity'=> $severity,
	        'message' => $message,
	        'filename'=> $filepath,
	        'line'    => $line,
	        'request' => $this->CI->uri->uri_string().'?'.$_SERVER['QUERY_STRING'],
	    );
	    $format = $this->CI->uri->file_extension() ? $this->CI->uri->file_extension() : 'json';
	    switch ($format) {
	    case 'xml':
	        @header('Content-Type: application/xml; charset=utf-8');
	        echo '<?xml version="1.0" encoding="utf-8"?>';
			echo '<rsp>';
			foreach ($data as $key=>$value) {
	    		echo '<'.$key.'>'.htmlentities($value).'</'.$key.'>';
			}
			echo '</rsp>';
	        exit;
	        break;
	    case 'json':
	    default:
	        echo json_encode($data);
	        break;
	    }
    }
}