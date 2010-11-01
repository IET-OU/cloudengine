<?php
/**
 * Functions related to users and user profiles (but not user authentications)
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */
class User_model extends Model {
	
    function User_model()
    {     
        parent::Model();
    }
	
	/**
	 * Get the details of all activated users
	 *
	 * @return array Array of the user details of all users of the site
	 */
	function get_users() {
		$query = $this->db->get('user');
		return $query->result();
	}
	
    /**
     * Get all the users starting with a specified letter of the alphabet
     *
     * @param string $alpha The letter of the alphabete
     * @return array Array of the users
     */
    function get_users_alpha($alpha = 'A') {
        // Need to check alphabetical
        if (strlen($alpha) != 1) {
            $alpha = 'A';
        }

        $query = $this->db->query("SELECT id, fullname, institution FROM user_profile 
                                   WHERE fullname LIKE '$alpha%' ORDER BY fullname ASC");
        return $query->result();
    }
    
   /**
	 * Get the details of a user given their username
	 *
	 * @param string $user_name
	 * @return object The details of the user
	 */
	function get_user_by_username($user_name) {
	    $this->db->where('user.user_name', $user_name);
	    $this->db->join('user_profile', 'user_profile.id=user.id');
	    $query = $this->db->get('user');
	    $result = $query->result();
	    $user = $result[0];
	    return $user; 
	}
	
	/**
	 * Get the details of a user given the user's ID. 
	 *
	 * @param integer $user_id The ID of the user
	 * @return object The details of the user
	 */
	function get_user($user_id) {
		$user = FALSE;
        $this->db->where('user.id', $user_id);
        $this->db->join('user', 'user.id = user_profile.id');
	    $query = $this->db->get('user_profile');
        if ($query->num_rows() !=  0 ) {
            $user = $query->row();
            $user->user_id = $user->id;
            $this->CI =& get_instance();
            $this->CI->load->model('tag_model');
    	    $user->tags = $this->CI->tag_model->get_tags('user', $user_id);
        }
	    return $user;
	}	
	    
    /**
     * Search the list of users' full name for a query string and return the details of 
     * any users that match that string
     *
     * @param string $query_string The query string
     * @return array Array of user details matching the query string
     */
    function search($query_string) {
        // Need to check the query string
        $this->db->like('fullname', $query_string);
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 'left');
       
        $query = $this->db->get('user_profile');
        return $query->result();        
    }
        
    /**
     * Get a user's e-mail address
     *
	 * @param integer $user_id The ID of the user
     * @return string The e-mail address of the user
     */
    function get_email($user_id) {
        $this->db->where('user.id', $user_id);
        $query = $this->db->get('user');
        if ($query->num_rows() !=  0 ) {
            $user = $query->row();
        }
        
        return $user->email;         
    }	

    /**
     * Get the details of whether a user wants to be e-mailed with follow-up comments
     *
	 * @param integer $user_id The ID of the user
     * @return boolean TRUE if they have specified that they would like to be 
     * e-mailed follow-up comments, FALSE otherwise
     */
    function email_comment($user_id) {
        $this->db->where('id', $user_id);
        $query = $this->db->get('user_profile');
        if ($query->num_rows() !=  0 ) {
            $profile = $query->row();
        }
        
        return $profile->email_comment;        
    }
    
    /**
     * Get the filename of a user's picture
     *
	 * @param integer $user_id The ID of the user
     * @return string The filename of the picture
     */
    function get_picture($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->select('picture');
        $query = $this->db->get('user_picture');
        $result = $query->result();
        return isset($result[0]->picture) ? $result[0]->picture : null;
    }    
     
    /**
     * Get number of clouds owned by a user
     *
	 * @param integer $user_id The ID of the user
     * @return integer - the number of clouds
     */
    function get_cloud_total($user_id) {
         $this->db->where('user_id', $user_id);
         $this->db->from('cloud');
         $cloud_total = $this->db->count_all_results();
         return $cloud_total;
    }
    
