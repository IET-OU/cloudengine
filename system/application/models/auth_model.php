<?php
/**
 * Model file for authentication-related functions
 * @copyright 2009, 2010, 2012 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Authentication
 */
class Auth_model extends Model {

    function __construct() {
        parent::Model();
    }

    /**
     * Check if a username exists
     *
     * @param string $username The user name to check
     * @param boolean $check_inactive If TRUE, also check the temporary users awaiting
     * activation as well as active users
     * @return boolean TRUE if a user already exists with the username, false otherwise
     */
    function username_exists($username, $check_inactive = FALSE) {
    	// Check activated users
    	$exists = FALSE;
    	$this->db->where('user_name', $username);
    	$query = $this->db->get('user');
    	if ($query->num_rows() > 0) {
    		$exists = TRUE;
    	}

    	// Check temporary inactivated users if specified
    	if ($check_inactive) {
	    	$this->db->where('user_name', $username);
	    	$query = $this->db->get('user_temp');
	    	if ($query->num_rows() > 0) {
	    		$exists = TRUE;
	    	}
    	}

    	return $exists;
    }

    /**
     * Check if an email address already exists among both activated and non-activated users
     *
     * @param string $email The email address to check
     * @param boolean $check_inactive If TRUE, also check the temporary users awaiting
     * activation as well as active users
     * @return boolean TRUE if a user already exists with the email address, false otherwise
     */
    function email_exists($email, $check_inactive = FALSE) {
    	// Check activated users
    	$exists = FALSE;
    	$this->db->where('email', $email);
    	$query = $this->db->get('user');
    	if ($query->num_rows() > 0) {
    		$exists = TRUE;
    	}

    	// Check temporary inactivated users if specified
    	if ($check_inactive) {
	    	$this->db->where('email', $email);
	    	$query = $this->db->get('user_temp');
	    	if ($query->num_rows() > 0) {
	    		$exists = TRUE;
	    	}
    	}

    	return $exists;
    }

    /**
     * Get the temporary user with a given temporary user id and activation code
     *
     * @param integer $temp_user_id The temporary user id
     * @param string $activation_code The activation code
     * @return object The user, or FALSE if no such temporary user exists.
     */
    function _get_temp_user($temp_user_id, $activation_code) {
    	$user = FALSE;
    	$this->db->where('id', $temp_user_id);
    	$this->db->where('activation_code', $activation_code);
    	$query = $this->db->get('user_temp');

    	if ($query->num_rows() != 0) {
    		$user = $query->row();
    	}
    	return $user;
    }

    /**
     * Activate a temporary user
     *
     * @param integer $temp_user_id The temporary id for the user
     * @param string $activation_code The activation code for the user
     * @return integer The new (permanent) id of the user if they were activated
     * successfully, FALSE if the activation was unsuccessful.
     */
    function activate_user($temp_user_id, $activation_code) {
    	$user_id = FALSE;
    	$user = $this->_get_temp_user($temp_user_id, $activation_code);
    	// Add the temporary user as a full activated user and delete the
    	// temporary user
	    if ($user) {
	    	$user_id = $this->_insert_user($user);
	    	if ($user_id) {
		    	$this->_delete_temp_user($temp_user_id);
	    	}
	    }

	    return $user_id;
    }

    /**
     * Insert a temporary user awaiting activation
     *
     * @param object $user The details of the temporary user
     * @return integer The temporary id for the temporary user
     */
	function insert_temp_user($user) {
    	$this->db->set($user);
    	$this->db->insert('user_temp');
    	return $this->db->insert_id();
	}

    /**
     * Remove a temporary user
     *
     * @param integer $temp_user_id The ID of the temporary user
     */
    function _delete_temp_user($temp_user_id) {
    	$this->db->where('id', $temp_user_id);
    	$this->db->delete('user_temp');
    }

