<?php 
/**
 * Model file for cloud-related functions
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Cloud
 */
class Cloud_model extends Model {

    function Cloud_model() {
        parent::Model();
    }
    
    /**
     * Get all clouds on the site that have not been flagged for moderation
     *
     * @return Array of clouds
     */
    function get_clouds() {
         $query = $this->db->query("SELECT * FROM cloud WHERE moderate = 0");	
        return $query->result();
    }     

    /**
     * Get all clouds starting with a specified letter of the alphabet
     *
     * @param string $alpha The letter of the alphabet
     * @return Array of clouds
     */
    function get_clouds_alpha($alpha = 'A') {
       if (strlen($alpha) != 1) {
           $alpha = 'A';
       }
         $query = $this->db->query("SELECT cl.title as title, cl.summary as summary, 
                                   cl.cloud_id as cloud_id, 
                                   COUNT(co.comment_id) AS total_comments 
						           FROM cloud cl
						           LEFT OUTER JOIN comment co ON cl.cloud_id = co.cloud_id
                       INNER JOIN user u on u.id = cl.user_id                       
						           WHERE ltrim(cl.title) LIKE '$alpha%' 
                       AND cl.moderate = 0 
						           AND u.banned = 0
                       GROUP BY cl.cloud_id 
						           ORDER BY title ASC");	
                       
        return $query->result();
    }
    
    /**
     * Get the active clouds on the site, based on clouds with most comments
     * in time period specified in config 
     *
     * @param integer $limit Limit to the number of clouds to get
     * @return Array of clouds, ordered by number of comments in the time period
     */
    function get_active_clouds($limit = 10) {
        $days = $this->config->item('active_clouds_days') ? 
                    $this->config->item('active_clouds_days') : 10;
        $since = time() - 60*60*24* $days;
        $query = $this->db->query("SELECT c.cloud_id, c.title, COUNT(*) AS total_comments 
                  FROM cloud c INNER JOIN comment co ON co.cloud_id = c.cloud_id 
                  WHERE co.timestamp > $since
                    GROUP BY c.cloud_id ORDER BY total_comments  DESC LIMIT $limit");   
        if (!$query) {
          return FALSE;
        }
        return $query->result();
    }
    
    /**
     * Get the total number of times a cloud has been viewed
     * - multiple views by the same logged in user count as one view
     * - mulitple views by a guest at the same IP address count as one view
     * @param integer $cloud_id The ID of the cloud
     * @return integer The number of views
     */
    function get_total_views($cloud_id) {
        $total_views = 0;
        if (is_numeric($cloud_id)) {
            $query = $this->db->query("SELECT * FROM logs WHERE item_type='cloud' ".
            "AND item_id = $cloud_id AND user_id = 0 AND ip <> '".$_SERVER['SERVER_ADDR'].
            "' GROUP BY ip");
            $total_views += $query->num_rows();
            $query = $this->db->query("SELECT * FROM logs WHERE item_type='cloud' ".
            "AND item_id = $cloud_id AND user_id <> 0 GROUP BY user_id");
           $total_views += $query->num_rows();
        }
        return $total_views;
    }
        
    /**
     * Gets the total number of clouds on the site
     *
     * @return integer The number of clouds
     */
    function get_total_clouds() {
        $query = $this->db->get('cloud');
        return $query->num_rows();
    }
    
    /**
     * Get the details of a cloud for a given cloud id 
     *
     * @param integer $cloud_id The ID of the cloud
     * @return object The details of the cloud
     */
    function get_cloud($cloud_id) {

        $cloud = false;
        $this->db->from('cloud');
        $this->db->where('cloud.cloud_id', $cloud_id);
        $this->db->where('user.banned',0);     
        $this->db->join('user_picture', 'cloud.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = cloud.user_id');
        $this->db->join('user', 'user_profile.id = user.id');        
        $query = $this->db->get();
        if ($query->num_rows() !=  0 ) {
            $cloud = $query->row();
        }

        $cloud->owner = false;
        if ($this->auth_lib->is_logged_in()) {
            $user_id = $this->db_session->userdata('id');
            $cloud->owner = $this->has_edit_permission($user_id, $cloud_id);

        } 

        return $cloud;
    }
    
    /**
     * Get the details of a cloud given its title 
     *
     * @param string $cloud_title The title of the cloud
     * @return object The details of the cloud
     */
    function get_cloud_by_title($cloud_title) {
        $cloud = false;
        $this->db->from('cloud');
        $this->db->where('cloud.title', $cloud_title);
        $this->db->where('user.banned',0);       
        $this->db->join('user_picture', 'cloud.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = cloud.user_id');
        $this->db->join('user', 'user_profile.id = user.id');               
        $query = $this->db->get();
        if ($query->num_rows() !=  0 ) {
            $cloud = $query->row();
        }
       
        return $cloud;
    }  
    
    /**
     * Determine if a user has edit permission for a specified cloud - 
     * they have edit permission if they are either the owner of the cloud or a site
     * admin
     *
     * @param integer $user_id The ID of the user
     * @param integer $cloud_id The ID of the cloud
     * @return boolean TRUE if they have edit permission, FALSE otherwise
     */
    function has_edit_permission($user_id, $cloud_id) {
        $permission = FALSE;
        if ($user_id) {
            $owner_user_id = $this->get_owner($cloud_id);
            if ($user_id == $owner_user_id || $this->auth_lib->is_admin()) {
                $permission = TRUE;
            }
        }

        return $permission;
    }
    
    /**
     * Check if a user has edit permission for a specified cloud (see has_edit_permission 
     * function). If not display an error page.
     *
     * @param integer $user_id The ID of the user
     * @param integer $cloud_id The ID of the cloud
     */
    function check_edit_permission($user_id, $cloud_id) {
        if (!$this->has_edit_permission($user_id, $cloud_id)) {
            show_error(t("You do not have edit permission for that cloud."));   
        } 
    }
    
    /**
     * Determine if a user is the owner of a cloud 
     *
     * @param integer $user_id The ID of the user
     * @param integer $cloud_id The ID of the cloud
     * @return boolean TRUE if they are the owner, FALSE otherwise.
     */
    function is_owner($cloud_id, $user_id) {
        $owner = false;
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('cloud');
        if ($query->num_rows() > 0) {
            $owner = true;
        }

        return $owner;
    }
    
    /**
     * Get the owner/creator of a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return integer The ID of the owner
     */
    function get_owner($cloud_id) {
        $this->db->where('cloud_id', $cloud_id);
        $query = $this->db->get('cloud');
        if ($query->num_rows() > 0) {
            $cloud = $query->row();
            $owner_user_id = $cloud->user_id;
        }

        return $owner_user_id;
    }
     
    /**
     * Get the cloudscapes that a cloud belongs to 
     *
     * @param integer $cloud_id The ID of the cloud
     * @return array Array of cloudscapes
     */
    function get_cloudscapes($cloud_id) {
        $this->db->from('cloudscape');
        $this->db->where('cloudscape_cloud.cloud_id', $cloud_id);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'cloudscape.user_id = user.id');        
        $this->db->join('cloudscape_cloud', 'cloudscape_cloud.cloudscape_id = cloudscape.cloudscape_id');
        $query = $this->db->get();
        return $query->result();        
    }
    
    /**
     * Get the newest clouds on the site
     *
     * @param integer $num Limit to the number of clouds to return
     * @return array Array of clouds
     */
    function get_new_clouds($limit) {
        $this->db->where('omit_from_new_list', 0);
        $this->db->where('moderate', 0);
        $this->db->select('cloud_id, title');
        $this->db->order_by("created", "desc");  
        $query = $this->db->get('cloud', $limit);
        return $query->result();
    }

    /**
     * Insert a cloud
     *
     * @param $cloud object The details of the cloud
     * @return integer $cloud_id The ID of the newly created cloud
     */
    function insert_cloud($cloud) {
        $valid = true;
        
        // Check that the user_id, title and body have been specified and that the user_id is numeric
        if (!$cloud->user_id || !$cloud->title || trim($cloud->title) == '' || !is_numeric($cloud->user_id)) {
        }
        
        if ($valid) { 
            if (!$cloud->moderate) {
                $cloud->moderate = 0;
            }
            $cloud->created = time();
            $this->db->insert('cloud', $cloud);
            $cloud_id =  $this->db->insert_id();
            
            if (!$cloud->moderate) {
                $this->approve_cloud($cloud_id);
            }              
        } else {
            $cloud_id = FALSE;
        }
        return $cloud_id;
    }
    
    /**
     * Update an existing cloud
     *
     * @param object $cloud The details to update
     */
    function update_cloud($cloud) {
        $cloud_id       = $cloud->cloud_id;
        $cloud->modified = time();
        $this->db->update('cloud', $cloud, array('cloud_id'=>$cloud_id)); 
        $this->update_in_search_index($cloud->cloud_id);
    }
    
   /**
    * Hide a cloud from the site cloudstream and lists of new clouds
    *
    * @param integer $cloud_id The ID of the cloud
    */    
    function hide_cloud($cloud_id) {
        $this->db->where('cloud_id', $cloud_id);
        $this->db->update('cloud', array('omit_from_new_list'=>1));
        
        $this->db->where('event_item_id', $cloud_id);
        $this->db->where('event_type', 'cloud');
        $this->db->update('event', array('omit_from_site_cloudstream'=>1));
    }

    /**
     * Delete an existing cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function delete_cloud($cloud_id) {
        // Delete from the search index - need to do this before the cloud is deleted
        $this->remove_from_search_index($cloud_id);
        
        $this->db->delete('cloud', array('cloud_id' => $cloud_id)); 
        $this->db->delete('cloudscape_cloud', array('cloud_id' => $cloud_id)); 
        $this->load->model('event_model');
        $event_model = new event_model();           
        $event_model->delete_events('cloud', $cloud_id);
        // Don't delete the comments until after the events as comments will have events
        $this->db->delete('comment', array('cloud_id' => $cloud_id));   
    }    
    
    /**
     * Update the entry for a cloud in the search index
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function update_in_search_index($cloud_id) {
    	if (config_item('x_search')) {
	        $this->CI=& get_instance();
	        $this->CI->load->model('search_model');
			$this->CI->search_model->update_item_in_index(base_url().'cloud/view/'.$cloud_id, $cloud_id, 'cloud');    
    	}
    }
    
    /**
     * Remove a cloud from the search inde
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function remove_from_search_index($cloud_id) {
    	if (config_item('x_search')) {
	        $this->CI=& get_instance();
	        $this->CI->load->model('search_model');
			$this->CI->search_model->delete_item_from_index($cloud_id, 'cloud');
    	}
    }
    
    /**
     * Approve a cloud that has been flagged for moderation 
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function approve_cloud($cloud_id) {
        $this->db->where('cloud_id', $cloud_id);
        $this->db->update('cloud', array('moderate'=>0)); 
        $cloud = $this->get_cloud($cloud_id);
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->add_event('user', $cloud->id, 'cloud', $cloud_id);
        $cloudscapes = $this->get_cloudscapes($cloud_id);
        if ($cloudscapes) {
            foreach ($cloudscapes as $cloudscape) {
                $event_model->event_model->add_event('cloudscape', $cloudscape->cloudscape_id, 
                'cloud', $cloud_id, $cloud->created); 
            }
        }
        // Add to the search index
        $this->update_in_search_index($cloud_id);
    }
    
    /**
     * Get all the clouds currently in moderations
     *
     * @return Array of clouds
     */
    function get_clouds_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = cloud.user_id');
        $query = $this->db->get('cloud');
        return $query->result();
    }
       