    /**
     * Get the clouds for a user 
     *
	 * @param integer $user_id The ID of the user
     * @param  integer $num The number of clouds to get
     * @return array The clouds that belong to the user
     */
    function get_clouds($user_id, $num = false) {
        if ($num) {
            $num ="LIMIT $num";
        }
         $query = $this->db->query("SELECT cl.title as title, cl.body as body, 
                                    cl.created AS timestamp, 
                                   cl.cloud_id AS cloud_id, 
                                   COUNT(co.comment_id) AS total_comments 
                                   FROM  cloud cl 
                                   LEFT OUTER JOIN comment co ON cl.cloud_id = co.cloud_id 
                                   WHERE cl.user_id = $user_id AND cl.moderate = 0 
                                   GROUP BY cl.cloud_id ORDER BY title ASC $num");


        return $query->result();
    }  

    /**
     * Update the profile for a user
     *
     * @param object $user The details to update
     */
	function update_profile($user) {
        $user_id       = $user->id;
        $this->CI =& get_instance();
        $user_to_update->id                     = $user_id;
        $user_to_update->fullname               = $user->fullname;
        $user_to_update->institution            = $user->institution;
        $user_to_update->description            = $user->description;
        $user_to_update->twitter_username       = $user->twitter_username;
        $user_to_update->homepage               = $user->homepage;
        $user_to_update->department             = $user->department;
        $user_to_update->email_follow           = $user->email_follow;
        $user_to_update->email_comment          = $user->email_comment;
        $user_to_update->email_comment_followup = $user->email_comment_followup;
        $user_to_update->email_events_attending = $user->email_events_attending;
        $user_to_update->email_news             = $user->email_news;
        $user_to_update->display_email          = $user->display_email;
        $user_to_update->whitelist              = $user->whitelist;
        $user_to_update->do_not_use_editor     = $user->do_not_use_editor;
        
        $this->db->update('user_profile', $user_to_update, array('id'=>$user_id)); 
        $this->update_in_search_index($user_id);
	}   

	/**
	 * Mark a user as whitelisted (for purposes of moderation/spam)
	 *
	 * @param integer $user_id The ID of the user
	 */
    function whitelist($user_id) {
       $this->db->where('id', $user_id);
       $this->db->update('user_profile', array('whitelist' => 1));    
    }

    /**
     * Update the filename of a users picture
     *
	 * @param integer $user_id The ID of the user
     * @param string $filename The filename of the picture
     */
    function update_picture($user_id, $filename) {

        if ($this->get_picture($user_id)) {
            $this->db->where('user_id', $user_id);
            $this->db->update('user_picture', array('picture' => $filename)); 
        } else {
            $this->db->set('user_id', $user_id);
            $this->db->set('picture', $filename);
            $this->db->insert('user_picture');
        }
    }    

    /***************************************************************************************
     * FOLLOWING
     * *************************************************************************************/
         
    /**
     * Get the cloudscapes that a user is following
     *
	 * @param integer $user_id The ID of the user
     * @return array Array of the cloudscapes followed by the user
     */
    function get_following_cloudscapes($user_id) {
        $this->db->order_by('title',  'asc');
        $this->db->from('cloudscape');
        $this->db->where('cloudscape_followed.user_id', $user_id);
        $this->db->join('cloudscape_followed', 
                        'cloudscape.cloudscape_id = cloudscape_followed.cloudscape_id');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get the clouds that a user is following
     *
	 * @param integer $user_id The ID of the user
     * @return array Array of the clouds followed by the user
     */
    function get_following_clouds($user_id) {
        $this->db->order_by('title',  'asc');
        $this->db->from('cloud');
        $this->db->where('cloud_followed.user_id', $user_id);
        $this->db->join('cloud_followed', 'cloud.cloud_id = cloud_followed.cloud_id');
        $query = $this->db->get();
        return $query->result();
    }    

    /**
     * Get the total number of cloudscapes that a user is following
     *
	 * @param integer $user_id The ID of the user
     * @return integer The number of clouds
     */
    function get_cloudscape_total($user_id) {
        $this->db->from('cloudscape');
        $this->db->where('cloudscape_followed.user_id', $user_id);
        $this->db->join('cloudscape_followed', 
                        'cloudscape.cloudscape_id = cloudscape_followed.cloudscape_id');
        $cloudscape_total = $this->db->count_all_results();
        return $cloudscape_total;        
    }    
    
    /**
     * Get the users that a user if following
     *
	 * @param integer $user_id The ID of the user
     * @return array Array of the users followed by the user
     */
    function get_following($user_id) {
        $this->db->from('user_profile');
        $this->db->where('user_followed.following_user_id', $user_id);
        $this->db->join('user_followed', 
                       'user_profile.id = user_followed.followed_user_id');
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 'left');
        $query = $this->db->get();
        return $query->result();        
    }
    
    /**
     * Get the followers for a user
     *
	 * @param integer $user_id The ID of the user
     * @return array Array of the users following the user
     */
    function get_followers($user_id) {
        $this->db->from('user_profile');
        $this->db->where('user_followed.followed_user_id', $user_id);
        $this->db->join('user_followed', 
                        'user_profile.id = user_followed.following_user_id');
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 'left');
        $query = $this->db->get();
        return $query->result();         
    }
    
