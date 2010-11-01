<?php
/**
 * Controller for functionality releated to search
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU General Public License v2.
 * @package Search
 */
class Search extends Controller {

	function Search() {
		parent::Controller();
 
		$this->load->library('zend');
		$this->zend->load('Zend/Search/Lucene');
		$this->load->library('layout', 'layout_main');  
		$this->load->model('search_model');
		if (!config_item('x_search')) {
			show_404();
		}
	}

    /**
     * Display the search form
     */
	function index() {
        $data['title'] = t("Search");  
		$this->layout->view('search/search_form', $data);
	}

	/**
	 * Display the result of a search
	 *
	 */
	function result() {
	    // Increase the memory limit as Zend Lucene sometimes struggles 
	    ini_set('memory_limit','128M');
	    
        if ($query_string  = $this->input->post('query_string')) {
            $query_string        = $this->input->post('query_string');
            $data['results']     = $this->search_model->search($query_string);
            $data['clouds']      = $this->search_model->search_for_item_type($query_string, 
                                                                             'cloud') ;
            $data['cloudscapes'] = $this->search_model->search_for_item_type($query_string, 
                                                                            'cloudscape');
            $data['users']       = $this->search_model->search_for_item_type($query_string, 
                                                                             'user');
            $data['total_hits']  = count($data['results']);
        }
        
        $data['title']        = t("Search results for !query", 
                                  array('!query'=>$query_string));
        $data['query_string'] = $query_string;
        $data['navigation']   = 'search';
        $this->search_model->log_search($query_string);
		$this->layout->view('search/results', $data);		
	}

	/**
	 * Recreate the search index
	 *
	 */
	function create() {
	    $this->auth_lib->check_is_admin(); 
        // This takes a while, so make sure the php script doesn't timeout.
	    set_time_limit(60*60);  
        $index = $this->search_model->create_index();
		echo 'Index created';
	}
	
	/**
	 * Update the clouds in the search index
	 *
	 */
	function update_clouds() {
	    set_time_limit(60*60);  
	    $this->auth_lib->check_is_admin(); 
	    $this->search_model->update_indexed_clouds();
	    echo 'Updated clouds in index';
	}

	/**
	 * Update the cloudscapes in the search index
	 *
	 */
	function update_cloudscapes() {
	    set_time_limit(60*60);  
	    $this->auth_lib->check_is_admin(); 
	    $this->search_model->update_indexed_cloudscapes();
	    echo 'Updated cloudscapes in index';
	}	
	
	/**
	 * Update the users in the search index
	 */
	function update_users() {
	    set_time_limit(60*60);  
	    $this->auth_lib->check_is_admin(); 
	    $this->search_model->update_indexed_users();
	    echo 'Updated users in index';
	}		
	
	/**
	 * Delete an item from the search index
	 *
	 * @param integer $item_id The id of the itme
	 * @param string $item_type The type of item e.g. 'cloud', cloudscape
	 */
	function delete_item($item_id, $item_type) {
	    $this->auth_lib->check_is_admin(); 
	    $this->search_model->delete_item_from_index($item_id, $item_type);
	    echo "Deleted item with item_id $item_id item type $item_type";
	}	
	
}