    /**
     * Remove any temporary users whose temporary user information was created longer
     * ago than the expiration time set in the config file.
     *
     */
    function clean_expired_temp_users() {
    	$expiration_time = config_item('expire_temp_users_time');
    	$expiry_timestamp = time() - $expiration_time;
    	$this->db->where("created < $expiry_timestamp");
    	$this->db->delete('user_temp');
    }

    /**
     * Gets the details of a user given the id of the user
     *
     * @param integer $user_id The id of the user
     * @return object The details of the user, or FALSE if no user with that id exists.
     */
    function get_user($user_id) {
    	$user = FALSE;
		$this->db->where('user.id', $user_id);
    	$this->db->join('user_profile', 'user_profile.id = user.id');
		$query = $this->db->get('user');
		if ($query->num_rows() > 0) {
			$user = $query->row();
		}

		return $user;
	}

	/**
	 * Gets a user's details given the username of the user
	 *
	 * @param string $username The username
     * @return object The details of the user, or FALSE if no user with that id exists.
	 */
	function get_user_by_username($username) {
		$user = FALSE;
		$this->db->where('user_name', $username);
    	$this->db->join('user_profile', 'user_profile.id = user.id');
		$query = $this->db->get('user');
		if ($query->num_rows() > 0) {
			$user = $query->row();
		}

		return $user;
	}

    /**
     * Get a user with a specified e-mail address
     *
     * @param string $email The e-mail address
     * @return object The details of the user, or FALSE if no user exists with that
     * e-mail address
     */
    function get_user_by_email($email) {
    	$user = FALSE;
    	$this->db->where('email', $email);
    	$this->db->join('user_profile', 'user_profile.id = user.id');
    	$query = $this->db->get('user');
    	if ($query->num_rows() > 0) {
    		$user = $query->row();
    	}
    	return $user;;
    }

    /**
     * Add a new user - should only be used to move a user from the temporary users to
     * the full users.
     *
     * @param object $user Object containing the details of the user to insert
     */
    function _insert_user($user_data) {
    	 // We should probably do some validation here, but as have done it elsewhere
    	 // before calling this function is ok for the moment

    	 // Add the data as appropriate to the user and user_profile tables
        $user->user_name  = $user_data->user_name;
    	$user->country_id = $user_data->country_id;
    	$user->password   = $user_data->password;
    	$user->email      = $user_data->email;
    	//Bug #69, 'user' table is a historic exception - it uses MySQL datetime/timestamp.
    	$user->created    = date('Y-m-d H:i:s');
    	$this->db->set($user);
    	$this->db->insert('user');

    	$profile->id          = $this->db->insert_id();
    	$profile->fullname    = $user_data->fullname;
    	$profile->institution = $user_data->institution;
    	$profile->whitelist   = $this->_white_list($user->email);

    	$this->db->set($profile);
    	$this->db->insert('user_profile');

    	return $profile->id;
    }

    /**
     * Determine if a user should be whitelisted based on their email address domain
     * and the domains set to be whitelisted in the config file
     *
     * @param string $email The e-mail address of the user
     * @return boolean TRUE if should be whitelisted, FALSE otherwise
     */
    function _white_list($email) {
    	$whitelist = FALSE;
    	if (config_item('whitelist_domains')) {
    		$domains = explode(':', config_item('whitelist_domains'));
    		foreach ($domains as $domain) {   // Check against each domain in turn
				if (substr($data['email'], strlen($data['email']) - strlen($domain))
				    == $domain) {
					$whitelist = TRUE;
				}
    		}
    	}

    	return $whitelist;
    }

	/**
	 * Checks the password for a give userid.
	 *
	 * @param integer $user_id The id of the user
	 * @param string $password The unhashed password
	 * @return boolean TRUE if the password is valid, FALSE otherwise
	 */
	function password_valid_for_userid($user_id, $password) {
		$password_valid = FALSE;
	    // Get the password hash and check the password against it
		$this->db->where('id', $user_id);
        $query = $this->db->get('user');
        if ($query->num_rows() == 1) {
            $user = $query->row();
            $hash = $user->password;
            $password_valid = $this->_check_password($password, $hash);
        }

        return $password_valid;
	}

