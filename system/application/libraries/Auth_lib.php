<?php
/**
 * Library for Authentication related functions
 *
 * @copyright 2009, 2010, 2012 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Authentication
 */
class Auth_lib {

    protected $CI;

    public function Auth_lib() {
        $this->CI =& get_instance();
        $this->CI->load->model('auth_model');
        $this->CI->load->library('Db_session');
    }

    /**
     * Process a user's registration
     *
     * @param object $user The user's registration details
     */
    public function register($user) {
    	$hash = $this->CI->auth_model->_hash_password($user->password);
    	$user->password = $hash;

    	$activation_code       = $this->_generate_random_string(6);
    	$user->activation_code = $activation_code;
    	// Add the user as a temporary user and send them an activation email
    	$user->temp_user_id = $this->CI->auth_model->insert_temp_user($user);
    	$this->_send_activation_email($user, $activation_code);
    }

    /**
     * Send the activation e-mail to a newly registered user with the link for the 
     * user to click on to activate their account. 
     *
     * @param object $user The details of the user to send the e-mail to
     * @param string $activation_code The activation code to send in the e-mail
     */
    protected function _send_activation_email($user, $activation_code) {
    	$data['temp_user_id']    = $user->temp_user_id;
    	$data['fullname']        = $user->fullname;
        $data['user_name']       = $user->user_name;
    	$data['activation_code'] = $activation_code;

 		$message = $this->CI->load->view('email/activation_email', $data, TRUE);
        $result = send_email($user->email, config_item('site_email'), 
                 t("!site-name! - Account activation"), $message);
    }

    /**
     * Process a user activation request
     *
     * @param integer $temp_user_id The temporary id assigned to the user before activation
     * @param string $code The activation code 
     * @return boolean TRUE if the user was successfully activated, FALSE otherwise
     */
    public function activate($temp_user_id, $activation_code) {
    	$success = FALSE;
    	
    	// Delete any users in the temp table who have expired
    	// Might want to move this to a cron job one day but putting it here will 
    	// at least mean that it gets run before any of the users get activated
    	$this->CI->auth_model->clean_expired_temp_users();
    	
    	// Activate the user
    	$user_id = $this->CI->auth_model->activate_user($temp_user_id, $activation_code);
    	
    	// Add the user's registration to the log of site events that admin's can see
    	if ($user_id) {
    		$success = TRUE;
    		$this->CI->load->model('event_model');   
            $this->CI->event_model->add_event('admin', 0, 'new_user', $user_id);
    	}
    	
		return $success;
    } 
    
    /**
     * Process a forgotten password reset request
     *
     * @param string $email The e-mail address given for the request
     * @return boolean TRUE if the request was processed successfully, FALSE if an error
     * occurred. 
     */
    public function forgotten_password($email) {
    	$success = FALSE;
    	$user = $this->CI->auth_model->get_user_by_email($email);
    	if ($user) {
    		$user_id = $user->id;
    		
    		// Generate a forgotten password code, save it for future reference and send
    		// an e-mail to the user the link with the code
			$forgotten_password_code = $this->_generate_random_string(6);
			$this->CI->auth_model->set_forgotten_password_code($user_id, 
			                                                   $forgotten_password_code);
            $this->_send_forgotten_password_email($user, $forgotten_password_code);			
    		$success = TRUE;
    	}
    	
    	return $success;
    }

    /**
     * Send the password to a user when they have requested a forgotten password reset
     * containing the link for them to click on to obtain a new password. 
     *
     * @param object $user Details of the user to send the e-mail too
     * @param string $forgotten_password_code The forgotten password code
     */
    protected function _send_forgotten_password_email($user, $forgotten_password_code) {
		$data['fullname']                = $user->fullname;
		$data['user_id']                 = $user->id;
		$data['forgotten_password_code'] = $forgotten_password_code;

        $message = $this->CI->load->view('email/forgotten_password_email', $data, true);
        $this->CI->load->plugin('phpmailer');
        send_email($user->email, config_item('site_email'), 
                   t('!site-name! - Forgotten password'), $message); 
    }

