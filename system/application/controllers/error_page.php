<?php 

/**
 * Controller for displaying various error pages for the site - it is not intended that
 * a user accesses these pages directly, however we need to use this as the default 
 * CodeIgniter error pages cannot access the information required to display them 
 * @see system/application/libraries/MY_Exceptions.php
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Error
 */
class Error_page extends Controller {

    function Error_page() {
        parent::Controller();
        $this->load->library('layout', 'layout_main'); 
    }
    
    /**
     * Display a 404 Error page
     *
     */
    function error_404() {
    	$data['title'] = t("Page not found");
    	$this->layout->view('error/error_404', $data);
    }
    
    /**
     * Display a database error page
     *
     */
    function error_db() {
    	$data['title'] = t("Error");
    	$this->layout->view('error/error_db', $data);
    }
    
    /**
     * Display a general error page
     *
     */
    function error_general() {
    	$data['title'] = t("Error");
    	$data['message'] = urldecode($this->input->post('message'));
    	$this->layout->view('error/error_general', $data);
    }
}