	/**
	 * Checks the password for a given username
	 *
	 * @param string $username The user name to check the password for
	 * @param string $password The unhashed password
	 * @return boolean TRUE if the password is valid, FALSE otherwise
	 */
    function password_valid($username, $password) {
		$password_valid = FALSE;
	    // Get the password hash and check the password against it
		$this->db->where('user_name', $username);
        $query = $this->db->get('user');
        if ($query->num_rows() == 1) {
            $user = $query->row();
            $hash = trim($user->password);
            $password_valid = $this->_check_password($password, $hash);
        }

        return $password_valid;
    }

    /**
     * Update the password for a user
     *
     * @param integer $user_id The id of the user
     * @param string $password The new unhashed password for the user
     */
	function update_password($user_id, $password) {
	    $this->db->where('id', $user_id);
	    $this->db->update('user', array('password' => $this->_hash_password($password)));
	}

    /**
     * Update the email for a user to the email stored as the 'new email'
     * @param integer $user_id The id of the user
    */
    function update_email($user_id) {
        $user = $this->get_user($user_id);
        $new_email = $user->new_email;
        $this->db->where('id', $user_id);
        $this->db->update('user', array('email' => $new_email));
    }

	/**
	 * Updates any data that needs to be updated when a user logs in
	 * (e.g. logs, date last visited etc)
	 *
	 * @param integer $user_id The id of the user
	 * @param bool $attempt Is this a banned user trying to login? Default FALSE.
	 */
	function update_user_login_data($user_id, $attempt=FALSE) {
		// Update the time of last visit
		$this->db->where('id', $user_id);
		//Bug #69, 'user' table is a historic exception - it uses MySQL datetime/timestamp.
		$this->db->update('user', array('last_visit'=>date('Y-m-d H:i:s')));

        $item_type = ($attempt) ? 'login_attempt' : 'login';

		// Update the log table
        $this->db->set('item_id', $user_id);
        $this->db->set('item_type', $item_type);
        // 'logs' is like most tables - it uses Unix timestamp.
        $this->db->set('timestamp', time());
        $this->db->set('user_id', $user_id);
        $this->db->set('ip', $this->input->ip_address());
        $this->db->insert('logs');
	}

   /**
    * Records an unsuccessful login attempt
    * @param integer $user_id The id of the user
    */
    function update_user_login_attempt_data($user_id) {
        // Update the log table
        $this->db->set('item_id', $user_id);
        $this->db->set('item_type', 'login_attempt');
        // 'logs' is like most tables - it uses Unix timestamp.
        $this->db->set('timestamp', time());
        $this->db->set('user_id', $user_id);
        $this->db->set('ip', $this->input->ip_address());
        $this->db->insert('logs');
        // Add an event to the admin cloudstream
        $this->CI =& get_instance();
        $this->CI->load->model('event_model');
        $this->CI->event_model->add_event('admin', 0, 'login_attempt', $user_id);
    }