    /**
     * Process a forgotten password reset
     *
     * @param integer $user_id The id of the user
     * @param string $code The forgotten password reset code
     * @return boolean TRUE if the password was successfully reset, FALSE otherwise
     */
    public function new_password($user_id, $code) {
    	$success = FALSE;
    	$user = $this->CI->auth_model->get_user($user_id);
    	$code_correct = $this->CI->auth_model->forgotten_password_code_valid($user_id, 
    	                                                           $code);

    	if ($code_correct) {
	    	// Generate the new password and send it to the user
	    	$password         = $this->_generate_random_string(8);      
	        $this->_send_new_password_email($user, $password);
	        $this->CI->auth_model->update_password($user_id, $password);
	        // Remove the forgotten password code 
	        $this->CI->auth_model->set_forgotten_password_code($user->id, '');
	        $success = TRUE;
    	}
        return $success;
    }

    /**
     * Sends the e-mail to a user with their temporary new password when they have
     * requested a password reset. 
     * 
     * @param object $user Details of the user to send the e-mail to
     * @param string $password The new (plain-text) password
     */
    protected function _send_new_password_email($user, $password) {
   		$data['password']  = $password;
   		$data['fullname']  = $user->fullname;
   		$data['user_name'] = $user->user_name;           

        // displays message to the user on screen
        $message = $this->CI->load->view('email/new_password_email', $data, true);

        $this->CI->load->plugin('phpmailer');
        send_email($user->email, config_item('site_email'), 
                   t('!site-name! - Password reset'), 
                   $message); 
 
    }
     
    /**
     * Process a request to change the e-mail address of the user by sending
    * out a confirmation email to the old address and storing the information
    * so that the email address can be changed when the link in that email is 
    * clicked
     * 
     * @param integer $user_id The id of the user
     * @param string $new_email The new email address 
     * @return boolean TRUE if the request was processed successfully, FALSE if an error
     * occurred. 
     */
    public function change_email($user_id, $new_email) {
        $success = FALSE;
        $user = $this->CI->auth_model->get_user($user_id);
        if ($user) {
            // Generate a change email code, save it for future reference and 
           // send an e-mail to the user's old email with a link with the code
           $change_email_code = $this->_generate_random_string(6);
           $this->CI->auth_model->set_change_email_code($user_id, 
           	           	           	$change_email_code, $new_email);
            $this->_send_change_email_email($user, $change_email_code, 
           	           	               $new_email);	
            $success = TRUE;
        }
        
        return $success;
    }
    
    /**
     * Process an email change request
     *
     * @param integer $user_id The id of the user
     * @param string $code The email change code
     * @return boolean TRUE if the email was successfully changed, FALSE otherwise
     */
    public function new_email($user_id, $code) {
        $success = FALSE;
        $user = $this->CI->auth_model->get_user($user_id);
        $code_correct = $this->CI->auth_model->change_email_code_valid(
                                                              $user_id, $code);

        if ($code_correct) {     
            $this->CI->auth_model->update_email($user_id);
            // Remove the change email code 
            $this->CI->auth_model->set_change_email_code($user->id, '', '');
            $success = TRUE;
        }
        return $success;
    }
    
    /**
     * Sends the e-mail to a user when they have requested an e-mail change
     * 
     * @param object $user Details of the user to send the e-mail to
     * @param string $code The change email code
     * @param string $new_email The new email address 
     */
    protected function _send_change_email_email($user, $code, $new_email) {
        $data['code']      = $code;
        $data['fullname']  = $user->fullname;
        $data['user_name'] = $user->user_name; 
        $data['new_email'] = $new_email;
        $data['user_id']   = $user->id;    

        // displays message to the user on screen
        $message = $this->CI->load->view('email/change_email_email', $data, 
                                         true);

        $this->CI->load->plugin('phpmailer');
        send_email($user->email, config_item('site_email'), 
                   t('!site-name! - Change Email'), $message); 
    }

