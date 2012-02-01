<?php 
/**
 * Model file for cloudscape-related functions
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Cloudscape
 */
class Cloudscape_model extends Model {
    
    function Cloudscape_model() {
        parent::Model();
        $current_user_id = $this->db_session->userdata('id');
    }
    
    /***************************************************************************************
     * BASIC CLOUDSCAPE METHODS
     * *************************************************************************************/
        
    /**
     * Get all cloudscapes with titles starting with a specified letter of the alphabet
     *
     * @param string $alpha The letter of the alphabet
     * @return array Array of cloudscapes
     */
    function get_cloudscapes_alpha($alpha = 'A') {
       if (strlen($alpha) != 1) {
          $alpha = 'A';
       }
       $query = $this->db->query("SELECT c.cloudscape_id, c.title, COUNT(*) AS no_clouds, 
                                  c.summary, c.cloudscape_id, c.created FROM cloudscape c 
                                  INNER JOIN cloudscape_cloud cc
                                  ON c.cloudscape_id = cc.cloudscape_id 
                                  INNER JOIN user u on u.id = c.user_id
                                  WHERE ltrim(c.title) LIKE'$alpha%' 
                                  AND c.moderate = 0
                                  AND banned = 0
                                  GROUP BY c.cloudscape_id
                                  ORDER BY c.title ASC");	
      return $query->result();
    }  
    
    /**
     * Get all cloudscapes on the site in alphabetical order
     *
     * @return array Array of cloudscapes
     */
    function get_cloudscapes() {

       $query = $this->db->query("SELECT * FROM cloudscape WHERE moderate = 0 
                                  ORDER BY title ASC");	
        return $query->result();
    } 
    
    /**
     * Get the total number of times a cloud has been viewed
     * - multiple views by the same logged in user count as one view
     * - mulitple views by a guest at the same IP address count as one view
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of views 
     */
    function get_total_views($cloudscape_id) {
        $total_views = 0;
        if (is_numeric($cloudscape_id)) {
            $query = $this->db->query("SELECT * FROM logs WHERE item_type='cloudscape' 
                                       AND item_id = $cloudscape_id AND user_id = 0 
                                       GROUP BY ip");
            $total_views += $query->num_rows();
            $query = $this->db->query("SELECT * FROM logs WHERE item_type='cloudscape' 
                                       AND item_id = $cloudscape_id AND user_id <> 0 
                                       GROUP BY user_id");
           $total_views += $query->num_rows();
        }
        return $total_views;
    }
    
    /**
     * Get cloudscapes under moderation
     *
     * @return array Array of cloudscapes
     */
    function get_cloudscapes_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = cloudscape.user_id');
        $query = $this->db->get('cloudscape');
        return $query->result();
    }
    
    /**
     * Approve a cloudscape that has been flagged for moderation 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function approve_cloudscape($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->update('cloudscape', array('moderate'=>0));
        $cloudscape = $this->get_cloudscape($cloudscape_id);
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->add_event('user', $cloudscape->id, 'cloudscape', $cloudscape_id);
        $this->update_in_search_index($cloudscape_id);
    }

    /**
     * Get the cloudscape with a given id 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return object Details of the cloudscape
     */
    function get_cloudscape($cloudscape_id) {
        $cloudscape = false;
        $this->db->from('cloudscape');
        // Call select() to ensure we don't include the user.created field.
        $this->db->select('cloudscape.*, user_picture.*, user_profile.*');
        $this->db->where('cloudscape.cloudscape_id', $cloudscape_id);
        $this->db->where('user.banned', 0);
        $this->db->join('user', 'user.id = cloudscape.user_id');
        $this->db->join('user_picture', 'cloudscape.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = cloudscape.user_id');
        $query = $this->db->get();
        if ($query->num_rows() !=  0 ) {
            $cloudscape = $query->row();
            $cloudscape->admin = false;
            $cloudscape->poster = false;
            $cloudscape->owner = false;
            
            if ($this->auth_lib->is_logged_in()) {
                $user_id = $this->db_session->userdata('id');
                $cloudscape->admin = $this->is_admin($cloudscape_id, $user_id);
                $cloudscape->poster = $this->is_poster($cloudscape_id, $user_id);
                $cloudscape->owner = $this->has_owner_permissions($cloudscape_id, $user_id);
            }
            if (!$cloudscape->user_id) {
                $cloudscape->user_id = $cloudscape->id;
            }  
            
            $cloudscape->dates = FALSE;
            if ($cloudscape->start_date) {
                if ($cloudscape->start_date) {
                    $cloudscape->dates = date('j F Y', $cloudscape->start_date);
                }
                     
                if  ($cloudscape->end_date && 
                     ($cloudscape->end_date != $cloudscape->start_date)) {
                    $cloudscape->dates .=  ' - '.date('j F Y', $cloudscape->end_date); 
                }
            }

        } else {
            $cloudscape = false;
        }

        return $cloudscape;
    }
    
    /**
     * Get a cloudscape from its title
     *
     * @param string $cloudscape_title The title of the cloudscape
     * @return object Details of the cloudscape
     */
    function get_cloudscape_by_title($cloudscape_title) {
        $cloudscape = false;
        $this->db->from('cloudscape');
        $this->db->where('cloudscape.title', $cloudscape_title);
        $this->db->join('user_picture', 'cloudscape.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = cloudscape.user_id');
        $query = $this->db->get();
        if ($query->num_rows() !=  0 ) {
            $cloudscape = $query->row();

        }
        return $cloudscape;
    }       

    /**
     * Get the featured cloudscapes 
     *
     * @param integer $limit Number of cloudscapes to get
     * @return array Array of featured cloudscapes
     */
    function get_featured_cloudscapes($limit) {
        $this->db->orderby('order', 'asc');
        $this->db->join('cloudscape', 
                        'cloudscape.cloudscape_id = featured_cloudscape.cloudscape_id');
        $query = $this->db->get('featured_cloudscape', $limit);
        return $query->result();
    }
    
    /**
     * Update the featured cloudscapes
     *
     * @param array $cloudscapes Array of the IDs of the new featured cloudscapes
     */
    function update_featured_cloudscapes($cloudscapes) {
        for ($i = 0; $i< 5; $i++) {
            $this->db->where('order', $i + 1);
            $this->db->update('featured_cloudscape', 
                              array('cloudscape_id'=>$cloudscapes[$i]));
        }
    }

    /**
     * Get the default featured cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return object Details of the default featured cloudscape
     */
     function get_default_cloudscape($cloudscape_id = false) {
        $this->db->orderby('order', 'asc');
        $this->db->join('cloudscape', 
                         'cloudscape.cloudscape_id = featured_cloudscape.cloudscape_id');

        $query = $this->db->get('featured_cloudscape');
        $first_row = TRUE;
        foreach($query->result() as $row) {
            if ($first_row) {
                $default_cloudscape_id = $row->cloudscape_id;
                $first_row = false;
            }

            if ($cloudscape_id == $row->cloudscape_id) {
                $default_cloudscape_id = $cloudscape_id;
            }
        }

        $this->db->where('cloudscape.cloudscape_id', $default_cloudscape_id);
        $this->db->join('cloudscape', 
                        'cloudscape.cloudscape_id = featured_cloudscape.cloudscape_id');
        $query = $this->db->get('featured_cloudscape');

        return $query->row();
    }
    
   /**
    * Hide the cloudscape from the site cloudstream and lists of new cloudscapes 
    *
     * @param integer $cloudscape_id The ID of the cloudscape
    */
   function hide_cloudscape($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->update('cloudscape', array('omit_from_new_list'=>1));
        $this->db->where('event_item_id', $cloudscape_id);
        $this->db->where('event_type', 'cloudscape');
        $this->db->update('event', array('omit_from_site_cloudstream'=>1));
    }
    
   /**
    * Remove a cloudscape from the events diary
    * @param integer $cloudscape_id The ID of the cloudscape
    */
    function remove_diary($cloud_id) {
        $this->db->where('cloudscape_id', $cloud_id);
        $this->db->update('cloudscape', array('display_event'=>0));
    }
 
   /**
    * Add a cloudscape to the events diary
    * @param integer $cloud_id The ID of the cloudscape
    */ 
    function add_diary($cloud_id) {
        $this->db->where('cloudscape_id', $cloud_id);
        $this->db->update('cloudscape', array('display_event'=>1));
    }    
       
    /**
     * Get the new cloudscapes on the site
     *
     * @param integer $limit Limit to number of clouscapes to get
     * @return array Array of cloudscapes 
     */
    function get_new_cloudscapes($limit) {
        $this->db->where('moderate', 0);
        $this->db->where('omit_from_new_list', 0);
        $this->db->select('cloudscape_id, title');
        $this->db->order_by("created", "desc");  
        $query = $this->db->get('cloudscape', $limit);
        return $query->result();
    }

    /**
     * Get the total number of cloudscapes on the site
     *
     * @return integer The number of cloudscapes
     */
    function get_total_cloudscapes() {
        $query = $this->db->get('cloudscape');
        return $query->num_rows();
    }
     
    /**
     * Insert a new cloudscape
     *
     * @param object $cloudscape The cloudscape details to insert
     * @return integer The ID of the new cloudscape
     */
    function insert_cloudscape($cloudscape) {
        $cloudscape->created = time();
        $cloudscape->open = 1;
        if (!$cloudscape->moderate) {
            $cloudscape->moderate = 0;
        }
        $this->db->insert('cloudscape', $cloudscape);
        $cloudscape_id =  $this->db->insert_id(); 

        $this->follow($cloudscape_id, $cloudscape->user_id);
        if (!$cloudscape->moderate) {
            $this->approve_cloudscape($cloudscape_id);
        }
        return $cloudscape_id;
    }

    /**
     * Update an existing cloudscape
     *
     * @param object $cloudscape The details of the cloudscape to update
     */
    function update_cloudscape($cloudscape) {
        $cloudscape_id = $cloudscape->cloudscape_id;
        $cloudscape->modified = time();
        $this->db->update('cloudscape', $cloudscape, array('cloudscape_id'=>$cloudscape_id));        
        $this->update_in_search_index($cloudscape->cloudscape_id);
    }
    
    /**
     * Delete a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function delete_cloudscape($cloudscape_id) {
        $this->db->delete('cloudscape', array('cloudscape_id' => $cloudscape_id));
        $this->db->delete('cloudscape_cloud', array('cloudscape_id' => $cloudscape_id));        
        // Delete any events associate with this cloudscape
        $this->load->model('event_model');
        $event_model = new event_model();           
        $event_model->delete_events('cloudscape', $cloudscape_id); 
        $this->remove_from_search_index($cloudscape_id);       
    }
    
    /**
     * Update the entry for a cloudscape in the search index
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function update_in_search_index($cloudscape_id) {
    	if (config_item('x_search')) {
	        $this->CI=& get_instance();
	        $this->CI->load->model('search_model');
			$this->CI->search_model->update_item_in_index(
			     base_url().'cloudscape/view/'.$cloudscape_id, $cloudscape_id, 'cloudscape');    
	    }
    }
        
    /**
     * Remove a cloudscape from the search inde
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function remove_from_search_index($cloudscape_id) {
    	if (config_item('x_search')) {
	        $this->CI=& get_instance();
	        $this->CI->load->model('search_model');
			$this->CI->search_model->delete_item_from_index($cloudscape_id, 'cloudscape');
    	}
    }    

    /**
     * Log a view of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function log_view($cloudscape_id) {
        $this->db->set('item_id', $cloudscape_id);
        $this->db->set('item_type', 'cloudscape');
        $this->db->set('timestamp', time());
        $this->db->set('user_id', $this->db_session->userdata('id'));
        $this->db->set('ip', $this->input->ip_address()); 
        $this->db->insert('logs');
    }  
    
    /***************************************************************************************
     * CLOUDS IN A CLOUDSCAPE 
     * *************************************************************************************/
    
    /**
     * Get the clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return array Array of clouds
     */
    function get_clouds($cloudscape_id) {
        $valid = false;
        if (is_numeric($cloudscape_id)) {
            $valid = TRUE;
        }

         if ($valid) {  
            $query = $this->db->query("SELECT cl.title as title, cl.body as body, 
            cl.cloud_id as cloud_id, COUNT(co.comment_id) AS total_comments, 
            cl.created AS timestamp, cl.created AS created, cl.modified AS modified
            FROM  cloud cl 
            LEFT OUTER JOIN comment co ON cl.cloud_id = co.cloud_id
            INNER JOIN cloudscape_cloud cc ON cc.cloud_id = cl.cloud_id 
            INNER JOIN user u on u.id = cl.user_id
            WHERE cc.cloudscape_id = $cloudscape_id 
            AND cl.moderate = 0 
            AND u.banned = 0
            GROUP BY cl.cloud_id ORDER BY title");
            $clouds = $query->result();
         }
         
        return $clouds;
    }
    
    /**
     * Get the number of clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of clouds
     */
    function get_total_clouds($cloudscape_id) {
        $this->db->from('cloud');
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->join('cloudscape_cloud', 'cloudscape_cloud.cloud_id = cloud.cloud_id');
        $total_clouds = $this->db->count_all_results(); 
        return $total_clouds;    
    }         
    
    /**
     * Determine if a cloud belongs to a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $cloud_id The ID of the cloud
     * @return boolean TRUE if the cloud belongs to the cloudscape, FALSE otherwise
     */
    function belongs($cloudscape_id, $cloud_id) {
        $cloud = false;
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('cloud_id', $cloud_id);
        $query = $this->db->get('cloudscape_cloud');
        if ($query->num_rows() > 0) {
            $cloud = TRUE;
        }
        
        return $cloud;
    } 
       
    /**
     * Add a cloud to a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $cloud_id The ID of the cloud
     */
    function add_cloud($cloudscape_id, $cloud_id) {
        if (!$this->belongs($cloudscape_id, $cloud_id)) {
            $this->db->set('cloudscape_id', $cloudscape_id);
            $this->db->set('cloud_id', $cloud_id);
            $user_id = $this->db_session->userdata('id');
            $this->db->set('user_id', $user_id);
            $this->db->insert('cloudscape_cloud');
            
            // If cloud is moderated, add it
            $this->load->model('cloud_model');
            $cloud_model = new cloud_model();              
            $cloud = $cloud_model->get_cloud($cloud_id); 
            if (!$cloud->moderate) {
                $this->load->model('event_model');
                $event_model = new event_model();    
                $event_model->add_event('cloudscape', $cloudscape_id, 'cloud', $cloud_id);
            }
        }
        
        $this->update_in_search_index($cloudscape_id);
    }  
    
    /**
     * Get the user who added a specific cloud to a specific cloudscape
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function get_cloud_added_user($cloud_id, $cloudscape_id) {
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->join('user_profile', 'user_profile.id = cloudscape_cloud.user_id');
        $query = $this->db->get('cloudscape_cloud');
        $result = $query->result();
        $user= $result[0];
        return $user;
    }
    
    /**
     * Remove a cloud from a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $cloud_id The ID of the cloud
     */
    function remove_cloud($cloudscape_id, $cloud_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('cloud_id', $cloud_id);
        $this->db->delete('cloudscape_cloud'); 
        $this->update_in_search_index($cloudscape_id);
    }  

    /***************************************************************************************
     * FOLLOWERS OF A CLOUDSCAPE 
     * *************************************************************************************/
    
    /**
     * Get the followers of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return array Array of users following the cloudscape
     */
    function get_followers($cloudscape_id) {
        $this->db->from('user_profile');
        $this->db->where('cloudscape_followed.cloudscape_id', $cloudscape_id);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'user_profile.id = user.id');                
        $this->db->join('cloudscape_followed', 
                        'user_profile.id = cloudscape_followed.user_id');
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 
                        'left');
        $query = $this->db->get();
        return $query->result();         
    }
    
    /**
     * Get total number of followers of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of followers
     */
    function get_total_followers($cloudscape_id) {
        $this->db->from('user_profile');
        $this->db->where('cloudscape_followed.cloudscape_id', $cloudscape_id);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'user_profile.id = user.id');                
        $this->db->join('cloudscape_followed', 
                        'user_profile.id = cloudscape_followed.user_id', 'left');
        $total_followers = $this->db->count_all_results();  
        return $total_followers;   
    }
            
    /**
     * Add a user to the followers of the cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     */
    function follow($cloudscape_id, $user_id) {
        if (!$this->is_following($cloudscape_id, $user_id)) {
            $this->db->set('cloudscape_id', $cloudscape_id);
            $this->db->set('user_id', $user_id);
            $this->db->set('timestamp', time());
            $this->db->insert('cloudscape_followed');
        } 
    }
    
    /**
     * Determine if a user is following a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user is following the cloudscape, FALSE otherwise
     */
    function is_following($cloudscape_id, $user_id) {        
        $following = FALSE;
        if ($user_id) {
            $this->db->where('cloudscape_id', $cloudscape_id);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('cloudscape_followed');
            
            if ($query->num_rows() > 0) {
                $following = TRUE;
            }
        }
        return $following;
    }
    
    /**
     * Remove a user from the followers of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     */
    function unfollow($cloudscape_id, $user_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('cloudscape_followed'); 
    }
    

    /***************************************************************************************
     * CLOUDSCAPE PERMISSIONS
     * *************************************************************************************/
         
    /**
     * Determine if a user is the owner of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user is the owner, FALSE otherwise
     */
    function is_owner($cloudscape_id, $user_id) {
        if ($user_id) {
            $owner = FALSE;
            $this->db->where('cloudscape_id', $cloudscape_id);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('cloudscape');
            if ($query->num_rows() > 0) {
                $owner = TRUE;
            }
        }
        return $owner;
    }
    
    /**
     * Determine if the user has owner permissions for the cloudscape i.e. is either 
     * the owner of the cloudscape or a site admin
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user has owner permissions, FALSE otherwise
     */
    function has_owner_permissions($cloudscape_id, $user_id) {
        $owner_permissions = FALSE;
        if ($this->is_owner($cloudscape_id, $user_id)) {
            $owner_permissions = TRUE;
        }
        if ($this->auth_lib->is_admin()) {
            $owner_permissions = TRUE;
        } 
        
        return $owner_permissions;       
    }    
    
    /**
     * Get the admins for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return array Array of users who are admins for the cloudscape
     */
    function get_admins($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->join('user_profile', 'user_profile.id = cloudscape_admin.user_id');
        $query = $this->db->get('cloudscape_admin');
        return $query->result();
    }
        
    /**
     * Determine if a user is an admin of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user is an admin of the cloudscape, FALSE otherwise
     */
    function is_admin($cloudscape_id, $user_id) {
        $admin = false;
        if ($user_id) {
            $this->db->where('cloudscape_id', $cloudscape_id);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('cloudscape_admin');
            if ($query->num_rows() > 0) {
                $admin = TRUE;
            }
        }
        return $admin;
    }
        
    
    /**
     * Add an admin to a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user to add
     */
    function add_admin($cloudscape_id, $user_id) {
        if ($user_id) {
            if (!$this->is_admin($cloudscape_id, $user_id)) {
                $this->db->set('cloudscape_id', $cloudscape_id);
                $this->db->set('user_id', $user_id);
                $this->db->insert('cloudscape_admin');
            }
        }
    }
    
    /**
     * Remove an admin from a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user to remove
     */
    function remove_admin($cloudscape_id, $user_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('cloudscape_admin'); 
    }   

  
    
    /**
     * Get the posters for a cloudscape i.e. the user with permission to add clouds to the
     * cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return array Array of users with post permissions
     */
    function get_posters($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->join('user_profile', 'user_profile.id = cloudscape_poster.user_id');
        $query = $this->db->get('cloudscape_poster');
        return $query->result();        
    } 
     
    /**
     * Determine if a user is a poster for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user has post permission, FALSE otherwise
     */
    function is_poster($cloudscape_id, $user_id) {
        $poster = FALSE;
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('cloudscape_poster');
        if ($query->num_rows() > 0) {
            $poster = TRUE;
        }
        
        return $poster;
    }
         
    /**
     * Add a poster to a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user to add
     */
    function add_poster($cloudscape_id, $user_id) {
        if (!$this->is_poster($cloudscape_id, $user_id)) {
            $this->db->set('cloudscape_id', $cloudscape_id);
            $this->db->set('user_id', $user_id);
            $this->db->insert('cloudscape_poster');
        }
    }
    
    /**
     * Remove a user as a poster for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user to remove
     */
    function remove_poster($cloudscape_id, $user_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('cloudscape_poster'); 
    }    
    
    /**
     * Determine if a cloudscape is open for any user to post to
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return boolean TRUE if the cloudscape is open, FALSE otherwise
     */
    function is_open($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $query = $this->db->get('cloudscape');
        $cloudscape = $query->row();
        return $cloudscape->open;        
    }
    
    /**
     * Make a cloudscape open
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function set_open($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->update('cloudscape', array('open'=>1));
    }
    
    /**
     * Make a cloudscape closed i.e. not open
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function set_closed($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->update('cloudscape', array('open'=>0));
    } 
       
    /**
     * Get all the cloudscapes for which a user has post permission
     *
     * @return array Array of cloudscapes
     */
    function get_cloudscapes_post($user_id) {
        $query = $this->db->query("SELECT cloudscape_id, title 
                                    FROM cloudscape WHERE user_id = $user_id
                                    UNION 
                                    SELECT c.cloudscape_id, title FROM cloudscape_poster cp
                                    INNER JOIN cloudscape c
                                    ON c.cloudscape_id = cp.cloudscape_id
                                    WHERE cp.user_id = $user_id
                                    UNION
                                    SELECT c.cloudscape_id, title FROM cloudscape_admin ca
                                    INNER JOIN cloudscape c
                                    ON c.cloudscape_id = ca.cloudscape_id
                                    WHERE ca.user_id = $user_id
                                    UNION
                        
                                    SELECT cloudscape_id, title
                                    FROM cloudscape WHERE open = 1 ORDER BY title");
        return $query->result();
           
    }  
    
    /**
     * Get all the cloudscapes created by a particular user
     *
     * @param integer $user_id The ID of the user
     * @return array Array of cloudscapes 
     */
    function get_cloudscapes_owner($user_id) {
        $this->db->order_by('title', 'asc');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('cloudscape');
        return $query->result();
    }
    
    /**
     * Determine if a user has permission to add a cloud to a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user has post permission, FALSE otherwise
     */
    function has_post_permission($cloudscape_id, $user_id) {
        $permission = FALSE;
        if ($user_id) {
            
    
            if ($this->is_open($cloudscape_id) || 
                $this->has_owner_permissions($cloudscape_id, $user_id) ||
                $this->is_admin($cloudscape_id, $user_id) || 
                $this->is_poster($cloudscape_iud, $user_id) || $this->auth_lib->is_admin()) {
                $permission = TRUE;
            }
        }

        return $permission;
    }    
    
    /**
     * Determine if a user has admin permission for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if a user has admin permission, FALSE otherwise
     */
    function has_admin_permission($cloudscape_id, $user_id) {
        $permission = FALSE;
        if ($user_id) {
            if ($this->is_admin($cloudscape_id, $user_id) || 
                $this->is_owner($cloudscape_id, $user_id)  || 
                $this->auth_lib->is_admin()) {
                $permission = TRUE;   
            }
        }
        return $permission;
    }


    /**
     * Check if a user has admin permission for a cloudscape, if not send to error page
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     */
    function check_admin_permission($cloudscape_id, $user_id) {
        if (!$this->has_admin_permission($cloudscape_id, $user_id)) {
            show_error(t("You do not have admin permissions for that cloudscape"));
        }
    } 
     
    /**
     * Check if a user has post permission for a cloudscape, if not send to error page
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $user_id The ID of the user
     */
    function check_post_permission($cloudscape_id, $user_id) {
        if (!$this->has_post_permission($cloudscape_id, $user_id)) {
            show_error(t(
			'The owner of this cloudscape has restricted who is allowed to add clouds to this cloudscape.'));
        }
    } 

    /**
     * Search the cloudscapes that a user has permission to post in
     *
     * @param integer $user_id The ID of the user
     * @param string $search_string The string to search for
     * @return array Array of cloudscapes
     */
    function search_post_permission($user_id, $search_string) {
        
        $query = $this->db->query("SELECT cloudscape_id, title 
                                    FROM cloudscape WHERE user_id = $user_id
                                    AND title LIKE '%".
                                    $this->db->escape_str($search_string)."%' 
                                    UNION 
                                    SELECT c.cloudscape_id, title FROM 
                                    cloudscape_poster cp
                                    INNER JOIN cloudscape c
                                    ON c.cloudscape_id = cp.cloudscape_id
                                    WHERE cp.user_id = $user_id
                                    AND title LIKE '%".
                                    $this->db->escape_str($search_string)."%' 
                                    UNION
                                    SELECT c.cloudscape_id, title FROM 
                                    cloudscape_admin ca
                                    INNER JOIN cloudscape c
                                    ON c.cloudscape_id = ca.cloudscape_id
                                    WHERE ca.user_id = $user_id
                                    AND title LIKE '%".
                                    $this->db->escape_str($search_string)."%' 
                                    UNION
                        
                                    SELECT cloudscape_id, title
                                    FROM cloudscape WHERE open = 1 AND title LIKE '%".
                                    $this->db->escape_str($search_string)."%' 
                                    ORDER BY title");
        return $query->result();
    }

    /** 
     * Update the filename of a Cloudscape picture.
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param string $filename The new filename
     */
    function update_picture($cloudscape_id, $data) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->update('cloudscape', array('image_path' => $data['image_path'],
            'image_attr_name'=>$data['image_attr_name'], 
            'image_attr_link'=>$data['image_attr_link']));
    }
    
/******************************************************************************************
 * SECTIONS
 * 
 * Sections can be created for a cloudscape and clouds that belong to the cloudscape added 
 * to sections. A cloud may belong to more than one section. 
 ******************************************************************************************/
    
    /**
     * Create a section
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param string $title The title of the section
     * @return integer The ID of the new section
     */
    function create_section($cloudscape_id, $title) {
        $section->cloudscape_id = $cloudscape_id;
        $section->title         = $title;
        $this->db->insert('cloudscape_section', $section);
        $section_id =  $this->db->insert_id(); 
        return $section_id;
    }
    
    /**
     * Delete a section. 
     *
     * @param integer $section_id The ID of the section
     */
    function delete_section($section_id) {
        // Remove the entry for the section in the cloudscape_section table
        $this->db->where('section_id', $section_id);
        $this->db->delete('cloudscape_section');
        
        // Remove any entries for the section in the section_cloud table
        $this->db->where('section_id', $section_id);
        $this->db->delete('section_cloud');        
    }
    
    /**
     * Rename a section 
     *
     * @param integer $section_id The ID of the section
     * @param string $title The new section title
     */
    function rename_section($section_id, $title) {
        $this->db->where('section_id', $section_id);
        $this->db->update('cloudscape_section', array('title'=>$title));
    }
    
    /**
     * Add a cloud to a section 
     * Note: currently not checking if the cloud belongs to the cloudscape that the section
     * belongs to
     *
     * @param integer $section_id The ID of the section
     * @param integer $cloud_id The ID of the cloud
     */
    function add_cloud_to_section($section_id, $cloud_id) {
        $section_cloud->section_id = $section_id;
        $section_cloud->cloud_id   = $cloud_id;
        $this->db->insert('section_cloud', $section_cloud);
    }
    
    /**
     * Remove a cloud from a section
     *
     * @param integer $section_id The ID of the section
     * @param integer $cloud_id The ID of the cloud
     */
    function remove_cloud_from_section($section_id, $cloud_id) {
        $this->db->where('section_id', $section_id);
        $this->db->where('cloud_id', $cloud_id);
        $this->db->delete('section_cloud');  
    }
    
    /**
     * Get all the sections of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return array Array of sections
     */
    function get_sections($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $query = $this->db->get('cloudscape_section');
        return $query->result();
    }
    
    /**
     * Get all the clouds in a section
     *
     * @param integer $section_id The ID of the section
     * @return array Array of clouds
     */
    function get_clouds_in_section($section_id) {
        $this->db->order_by('title', 'asc');
        $this->db->where('section_id', $section_id);
        $this->db->where('user.banned',0);
        // Ensure that the `cloud` table is joined before the ON `cloud`.user_id... part.
        $this->db->join('cloud', 'cloud.cloud_id = section_cloud.cloud_id');
        $this->db->join('user', 'cloud.user_id = user.id');
        $query = $this->db->get('section_cloud');
        return $query->result();
    }
    
    /**
     * Determine if a section belongs to a specified cloudscape 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $section_id The ID of the section
     * @return boolean TRUE if the section belongs to the cloudscape, FALSE otherwise
     */
    function section_belongs_to_cloudscape($cloudscape_id, $section_id) {
        $belongs = false;
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('section_id', $section_id);
        $query = $this->db->get('cloudscape_section');
        if ($query->num_rows() > 0) {
            $belongs = true;
        }
        return $belongs;   
    }
    
    /**
     * Determine if a cloud belongs to a specified section
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $section_id The ID of the section
     * @return boolean TRUE if the cloud belongs to the section, FALSE otherwise
     */
    function is_cloud_in_section($cloud_id, $section_id) {
        $in_section = false;
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('section_id', $section_id);
        $query = $this->db->get('section_cloud');
        if ($query->num_rows() > 0) {
            $in_section = true;
        }
        return $in_section;
    }
    
    /**
     * Get a specified section 
     * @param integer $section_id The ID of the section
     * @return object Details of the section
     */
    function get_section($section_id) {
        $this->db->where('section_id', $section_id);
        $query = $this->db->get('cloudscape_section');
        $result = $query->result();
        return $result[0];  
    }
    
    /**
     * Get most recently viewed cloudscapes by a user
     *
     * @param integer $user_id The ID of the user
     * @param integer $num The number of cloudscapes to get
     */
    function get_recently_viewed_cloudscapes($user_id, $num) {
        $user_id = (int) $user_id; // Make sure we're passing in an integer even though 
                                   // only set internally
        $num = (int) $num;          // Ditto 
        $query = $this->db->query("SELECT DISTINCT c.cloudscape_id, c.title
        FROM logs l INNER JOIN cloudscape c ON c.cloudscape_id = l.item_id
        WHERE l.item_type ='cloudscape' AND l.user_id = $user_id ORDER BY timestamp desc LIMIT $num");
        return $query->result();
    }
    
    /**
     * Get the total number of comments on all the clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of comments 
     */
    function get_total_comments($cloudscape_id) {
        $cloudscape_id = (int) $cloudscape_id; 
        $query = $this->db->query("SELECT * FROM comment co
                                   INNER JOIN cloudscape_cloud cc 
                                   ON cc.cloud_id = co.cloud_id
                                   WHERE cc.cloudscape_id = $cloudscape_id");        
        
        return $query->num_rows();
    }
    
    /**
     * Get the total number of links on all the clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of links
     */
    function get_total_links($cloudscape_id) {
        $cloudscape_id = (int) $cloudscape_id; 
        $query = $this->db->query("SELECT * FROM cloud_link cl
                                   INNER JOIN cloudscape_cloud cc 
                                   ON cc.cloud_id = cl.cloud_id
                                   WHERE cc.cloudscape_id = $cloudscape_id");        
        
        return $query->num_rows();
    }  
    
    /**
     * Get the total number of references on all the clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The total number of references
     */
    function get_total_references($cloudscape_id) {
        $cloudscape_id = (int) $cloudscape_id; 
        $query = $this->db->query("SELECT * FROM cloud_reference cr
                                   INNER JOIN cloudscape_cloud cc 
                                   ON cc.cloud_id = cr.cloud_id
                                   WHERE cc.cloudscape_id = $cloudscape_id");        
        
        return $query->num_rows();
    }    

    /**
     * Get the total number of embeds on all the clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of embeds
     */
    function get_total_embeds($cloudscape_id) {
        $cloudscape_id = (int) $cloudscape_id; 
        $query = $this->db->query("SELECT * FROM cloud_embed ce
                                   INNER JOIN cloudscape_cloud cc 
                                   ON cc.cloud_id = ce.cloud_id
                                   WHERE cc.cloudscape_id = $cloudscape_id");        
        
        return $query->num_rows();
    }    

    /**
     * Get the total number of extra content items on all the clouds in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of extra content items
     */
     function get_total_content($cloudscape_id) {
        $cloudscape_id = (int) $cloudscape_id; 
        $query = $this->db->query("SELECT * FROM cloud_content cco
                                   INNER JOIN cloudscape_cloud cc 
                                   ON cc.cloud_id = cco.cloud_id
                                   WHERE cc.cloudscape_id = $cloudscape_id");        
        
        return $query->num_rows();
    }     
    
    /**
     * Get the total number of distinct users who have commented on a cloud in a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of commenters
     */
    function get_total_commenters($cloudscape_id) {
        $cloudscape_id = (int) $cloudscape_id; 
        $query = $this->db->query("SELECT DISTINCT(co.user_id) FROM comment co
                                   INNER JOIN cloudscape_cloud cc 
                                   ON cc.cloud_id = co.cloud_id
                                   WHERE cc.cloudscape_id = $cloudscape_id");  
        return $query->num_rows();
    }
    
   /**
     * Get the most popular cloudscapes on the site 
     *
     * @param integer $limit Limit to number of cloudscapes to reture
     * @return array Array of cloudscapes
     */
    function get_popular_cloudscapes($limit  = 10) {
        $query = $this->db->query("SELECT c.cloudscape_id, c.title 
                                   FROM cloudscape_popular cp
                                   INNER JOIN cloudscape c 
                                   ON c.cloudscape_id = cp.cloudscape_id
                                   ORDER BY RAND() LIMIT $limit ", $limit );
        return $query->result();
    }     
    
    /**
     * Recache the most popular cloudscapes on the site in the cloudscape_popular table
     */
    function repopulate_popular_cloudscapes() {
        // Clear the popular cloudscapes table
        $query = $this->db->query("DELETE FROM cloudscape_popular");
        
        // Calculate the most popular cloudscapes
        $cloudscapes = $this->calculate_popular_cloudscapes();
        
        // Repopular popular cloudscapes table  
        
        foreach ($cloudscapes as $cloudscape) {
            $this->db->insert('cloudscape_popular', 
                              array('cloudscape_id'=>$cloudscape->cloudscape_id));
        }     
    }
    
    /**
     * Calculate the most populate cloudscapes on the site. 
     * At the moment this algorithm is very simplistic, but can make more sophisticated 
     * eventually
     *
     * @return array Array of cloudscape_ids 
     */
    function calculate_popular_cloudscapes($limit = 30) {
        $days = $this->config->item('popular_cloudscapes_days') ? 
                    $this->config->item('popular_cloudscapes_days') : 10;
        $since = time() - 60*60*24* $days;
        $query = $this->db->query("SELECT c.cloudscape_id, COUNT(*) AS total_favourites
                  FROM cloudscape c INNER JOIN favourite f ON f.item_id = c.cloudscape_id 
                  AND f.item_type = 'cloudscape'
                  WHERE f.timestamp > $since
                    GROUP BY c.cloudscape_id ORDER BY total_favourites DESC LIMIT $limit"); 
       return $query ->result();      
    }    
}