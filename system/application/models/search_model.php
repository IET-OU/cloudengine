<?php 
/**
 *  Model file for functions related to search
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Search
 */
class Search_model extends Model {

    function Search_model() {
        parent::Model();
        $this->CI=& get_instance();
        $this->CI->load->library('firephp');            
        $this->CI->load->library('zend');
		    $this->CI->zend->load('Zend/Search/Lucene');
		    Zend_Search_Lucene_Analysis_Analyzer::setDefault(new 
		                                       StandardAnalyzer_Analyzer_Standard_English() );
		    $this->search_index_path = $this->config->item('search_index_path');
    }

    /**
     * Create the search index 
     * 
     * Make sure that the PHP script timeout is large enough to allow this function to 
     * complete
     *
     * @return The search index 
     */
    function create_index($return_error = false, $index_limit = false ) {

      $this->CI=& get_instance();  
      
      // Change the analyser to a better one so stems words, indexes 
	    // numbers etc. 
	    try {
        	Zend_Search_Lucene_Analysis_Analyzer::setDefault(new 
        	                                   StandardAnalyzer_Analyzer_Standard_English() );
			$index = Zend_Search_Lucene::create($this->search_index_path);
        } catch (Zend_Search_Lucene_Exception $e){
            if ($return_error) {
                return $e->getMessage();
            }
            echo $e->getMessage();
        }

      //create users index
      $this->update_indexed_users($index,$index_limit);
      //create clouds index
		  $this->update_indexed_clouds($index,$index_limit);
      //create cloudscapes index
      $this->update_indexed_cloudscapes($index,$index_limit);     

      $index->commit();
      $index->optimize();
      
    }

    /**
     * Open the search index for the site 
     *
     * @return object The search index 
     */
    function open_index() {
        /*try {
            $this->firephp->fb($this->search_index_path,'path','INFO');
            //$index = Zend_Search_Lucene::open($this->search_index_path );
            $index = Zend_Search_Lucene::open('\\appleton\rjl354\websites\cloudworks_data\search\index' );
            $this->firephp->fb($index,'$index2','INFO');
        } catch (Zend_Search_Lucene_Exception $e){
             @header("HTTP/1.1 500 Internal Server Error", TRUE, 500);
             $data['message'] = '<div class="error">'.t("Error: search is not working.").
                                '<br />'.$e->getMessage().'</div>';
              log_message('error', "Error: search is not working. | ".$e->getMessage().
                          " This may because this is a new install and the search index has 
                             not been created.");
              show_error($data['message']);
              exit();
        }*/
        $index = Zend_Search_Lucene::open($this->search_index_path);
        return $index;
    }


    /**
     * Update the index for all clouds
     *
     * @param object $index
     */
    function update_indexed_clouds($index = false, $index_limit = false) {
        if (!$index) {
            $index = $this->open_index();
        }        
		    $this->CI->load->model('cloud_model');
        $clouds = $this->CI->cloud_model->get_clouds();
        $clouds_indexed = 0;
        //$this->firephp->fb($clouds,'$clouds','INFO');
        
        //allow a limit to indexing to be passed for testing purposes
        if ($index_limit) {
          for($i=0; $i<$index_limit; $i++) {
            $cloud_id = $clouds[$i]->cloud_id;
            //$this->firephp->fb($cloud_id,'$cloud_id','INFO');   
      	    $this->update_item_in_index(base_url().'cloud/view/'.$cloud_id, 
      	                                           $cloud_id, 'cloud', $index);  
          }         
        } 
        else {
          foreach($clouds as $cloud) {
              $clouds_indexed++;
              $cloud_id = $cloud->cloud_id; 
      	    $this->update_item_in_index(base_url().'cloud/view/'.$cloud_id, 
      	                                           $cloud_id, 'cloud', $index); 
          }
        }
        $index->commit();
        $index->optimize();
    }
    
    /**
     * Update the index for all clouds
     *
     * @param object $index
     */    
    function update_indexed_cloudscapes($index = false, $index_limit = false) {
                
        if (!$index) {
            $index = $this->open_index();
        }      

		    $this->CI->load->model('cloudscape_model');
        $cloudscapes = $this->CI->cloudscape_model->get_cloudscapes();

        //allow a limit to indexing to be passed for testing purposes
        if ($index_limit) {
          for($i=0; $i<$index_limit; $i++) {
            $cloudscape_id = $cloudscapes[$i]->cloudscape_id;
            //$this->firephp->fb($cloudscape_id,'$cloudscape_id','INFO');   
      	    $this->search_model->update_item_in_index(base_url().'cloudscape/view/'.
      	                                              $cloudscape_id, 
      	                                              $cloudscape_id, 'cloudscape',  $index);   
          }        
        }
        else {
          foreach($cloudscapes as $cloudscape) {
              $cloudscape_id = $cloudscape->cloudscape_id;
      	    $this->search_model->update_item_in_index(base_url().'cloudscape/view/'.
      	                                              $cloudscape_id, 
      	                                              $cloudscape_id, 'cloudscape',  $index);     	    
          } 
        }
        
        $index->commit();
        $index->optimize();
    }
    
