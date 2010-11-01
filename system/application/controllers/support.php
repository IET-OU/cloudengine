<?php
/**
 * Controller for various static pages about the site in the 'support' section.
 * Admins can update URLs under 'support' using the admin interface - this controller is used
 * to then display those pages 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Page
 */

/**
 * Controller for static about pages
 */
class Support extends Controller {

	function Support ()
	{
		parent::Controller();	
		$this->load->library('layout', 'layout_main'); 
		$this->load->model('page_model');
	}
	
	/**
	 * Remap /section/<name> to the appropriate page
	 *
	 * @param string $shortcut
	 */
	function _remap($name) {
	    $page = $this->page_model->get_page('support', $name, $this->lang->lang_code());
	    $data['title']      = $page->title;
	    $data['navigation'] = 'support'; 
	    $data['page']       = $page;
	    
        $this->layout->view('page/view', $data);
	}	
}