    /**
     * Logs a user out
     */
    public function logout() {
        if ($this->CI->db_session) {
            $username = $this->CI->db_session->userdata('user_name');

            if ($username) {
        		$this->CI->db_session->unset_userdata('id');
            	$this->CI->db_session->unset_userdata('user_name');
            	$this->CI->db_session->unset_userdata('role');
            }
        }
    }  

	/**
	 * Logs in a user
	 *
	 * @param string $username The username to login 
	 * @param string $password The password given for the username
	 * @return boolean TRUE if login was successful, FALSE otherwise 
	 */
	public function login($username, $password) { 
		$login_success = $password_valid = FALSE;
		$user= $this->CI->auth_model->get_user_by_username($username);
        if (1 == $user->banned) {
            // Log login-attempts by banned users! $attempt=TRUE
            $this->CI->auth_model->update_user_login_data($user->id, TRUE);
            show_error(t("The username is invalid. If you think this is a mistake, please contact site support."));
        } else {
            $password_valid = $this->CI->auth_model->password_valid($username, $password);
        }

        if ($this->CI->auth_model->too_many_login_attempts($user->id)) {
            show_error(t("Too many login attempts. Please try again later or contact site support."));
        }
         
        if ($password_valid) {

            // Update the session data
            $userdata['id']         = $user->id;
            $userdata['user_name']  = $user->user_name;
            $userdata['country_id'] = $user->country_id;
            $userdata['email']      = $user->email;
            $userdata['role']       = $user->role;
            $userdata['last_visit'] = $user->last_visit;
            $userdata['created']    = $user->created;
            $userdata['modified']   = $user->modified;
            $this->CI->db_session->set_userdata($userdata);
            // Update logged information about the log in in the database
            $this->CI->auth_model->update_user_login_data($user->id);
            $login_success = TRUE;
        } else {
            $this->CI->auth_model->update_user_login_attempt_data($user->id);
        }
        return $login_success;
    }

	/**
	 * Generate a captcha
	 *
	 */
	public function captcha_init() {
        if (config_item('x_captcha')) {
        	$this->CI->load->library('Captcha_lib', 'captcha_lib');
        	$this->CI->captcha_lib->captcha_init('_register');
        }		
	}

	/**
	 * Determines if the current user is logged on to the site
	 *
	 * @return boolean TRUE if logged on, FALSE otherwise
	 */
	public function is_logged_in() {
		$logged_in = FALSE;
		if ($this->CI->db_session->userdata('role')) {
			$logged_in = TRUE;
		}
		return $logged_in;
	}

	/**
	 * If the current user is not logged on, redirects to the login page
	 */
	public function check_logged_in() {
		if (!$this->is_logged_in()) {
			redirect('/auth/login');
		}
	}

	/**
	 * Determines if the current user is an admin
	 *
	 * @return boolean TRUE if logged on and an admin, FALSE otherwise 
	 */
	public function is_admin() {
		$is_admin = FALSE;

        if ($this->CI->db_session->userdata('id')) {
            $username = $this->CI->db_session->userdata('user_name');
            $role     = $this->CI->db_session->userdata('role');

            if ($username && $role == 'admin') {
            	$is_admin = TRUE;
            }
        }

        return $is_admin;
	}

	/**
	 * Check if the current user is logged on and is an admin. If not, 
	 * redirects to an error page. 
	 */
	public function check_is_admin() {
		$is_admin = $this->is_admin();
		if (!$is_admin) {
			show_error(t('You are not allowed to access this page'));
		}

	}

    /**
     * Generates a random string.
     *
     * @return $key random string
     */
    protected function _generate_random_string($length)
    {
    	// Not using special characters as can cause problems in URLs 
        $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";

        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $charset[(mt_rand(0, (strlen($charset)-1)))];
        }
        return $key;
    }
}