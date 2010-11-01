<?php

/**
 * Controller for site shortcuts. 
 * 
 * A database admin can set up shortcuts for particular pages in the shortcut table in the 
 * database, to allow for more friendly URLs. 
 * 
 * This controller redirect the shortcut URLs to the page specified for the shortcut. 
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU General Public License v2.
 * @package Shortcut 
 */
class Go extends Controller {

	function Go() {
		parent::Controller();
		$this->load->model('shortcut_model');
	}
	
	/**
	 * Remap /go/<shortcut> to the URL specified for the shortcut 
	 *
	 * @param string $shortcut
	 */
	function _remap($shortcut) {
	    $url = $this->shortcut_model->get_url($shortcut);
	    redirect($url);
	}
}