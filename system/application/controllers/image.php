<?php
/**
 * Controller to serve images used by the site.
 *
 * Images for the site are not necessarily kept in the webroot, so this controller is used to
 * retrieve and server specified images as HTTP requests.
 *
 * @copyright 2009, 2010, 2017 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Image
 */

class Image extends MY_Controller {

  const IMAGE_REGEX = '/^.+\.(?P<ext>gif|jpe?g|JPE?G|png|PNG)$/';

	public function Image()
	{
        parent::MY_Controller();
	}

	/**
	 * Send a http response containing the image for a specified cloudscape
	 *
	 * @param integer $cloudscape_id The ID of the cloudscape
	 */
	public function cloudscape($cloudscape_id) {
	    $this->load->model('cloudscape_model');

	    $cloudscape = $this->cloudscape_model->get_cloudscape($cloudscape_id);
	    $path       = $this->config->item('upload_path_cloudscape').$cloudscape->image_path;

      $this->_print_image($image_name, $path);
	}

	/**
	 * Send a http response containing the 64x64 image for a specified user
	 *
	 * @param integer $user_id The ID of the user
	 */
	public function user($user_id) {
	    $this->load->model('user_model');

	    $image_name = $this->user_model->get_picture($user_id);
	    $path = $this->config->item('upload_path_user').$image_name;

      $this->_print_image($image_name, $path);
	}

	/**
	 * Send a http response containing the 32x32 image for a specified user
	 *
	 * @param integer $user_id The ID of the user
	 */
	public function user_32($user_id) {
	    $this->load->model('user_model');

	    $image_name = '32-'.$this->user_model->get_picture($user_id);
	    $path = $this->config->item('upload_path_user').$image_name;

      $this->_print_image($image_name, $path);
	}

	/**
	 * Send a http response containing the 16x16 image for a specified user
	 *
	 * @param integer $user_id The ID of the user
	 */
    public function user_16($user_id) {
	    $this->load->model('user_model');

	    $image_name = '16-'.$this->user_model->get_picture($user_id);
	    $path = $this->config->item('upload_path_user').$image_name;

      $this->_print_image($image_name, $path);
	}

	/**
	 * Send a http response containing a captch image
	 *
	 * @param string $image_name The name of the image with .jpg removed from the end
	 * (this seems to be stripped off by codeigniter so as we know it's a jpg, we'll
	 * add it back on ourselves)
	 */
	function captcha($image_name) {
		$path = config_item('FAL_captcha_image_path').str_replace('_', '.', $image_name);

	    if (is_readable($path)) {
            header('Content-Type: image/jpeg');
            echo(file_get_contents($path));
	    } else {
	        show_404();
	    }
	}

    public function badge($badge_id) {
        ini_set("display_errors", 'On');
        error_reporting(E_ALL);
        $this->load->model('badge_model');

        $image_name = $this->badge_model->get_image($badge_id);
        $path = $this->config->item('upload_path_badge').$image_name;

        $this->_print_image($image_name, $path);
    }

    /** Output an image file, with the correct HTTP header(s).
     *
     * @param string $image_name
     * @param string $path
		 * @return void
     */
    protected function _print_image($image_name, $path) {

        if (preg_match(self::IMAGE_REGEX, $image_name, $matches)
            && is_readable($path)) {

            $mimetype = str_replace('jpg', 'jpeg', strtolower($matches[ 'ext' ]));

            header('Content-Type: image/' . $mimetype);
            echo file_get_contents($path);

        } else {
            show_404();
        }
    }
}