    /**
     * Update the index for all clouds
     *
     * @param object $index
     */    
    function update_indexed_users($index = false, $index_limit = false) {
      
        //$this->firephp->fb($index,'$index','INFO');    

        if (!$index) {
            $index = $this->open_index();
        }
		    $this->CI->load->model('user_model');
        $users = $this->CI->user_model->get_users();

        //allow a limit to indexing to be passed for testing purposes
        if ($index_limit) {
          for($i=0; $i<$index_limit; $i++) {
            $user_id = $users[$i]->id;
            //$this->firephp->fb($user_id,'$user_id','INFO');   
            $this->search_model->update_item_in_index(base_url().'user/view/'.$user_id, 
      	                                           $user_id, 'user',  $index);     	   
          } 
        }
        else {
          foreach($users as $user) {
            $user_id = $user->id;
      	    $this->search_model->update_item_in_index(base_url().'user/view/'.$user_id , 
      	                                           $user_id, 'user',  $index);     	   
          }
        }
        
        $index->commit();
        $index->optimize();
    }

    /**
     * Update an item in the index
     *
     * @param string $url URL of the item
     * @param integer $item_type The item type of the item e.g. cloud, cloudscape
     * @param string $item_id The id of the item 
     * @param object $index The search index
     */
    function update_item_in_index($url, $item_id, $item_type, $index = false) {
        // Delete the item and re-add it        
        $this->delete_item_from_index($item_id, $item_type, $index);
        $this->add_item_to_index($url, $item_id, $item_type, $index);
    }

    /**
     * Add an item to a search index 
     *
     * @param string $url URL of the page to index
     * @param string $item_type The item type of the item e.g. cloud, cloudscape
     * @param integer $item_id The id of the item 
     * @param object $index The search index
     */
    function add_item_to_index($url, $item_id, $item_type, $index = false) {
        if (!$index) {
            $index = $this->open_index();
        }
        try {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(new 
                                               StandardAnalyzer_Analyzer_Standard_English() );
          //$this->firephp->fb($url,'$url','INFO');
          $url_to_index = str_replace('/view','/search_view',$url);
          //$this->firephp->fb($url_to_index,'$url_to_index','INFO');
        	$doc = Zend_Search_Lucene_Document_HTML::loadHTMLFile($url_to_index);

        	$doc->addField(Zend_Search_Lucene_Field::Keyword('url',  $url));
        	$doc->addField(Zend_Search_Lucene_Field::Keyword($item_type.'_id', $item_id ));  
          $doc->addField(Zend_Search_Lucene_Field::Keyword('item_type', $item_type));
          //$this->firephp->fb($doc,'$doc','INFO');
          $index->addDocument($doc);
        } catch (Zend_Search_Lucene_Exception $e){
              log_message('error', "Error: Adding $item_type $item_id. | ".$e->getMessage()); 
        }
    }
    
    /**
     * Remove a page from the index 
     *
     * @param string $url The URL fo the page to remove
     * @param object $index The search index
     */
    function delete_item_from_index($item_id, $item_type, $index = false) {
      
        if (!$index) {
            $index = $this->open_index();
        }        
        $ids = $this->get_items($item_id, $item_type,$index);
        foreach ($ids as $id) { 
            if ( $id !== FALSE) {
                $index->delete($id);
            }
        }
    }
    
    /**
     * Get the index id of a specified item
     *
     * @param integer $item_type The item type of the item e.g. cloud, cloudscape
     * @param string $item_id The id of the item 
     * @return integer The id of the item
     */
    function get_items($item_id, $item_type, $index = false) {
                
        if (!$index) {
            $index = $this->open_index();
        }

        //$this->firephp->fb($item_id,'$item_id','INFO');
        //$this->firephp->fb($item_type,'$item_type','INFO');
        //$this->firephp->fb($index,'$index','INFO');

        $term = new Zend_Search_Lucene_Index_Term($item_id, $item_type.'_id');        
        $ids = $index->termDocs($term);   
        
        return $ids;
    }
    

    /**
     * Query the search index for a specified search string
     *
     * @param string $search_string The search string to search the index for
     * @return array  Array of search results returned
     */
    function search($search_string) {
      
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new 
                                                StandardAnalyzer_Analyzer_Standard_English());
        $index   = $this->open_index();
        $this->firephp->fb($index,'$index','INFO');      
        $results = $index->find($search_string);
        $this->firephp->fb($results,'$results','INFO');
        return $results;
    }

    /**
     * Query the search index for a specified search string, returning only items of a 
     * specified item type
     *
     * @param string $search_string The search string to search the index for
     * @param string $item_type The item type to filter by e.g. cloud, cloudscape 
     * @return array  Array of search results returned
     */
    function search_for_item_type($search_string, $item_type) {

        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new 
                                                StandardAnalyzer_Analyzer_Standard_English());

        $index   = $this->open_index();
        //$this->firephp->fb($item_type,'$item_type','INFO');
        //exit;
        $results = $index->find($search_string);

        // Go through the results and only include those of the specified item type
        $filtered_results = array();
        foreach ($results as $result) {
            if ($result->item_type == $item_type) {
                $filtered_results[] = $result;
            }
        }

        return $filtered_results;
    }
     
    /**
     * Log the search
     *
     * @param string $search_term The search term used in the search
     */
    function log_search($search_term) {
        $this->db->set('item_id', '0');
        $this->db->set('search_term', $search_term);   
        $this->db->set('item_type', 'search');
        $this->db->set('timestamp', time());
        $user_id = $this->db_session->userdata('id');
        $this->db->set('user_id', $user_id);
        $this->db->set('ip', $this->input->ip_address()); 
        $this->db->insert('logs');       
    }    
}

function open_index() {
      $index   = $this->open_index();
}
