<?php
/**
 * Controller for serving images used by the site
 * 
 * Images for the site are not necessarily kept in the webroot, so this controller is used to 
 * retrieve and server specified images as HTTP requests
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Image
 */

class Image extends Controller {

	function Image()
	{
        parent::Controller();  
	}

	/**
	 * Send a http response containing the image for a specified cloudscape 
	 *
	 * @param integer $cloudscape_id The ID of the cloudscape
	 */
	function cloudscape($cloudscape_id) {
	    $this->load->model('cloudscape_model');

	    $cloudscape = $this->cloudscape_model->get_cloudscape($cloudscape_id);
	    $path       = $this->config->item('upload_path_cloudscape').$cloudscape->image_path;

	    if (preg_match('/^.+\.(gif|jpe?g|JP?G|png|PNG)$/', $cloudscape->image_path, $matches) 
	        && is_readable($path)) {
            header('Content-Type: image/'.$matches[1]);
            echo(file_get_contents($path));
	    } else {
	        show_404();
	    }
	}

	/**
	 * Send a http response containing the 64x64 image for a specified user
	 *
	 * @param integer $user_id The ID of the user
	 */
	function user($user_id) {
	    $this->load->model('user_model');
	    
	    $image_name = $this->user_model->get_picture($user_id);
	    $path = $this->config->item('upload_path_user').$image_name;
	    if (preg_match('/^.+\.(gif|jpe?g|JP?G|png|PNG)$/', $image_name, $matches) 
	        && is_readable($path)) {
            header('Content-Type: image/'.$matches[1]);
            echo(file_get_contents($path));
	    } else {
	        show_404();
	    }
	}

	/**
	 * Send a http response containing the 32x32 image for a specified user
	 *
	 * @param integer $user_id The ID of the user
	 */
	function user_32($user_id) {
	    $this->load->model('user_model');
	    
	    $image_name = '32-'.$this->user_model->get_picture($user_id);
	    $path = $this->config->item('upload_path_user').$image_name;
	    if (preg_match('/^.+\.(gif|jpe?g|JP?G|png|PNG)$/', $image_name, $matches) 
	        && is_readable($path)) {
            header('Content-Type: image/'.$matches[1]);
            echo(file_get_contents($path));
	    } else {
	        show_404();
	    }
	}
	
	/**
	 * Send a http response containing the 16x16 image for a specified user
	 *
	 * @param integer $user_id The ID of the user
	 */	
    function user_16($user_id) {
	    $this->load->model('user_model');
	    
	    $image_name = '16-'.$this->user_model->get_picture($user_id);
	    $path = $this->config->item('upload_path_user').$image_name;
	    if (preg_match('/^.+\.(gif|jpe?g|JP?G|png|PNG)$/', $image_name, $matches) 
	        && is_readable($path)) {
            header('Content-Type: image/'.$matches[1]);
            echo(file_get_contents($path));
	    } else {
	        show_404();
	    }
	}
	
	/**
	 * Send a http response containing a captch image
	 *
	 * @param string $image_name The name of the image with .jpg removed from the end
	 * (this seems to be stripped off by codeigniter so as we know it's a jpg, we'll
	 * add it back on ourselves)
	 */
	function captcha($image_name) {
		$path = config_item('FAL_captcha_image_path').$image_name.'.jpg';
	    if (is_readable($path)) {
            header('Content-Type: image/jpeg');
            echo(file_get_contents($path));
	    } else {
	        show_404();
	    }
	}
}