    /**
     * Determine if one user is following another
     *
     * @param integer $followed_user_id The user ID of the user being followed
     * @param integer $following_user_id The user ID of the possible follower
     * @return boolean TRUE if the follower is following the first user specified, 
     * FALSE otherwise
     */
    function is_following($followed_user_id, $following_user_id) {             
        $following = false;
        $this->db->where('followed_user_id', $followed_user_id);
        $this->db->where('following_user_id', $following_user_id);
        $query = $this->db->get('user_followed');
        
        if ($query->num_rows() > 0) {
            $following = true;
        }
        
        return $following;
    }
    
    /**
     * Add a user as as follower of another
     *
     * @param integer $followed_user_id The user ID of the user being followed
     * @param integer $following_user_id The user ID of the follower to add
     */
    function follow($followed_user_id, $following_user_id) {
        if (!$this->is_following($followed_user_id, $following_user_id)) {
            $this->db->set('followed_user_id', $followed_user_id);
            $this->db->set('following_user_id', $following_user_id);
            $this->db->set('timestamp', time());
            $this->db->insert('user_followed');
        }  
    }
    
    /**
     * Remove a user as a follower of another
     *
     * @param integer $followed_user_id The user ID of the user being followed
     * @param integer $following_user_id The user ID of the follower to remove
     */
    function unfollow($followed_user_id, $following_user_id) {
        $this->db->where('followed_user_id', $followed_user_id);
        $this->db->where('following_user_id', $following_user_id);
        $this->db->delete('user_followed'); 
    }    
   
    /***************************************************************************************
     * FIND PEOPLE AND INSTITIONS
     * *************************************************************************************/

    /**
     * Get all the institutions starting with a specified letter
     *
     * @param string $alpha The letter of the alphabet
     * @return array Array of the institutions starting with that letter
     */
    function get_institutions($alpha = 'A') {
        // Need to check alphabetical
        if (strlen($alpha) != 1) {
            $alpha = 'A';
        }
        
        $query = $this->db->query("SELECT institution, COUNT(*) AS total_users 
                                   FROM user_profile WHERE institution LIKE '$alpha%' 
                                   GROUP BY TRIM(institution) ORDER BY institution ASC");
        return $query->result();
    }

    /** Get suggested institutions.
     * @param string $query Part of an institution name
     * @return array Array of the institution names, with total users
     */
    public function suggest_institutions($query, $limit = 5) {
        $query = $this->db->query("SELECT
          institution AS name, COUNT(*) AS total
          FROM user_profile
          WHERE institution LIKE '%$query%' 
          GROUP BY TRIM(name) ORDER BY total DESC LIMIT $limit");
          return $query->result();
    }

    /**
     * Get all the users in an institutions
     *
     * @param stringe $institution The name of the institution
     * @return array Array of the users who have specified that they belong to the
     * institution
     */
    function get_users_in_institution($institution) {
        $this->db->order_by('fullname');
        $this->db->where('institution', $institution);
        $query = $this->db->get('user_profile');
        return $query->result();
    }

    /***************************************************************************************
     * STATISTICS
     * *************************************************************************************/

    /**
     * Get the total number of activated users on the site
     *
     * @return integer The total number of users 
     */
    function get_total_users() {
        $query = $this->db->get('user');
        return $query->num_rows();
    }  	    
    
    /**
     * Get the number of users registered between two dates 
     *
     * @param integer $startdate The start date as a unix timestamp
     * @param integer $enddate The end date as a unix time stamp
     * @return integer The number of users registered
     */
    function get_users_registered($startdate, $enddate) {
        $this->db->query("SELECT * FROM user WHERE created >= $startdate 
                          AND created < $enddate");
        return $query->num_rows();
    }
    
    /***************************************************************************************
     * SEARCH
     * *************************************************************************************/
           
    /**
     * Update the entry for a user in the search index
     *
	 * @param integer $user_id The ID of the user
     */
    function update_in_search_index($user_id) {
    	if (config_item('x_search')) {
        $this->CI=& get_instance();
        $this->CI->load->model('search_model');
		$this->CI->search_model->update_item_in_index(base_url().'user/view/'.$user_id, 
		                                              $user_id, 'user');    
    	}
     }
    
    /**
     * Remove a user from the search index
     *
	 * @param integer $user_id The ID of the user
     */
    function remove_from_search_index($user_id) {
    	if (config_item('x_search')) {
        	$this->CI=& get_instance();
	        $this->CI->load->model('search_model');
			$this->CI->search_model->delete_item_from_index($user_id, 'user');
    	}
    }
}