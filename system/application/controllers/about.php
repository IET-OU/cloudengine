<?php

/**
 * Controller for various static pages about the site in the 'about' section.
 * Admins can update URLs under 'about' using the admin interface - this controller is used
 * to then display those pages 
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Page
 */
class About extends MY_Controller {

	function About ()
	{
		parent::MY_Controller();	
		$this->load->library('layout', 'layout_main'); 
		$this->load->model('page_model');
	}
	
	/**
	 * Takes the page name specified in the URL and retrieves from the database the appropriate 
	 * page for the about section in the current language and then displays it.
	 *
	 * @param string $name The page name
	 */
	function _remap($name) {
	    $page = $this->page_model->get_page('about', $name, $this->lang->lang_code());
	    $data['title']      = $page->title;
	    $data['navigation'] = 'about'; 
	    $data['page']       = $page;
	    
        $this->layout->view('page/view', $data);
	}		
}