    /**
     * Add some tags to a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param string $tags comma-separated string of tags 
     * @param integer $user_id The ID of the user adding the tags
     */
    function add_tags($cloud_id, $tags, $user_id) {
        $this->tag_model->add_tags('cloud', $cloud_id, $tags, $user_id);
        $this->update_in_search_index($cloud_id);    
    }
    
    /**
     * Log a view of a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */  
    function log_view($cloud_id) {
        $this->db->set('item_id', $cloud_id);   
        $this->db->set('item_type', 'cloud');
        $this->db->set('timestamp', time());
        $user_id = $this->db_session->userdata('id');
        $this->db->set('user_id', $user_id);
        $this->db->set('ip', $this->input->ip_address()); 
        $this->db->insert('logs');       
    }
    
   /**
     * Get the references for a specific cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return array Array of references
     */
    function get_references($cloud_id) {
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('moderate', 0);       
        $this->db->where('user.banned',0);          
        $this->db->join('user', 'cloud_reference.user_id = user.id');        
        $this->db->join('user_profile', 'user_profile.id = cloud_reference.user_id');        
        $this->db->order_by('timestamp', 'asc');
        $query = $this->db->get('cloud_reference');
        return $query->result(); 
    }  

    /**
     * Get a reference
     *
     * @param integer $reference_id
     * @return object Details of the reference
     */
    function get_reference($reference_id) {
        $this->db->where('reference_id', $reference_id);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'user.id = cloud_reference.user_id');
        $this->db->join('user_profile', 'user_profile.id = cloud_reference.user_id');                
        
