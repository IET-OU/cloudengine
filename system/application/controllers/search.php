<?php
/**
 * Controller for functionality related to search.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Search
 */
class Search extends MY_Controller {

	function Search() {
		parent::MY_Controller();
 
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

      if ($query_string = $this->input->get('q')) {
  		  
        try {
        
          //this limits the display of results
          $data['output_limit'] = 200;
          //this limits how many results are shown on each page.
          //this figure also needs changing in search.js 'pageSize' if it is changed here
          $data['page_limit']   = 15;
      
          //clouds                                                
          $data['clouds']           = $this->search_model->search_for_item_type($query_string,'cloud');
          $data['cloud_hits']       = count($data['clouds']);
          //if there are less clouds than the output limit, adjust the limit to the number of results
          if ($data['cloud_hits'] < $data['output_limit']) {
            $data['cloud_output_limit'] = $data['cloud_hits'];
          //else use the limit
          } else {
            $data['cloud_output_limit'] = $data['output_limit'];
          }
          
          //cloudscapes
          $data['cloudscapes']      = $this->search_model->search_for_item_type($query_string,'cloudscape');
          $data['cloudscape_hits']  = count($data['cloudscapes']);
          //if there are less cloudscapes than the output limit, adjust the limit to the number of results
          if ($data['cloudscape_hits'] < $data['output_limit']) {
            $data['cloudscape_output_limit'] = $data['cloudscape_hits'];
          //else use the limit          
          } else {
            $data['cloudscape_output_limit'] = $data['output_limit'];
          } 
          
          //users
          $data['users']            = $this->search_model->search_for_item_type($query_string,'user');
          $data['user_hits']        = count($data['users']);
          //if there are less users than the output limit, adjust the limit to the number of results        
          if ($data['user_hits'] < $data['output_limit']) {
            $data['user_output_limit'] = $data['user_hits'];
          //else use the limit          
          } else {
            $data['user_output_limit'] = $data['output_limit'];
          }
          
          //total hits
          $data['total_hits']       = $data['cloud_hits'] + $data['cloudscape_hits'] + $data['user_hits'];
                                      
  		  }
  		  catch (Exception $e) {
  		    $data['error'] = $e->getMessage();
  		  }
        
      }

      $data['title']        = t("Search results for '!query'", array('!query'=>$query_string));
      $data['query_string'] = $query_string;
      $data['navigation']   = 'search';

      $this->search_model->log_search($query_string);
	    $this->layout->view('search/results', $data);		
	}

	/**
	 * Display the result of a search
	 *
	 */
	function all_results($type) {
	    
      // Increase the memory limit as Zend Lucene sometimes struggles 
	    ini_set('memory_limit','128M');
      
      $type_single = $type;
      $type_plural = $type .'s';

      if ($query_string = $this->input->get('q')) {
  		  try {
          //search                                                
          $data[$type_plural]  = $this->search_model->search_for_item_type($query_string,$type_single);
          $data['total_hits']  = count($data[$type_plural]);         
  		  }
  		  catch (Exception $e) {
  		    $data['error'] = $e->getMessage();
  		  }
      }

      $data['title']        = t(ucfirst($type_single) ." search results for '!query'", array('!query'=>$query_string));
      $data['query_string'] = $query_string;
      $data['navigation']   = 'search';
      $data['type_single']  = $type_single;
      $data['type_plural']  = $type_plural;    
      
      $this->search_model->log_search($query_string);
	    $this->layout->view('search/all_results', $data);		
	}

	/**
	 * Recreate the search index
	 *
	 */
	function create($index_limit) {
   
      $this->auth_lib->check_is_admin(); 
      // This takes a while, so make sure the php script doesn't timeout.
	    set_time_limit(60*60);  
      
      //set start time variable
      $time = microtime();
      $time = explode(' ', $time);
      $time = $time[1] + $time[0];
      $start = $time;
      //end start time variable

      $index = $this->search_model->create_index(false,$index_limit);
      
      //set end time variable      
      $time = microtime();
      $time = explode(' ', $time);
      $time = $time[1] + $time[0];
      $finish = $time;
      $total_time = round(($finish - $start), 4);
      $this->firephp->fb($total_time .' seconds','Time to index','INFO');
      //end end time variable    
     
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

	/** Output the OpenSearch Description XML.
	*/
	public function opensearch_desc() {
	    header('Content-Type: application/xml; charset=utf-8');
	    //@header('Content-Disposition: inline; '.$_SERVER['HTTP_HOST'].'-opensearch.xml');
		$this->load->view('search/opensearch_description');
	}
}