   /**
    * Determine if there have been too many unsuccessful login attempts for
    * a specified user in the last ten minutes. The number of login attempts
    * allowed is specified in the cloudengine config file.
    *
    * @param integer $user_id The id of the user
    * @return boolean TRUE if too many attempts, FALSE otherwise
    */
    function too_many_login_attempts($user_id) {
        $user_id = (int) $user_id; // Make sure user_id is an integer
        $too_many_attempts = TRUE;
        // Maximum number of login attempts in the last ten minutes for a single user
        $no_attempts_allowed = config_item('max_login_attempts');

        $end_time = time();
        $start_time = $end_time - 10*60;
        $this->CI =& get_instance();

        $query = $this->CI->db->query("SELECT * FROM logs
                              WHERE item_type = 'login_attempt'
                              AND user_id = $user_id
                              AND timestamp > $start_time
                              AND timestamp < $end_time");

        $no_attempts = $query->num_rows();

        if ($no_attempts < $no_attempts_allowed) {
            $too_many_attempts = FALSE;
        }

        return $too_many_attempts;
    }

	/**
	 * Set a forgotten password code for a user
	 *
	 * @param integer $user_id The id of the user
	 * @param string $forgotten_password_code The forgotten password code
	 */
	function set_forgotten_password_code($user_id, $forgotten_password_code) {
	    $this->db->where('id', $user_id);
	    $this->db->update('user', array('forgotten_password_code'=>$forgotten_password_code));
	}

    /**
     * Set a change email code for a user
     *
     * @param integer $user_id The id of the user
     * @param string $change_email_code The forgotten password code
     * @param string $new_email The new email address to store
     */
    function set_change_email_code($user_id, $change_email_code, $new_email) {
        $this->db->where('id', $user_id);
        $this->db->update('user', array('change_email_code' =>
                                        $change_email_code));

        $this->db->where('id', $user_id);
        $this->db->update('user', array('new_email'=>$new_email));
    }

	/**
	 * Check if the forgotten password code for a given user is correct
	 *
	 * @param integer $user_id
	 * @param ustring $forgotten_password_code
	 * @return boolean TRUE if it is correct, FALSE otherwise
	 */
	function forgotten_password_code_valid($user_id, $forgotten_password_code) {
	    $code_correct = FALSE;
	    if ($forgotten_password_code) { // Make sure returns FALSE if no code is set
		    $this->db->where('id', $user_id);
	        $this->db->where('forgotten_password_code', $forgotten_password_code);
	        $query = $this->db->get('user');
	        if ($query->num_rows() == 1) {
	            $code_correct = TRUE;
	        }
	    }

        return $code_correct;
	}

    /**
    * Check if the change email code for a given user is correct
    *
    * @param integer $user_id
    * @param ustring $change_email_code
    * @return boolean TRUE if it is correct, FALSE otherwise
    */
    function change_email_code_valid($user_id, $change_email_code) {
        $code_correct = FALSE;
        if ($change_email_code) { // Make sure returns FALSE if no code is set
            $this->db->where('id', $user_id);
            $this->db->where('change_email_code', $change_email_code);
            $query = $this->db->get('user');
            if ($query->num_rows() == 1) {
                $code_correct = TRUE;
            }
        }

        return $code_correct;
    }

	/**
	 * Get the country options possible for a user
	 *
	 * @return array An array of the country IDs and country names
	 */
	function get_countries() {
		$options[225] = "United Kingdom"; // The first option becomes the default,
		// make the UK the default - hack as won't work if database table is changed.
		$query = $this->db->get('country');
		foreach ($query->result() as $row) {
			$options[$row->{'id'}] = $row->{'name'};
		}

		return $options;
	}

	/**
	 * Creates the hash of a password (so that the encoded version can be stored in the
	 * database rather than the plaintext version)
	 * Note that from a security perspective, the use_password_hash config item should always
	 * be set to TRUE. The option to set it to FALSE is given for reasons of backwards
	 * compatibility.
	 *
	 * @param string $password
	 * @return string The hashed password
	 */
  	function _hash_password($password) {
  		if (config_item('use_password_hash')) {
  			$this->CI = & get_instance();
  			$this->CI->load->library('hash');
  			$hash = Hash::HashPassword($password);
  		} else {
  			$hash = md5($password);
  		}

		return $hash;
  	}

  	/**
  	 * Check if a password matches a hash
  	 *
  	 * @param string $password The password
  	 * @param The $hash that the string should match
  	 * @return boolean TRUE if it matches, FALSE otherwise
  	 */
  	function _check_password($password, $hash) {
  		$password_valid = FALSE;
  		if (config_item('use_password_hash')) {
  			$this->CI = & get_instance();
  			$this->CI->load->library('hash');
  			$password_valid = Hash::CheckPassword($password, $hash);
  		} else {
  			if (md5($password) == $hash) {
  				$password_valid = TRUE;
  			}
  		}
		return $password_valid;
  	}
}
