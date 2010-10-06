<?php

/**
 * Controller for functionality related to favourites
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Favourites
 */

class Favourite extends Controller {

	function Favourite()
	{
		parent::Controller();
		$this->load->model('favourite_model');
		$this->load->library('layout', 'layout_main'); 	
	}

	/**
	 * Display the most popular clouds and cloudscapes on the site
	 *
	 */
	function popular() {
	    $data['title']       = t("Popular Clouds and Cloudscapes");
	    $data['clouds']      = $this->favourite_model->get_popular('cloud', 20);
	    $data['cloudscapes'] = $this->favourite_model->get_popular('cloudscape', 20);
	    $this->layout->view('favourite/popular', $data);
	}
}