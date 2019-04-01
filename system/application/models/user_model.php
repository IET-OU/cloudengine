<?php
/**
 * Functions related to users and user profiles (but not user authentications)
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */
class User_model extends Model {

    function __construct()
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
    function get_users_alpha($alpha = 'A', $only_active = TRUE) {
        // Need to check alphabetical
        if (strlen($alpha) != 1) {
            $alpha = 'A';
        }
        // The following should execute the following query:
        // SELECT id, fullname, institution FROM user_profile
        // WHERE fullname LIKE '$alpha%' ORDER BY fullname ASC
        if($only_active) {
          $this->where_active();
        }
        $this->db->like('fullname',$alpha,'after');
        $this->db->select('user.id, fullname, institution, banned, deleted, email, user_name');
        $this->db->order_by('fullname','asc');
   	    $this->db->join('user', 'user_profile.id=user.id');
        $query = $this->db->get('user_profile');

        return $query->result();
    }

    /**
     * Get all admin users
     * @return array Array of the admin users
     */
    function get_admins() {
        $this->db->where('role', 'admin');
        $query = $this->db->get('user');
        return $query->result();
    }

   /**
	 * Get the details of a user given their username
	 *
	 * @param string $user_name
	 * @return object The details of the user
	 */
	function get_user_by_username($user_name) {
	    $this->where_active();
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
	function get_user($user_id, $only_active = TRUE) {
		$user = FALSE;
		if (!is_numeric($user_id)) {
		    return $this->get_user_by_username($user_id);
		}
        if($only_active) {
          $this->where_active();
        }
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
	 * Get the full name of a user given the user's ID.
	 *
	 * @param integer $user_id The ID of the user
	 * @return object The details of the user
	 */
	function get_user_full_name($user_id) {
		  $user = FALSE;
        $this->db->select('fullname');
        $this->db->where('user.id', $user_id);
        $this->db->join('user', 'user.id = user_profile.id');
        $query = $this->db->get('user_profile');
        if ($query->num_rows() !=  0 ) {
            $user = $query->row();
        }
	    return $user->fullname;
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
            $num = (int) $num;
            $num ="LIMIT $num";
        }

        $user_id = (int) $user_id;

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

    function get_comments($user_id) {
        $user_id = (int) $user_id;

        $query = $this->db->query("SELECT * FROM  comment
                                   WHERE comment.user_id = $user_id");
        return $query->result();
    }

    function get_clouds_with_contributions($user_id) {
        $user_id = (int) $user_id;

        $query = $this->db->query("SELECT cl.title AS title, cl.body AS body,
                                    cl.cloud_id AS cloud_id
                                    FROM  cloud cl
                                    WHERE cl.user_id = $user_id AND cl.moderate = 0
                                    UNION
                                    SELECT cl.title AS title, cl.body AS body,
                                    cl.cloud_id AS cloud_id
                                    FROM  cloud cl
                                    INNER JOIN COMMENT co
                                    ON co.cloud_id = cl.cloud_id
                                    WHERE co.user_id = $user_id AND co.moderate = 0
                                    UNION
                                    SELECT cl.title AS title, cl.body AS body,
                                    cl.cloud_id AS cloud_id
                                    FROM  cloud cl
                                    INNER JOIN cloud_content cc
                                    ON cc.cloud_id = cl.cloud_id
                                    WHERE cc.user_id = $user_id AND cc.moderate = 0
                                    UNION
                                    SELECT cl.title AS title, cl.body AS body,
                                    cl.cloud_id AS cloud_id
                                    FROM  cloud cl
                                    INNER JOIN cloud_embed ce
                                    ON ce.cloud_id = cl.cloud_id
                                    WHERE ce.user_id = $user_id AND ce.moderate = 0
                                    UNION
                                    SELECT cl.title AS title, cl.body AS body,
                                    cl.cloud_id AS cloud_id
                                    FROM  cloud cl
                                    INNER JOIN cloud_reference cr
                                    ON cr.cloud_id = cl.cloud_id
                                    WHERE cr.user_id = $user_id AND cr.moderate = 0
                                    UNION
                                    SELECT cl.title AS title, cl.body AS body,
                                    cl.cloud_id AS cloud_id
                                    FROM  cloud cl
                                    INNER JOIN cloud_link cli
                                    ON cli.cloud_id = cl.cloud_id
                                    WHERE cli.user_id = $user_id AND cli.moderate = 0 ");


        return $query->result();
    }

    /**
     * Update the profile for a user
     *
     * @param object $user The details to update
     */
	function update_profile($user) {
        $user_id       = $user->id;
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
        $user_to_update->do_not_use_editor      = $user->do_not_use_editor;
        if ($this->config->item('x_message')) {
            $user_to_update->email_message_notify = $user->email_message_notify;
        }

        $this->db->update('user_profile', $user_to_update, array('id'=>$user_id));
        $this->update_in_search_index($user_id);

        // Add an event to the admin cloudstream
        $this->CI->event_model->add_event('admin', 0, 'profile_edit', $user_id);
    }

    /**
     * Approve a profile under moderation
     *
     * @param integer $user_id The ID of the user
     */
    function approve_profile($user_id) {
        $this->db->where('id', $user_id);
        $this->db->update('user_profile', array('moderate'=>0));
    }

   /**
    * Delete a profile
    */
    function delete_profile($user_id) {
       $this->db->where('id', $user_id);
       $this->db->update('user_profile', array('deleted' => 1, 'moderate'=>0));
    }

    /**
     * Get all profiles requiring moderation
     *
     * @return array Array of profiles
     */
    function get_profiles_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->where('user.banned', 0);
        $this->db->join('user', 'user.id = user_profile.id');
        $query = $this->db->get('user_profile');
        return $query->result();
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
	 * Mark a user as deleted
	 *
	 * @param integer $user_id The ID of the user
	 */
    function delete($user_id) {
       $this->db->where('id', $user_id);
       $this->db->update('user_profile', array('deleted' => 1));
    }

	/**
	 * Unmark a user as deleted
	 *
	 * @param integer $user_id The ID of the user
	 */
    function undelete($user_id) {
       $this->db->where('id', $user_id);
       $this->db->update('user_profile', array('deleted' => 0));
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

        $query = $this->db->query("SELECT p.institution, COUNT(*) AS total_users
                                   FROM user_profile AS p
                                   JOIN user AS u ON u.id = p.id
                                   WHERE p.institution LIKE '$alpha%'
                                   AND u.banned = 0
                                   GROUP BY TRIM(p.institution) ORDER BY p.institution ASC");
        return $query->result();
    }

    /** Get suggested institutions.
     * @param string $query Part of an institution name
     * @return array Array of the institution names, with total users
     */
    public function suggest_institutions($query, $limit = 5) {
        $query = $this->db->escape_str($query);
        $limit = (int) $limit;
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
        /*
        SELECT p.id,p.full_name,p.institition
          FROM user_profile AS p
          JOIN user AS u ON u.id = p.id
          WHERE p.institution = 'matressman'
            AND u.banned = 0
          ORDER BY p.full_name;
        */
        $this->db->join('user', 'user.id = user_profile.id');
        $this->db->order_by('fullname');

        $this->db->where('institution', $institution);
        $this->db->where('banned', 0);
        $query = $this->db->get('user_profile');
        $result = $query->result();

        return $result ?: null;
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
        $startdate = (int) $startdate;
        $enddate   = (int) $enddate;
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

	/**
	 * Mark a user as banned
	 *
	 * @param integer $user_id The ID of the user
	 */
    function ban($user_id) {
       $this->db->where('id', $user_id);
       $this->db->update('user', array('banned' => 1));
    }

	/**
	 * Mark a user as unbanned
	 *
	 * @param integer $user_id The ID of the user
	 */
    function unban($user_id) {
       $this->db->where('id', $user_id);
       $this->db->update('user', array('banned' => 0));
    }

    /** Add database check for users who are 'deleted' or not active.
    */
    protected function where_active() {
        $this->db->where('user_profile.moderate', 0);
        $this->db->where('user_profile.deleted', 0);
        $this->db->where('user.banned', 0);
    }

    /**Get array of random users, filtered by existing CSV file(s).
     * @return array
     */
    public function get_random_users($target=300) {
    //SELECT user.id,user_name,email,fullname FROM user JOIN user_profile ON user_profile.id=user.id WHERE banned=0 ORDER BY RAND() LIMIT 20;
        $this->where_active();
        $this->db->select('user.id,user_name,email, fullname');
        $this->db->order_by('RAND()');
        $this->db->limit($target+200);
   	    $this->db->join('user_profile', 'user_profile.id=user.id');
        $query = $this->db->get('user');

        $rand_users = $query->result();

        // Read in existing CSV file(s).
        $csv = $this->_read_csv($this->config->item('data_dir').'tmp/cloudworks_users_300_rand.csv');

        //Filter existing CSV users from query result.
        $removed = array();
        foreach ($rand_users as $key => $user) {
            $email = $user->email;
            if (in_array($email, $csv)) {
                $removed[] = $user;
                unset($rand_users[$key]);
            }
        }
        echo "(Filtered:".count($removed)."),".PHP_EOL;

        return array_slice($rand_users, 0, $target);
    }

    function get_unactivated_users() {
       $this->db->order_by('created', 'desc');
        $query = $this->db->get('user_temp');
        return $query->result();
    }

    protected function _read_csv($csv_file) {
        $csv = array();
        if (($handle = fopen($csv_file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $csv[] = $data[0];
                //$csv[$data[0]] = array('email'=>$data[0], 'fullname'=>$data[1]);
            }
            fclose($handle);
        } else {
            show_error("Error reading $csv_file.");
        }
        return $csv;
    }
}