        $query = $this->db->get('cloud_reference');
        
        if ($query->num_rows() !=  0 ) {
            $reference = $query->row();
        }
        return $reference;
    }
      
    /**
     * Add a new reference for a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param string $reference_text The text for the reference
     * @param integer $user_id The ID of the user adding the reference
     * @param boolean $moderate TRUE if the reference needs moderation, FALSE otherwise
     * @return integer The ID of the newly created reference
     */
    function add_reference($cloud_id, $reference_text, $user_id, $moderate) {
        $reference->cloud_id  = $cloud_id;
        $reference->reference_text = $reference_text;
        $reference->user_id   = $user_id;
        $reference->timestamp = time();
        $reference->moderate  = $moderate;
        $this->db->insert('cloud_reference', $reference); 
        $reference_id = $this->db->insert_id();
        
        if (!$moderate) {
            $this->approve_reference($reference_id);
        }
        $this->update_in_search_index($cloud_id);
        return $reference_id;   
    }
    
    /**
     * Remove a reference
     *
     * @param integer $reference_id The ID of the reference
     */
    function delete_reference($reference_id) {
        $this->db->delete('cloud_reference', array('reference_id' => $reference_id));
         $this->load->model('event_model');
        $event_model = new event_model();  
        $event_model->delete_events('reference', $reference_id);  
              
    }    
    
    /**
     * Approve a reference
     *
     * @param integer $reference_id The ID of the reference
     */
    function approve_reference($reference_id) {
        if ($reference_id) {
            $this->db->where('reference_id', $reference_id);
            $this->db->update('cloud_reference', array('moderate'=>0)); 
            $reference = $this->get_reference($reference_id);
            $this->load->model('event_model');
            $event_model = new event_model();
            $event_model->add_event('cloud', $reference->cloud_id, 'reference', $reference_id);
            $event_model->add_event('user', $reference->user_id, 'reference', $reference_id);
        }   
    }  
    
    /**
     * Get all reference under moderation
     *
     * @return Array of references
     */
    function get_references_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = cloud_reference.user_id');        
        $this->db->order_by('timestamp', 'asc');
        $query = $this->db->get('cloud_reference');
        return $query->result();   
    }   

    /***************************************************************************************
     * FOLLOWERS OF A CLOUD 
     * *************************************************************************************/

    /**
     * Get the followers of a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return Array of users following the cloud
     */
    function get_followers($cloud_id) {
        $this->db->from('user_profile');
        $this->db->where('cloud_followed.cloud_id', $cloud_id);
        $this->db->join('cloud_followed', 'user_profile.id = cloud_followed.user_id');
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 'left');
        $query = $this->db->get();
        return $query->result();         
    }

    /**
     * Get total number of followers of a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return integer The number of users
     */
    function get_total_followers($cloud_id) {
        $this->db->from('user_profile');
        $this->db->where('cloud_followed.cloud_id', $cloud_id);
        $this->db->join('cloud_followed', 'user_profile.id = cloud_followed.user_id', 'left');
        $total_followers = $this->db->count_all_results();  
        return $total_followers;   
    }

    /**
     * Add a user to the followers of the cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $user_id The ID of the user to add
     */
    function follow($cloud_id, $user_id) {
        if (!$this->is_following($cloud_id, $user_id)) {
            $this->db->set('cloud_id', $cloud_id);
            $this->db->set('user_id', $user_id);
            $this->db->set('timestamp', time());
            $this->db->insert('cloud_followed');
        } 
    }
    
    /**
     * Determine if a user is following a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user is following the cloud, FALSE otherwise
     */
    function is_following($cloud_id, $user_id) {        
        $following = false;
        if ($user_id) {
            $this->db->where('cloud_id', $cloud_id);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('cloud_followed');
            
            if ($query->num_rows() > 0) {
                $following = TRUE;
            }
        }
        return $following;
    }
    
    /**
     * Remove a user from the followers of a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $user_id The ID of the user
     */
    function unfollow($cloud_id, $user_id) {
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('cloud_followed'); 
    }    
    
    /**
     * Recache the most popular clouds on the site in the cloud_popular table
     */
    function repopulate_popular_clouds() {
        // Clear the popular clouds table
        $query = $this->db->query("DELETE FROM cloud_popular");
        
        // Calculate the most popular clouds
        $clouds = $this->calculate_popular_clouds();
        // Repopular popular clouds table  
        foreach ($clouds as $cloud) {
            $this->db->insert('cloud_popular', array('cloud_id'=>$cloud->cloud_id));
        }     
    }
    
    /**
     * Calculate the most populate clouds on the site. 
     * At the moment this algorithm is very simplistic, but can make more sophisticated 
     * eventually
     *
     * @return Array of cloud IDs
     */
    function calculate_popular_clouds($limit = 30) {
        $days = $this->config->item('popular_clouds_days') ? 
                    $this->config->item('popular_clouds_days') : 10;
        $since = time() - 60*60*24* $days;
        $query = $this->db->query("SELECT c.cloud_id, COUNT(*) AS total_favourites
                  FROM cloud c INNER JOIN favourite f ON f.item_id = c.cloud_id 
                  AND f.item_type = 'cloud'
                  WHERE f.timestamp > $since
                    GROUP BY c.cloud_id ORDER BY total_favourites DESC LIMIT $limit"); 
       return $query->result();      
    }
    
    /**
     * Get the most popular clouds on the site 
     *
     * @param integer $num Limit to the number of clouds to return
     * @return array Array of clouds
     */
    function get_popular_clouds($limit = 10) {
        $query = $this->db->query("SELECT c.cloud_id, c.title FROM cloud_popular cp
        INNER JOIN cloud c ON c.cloud_id = cp.cloud_id
        ORDER BY RAND() LIMIT $limit", $limit);
        return $query->result();
    } 
}