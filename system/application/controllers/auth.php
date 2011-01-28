<?php

/**
 * Controller for authentication-related functionality
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Authentication
 */

class Auth extends MY_Controller {

	function Auth() {
		parent::MY_Controller();
		$this->load->model('user_model');
		$this->load->model('auth_model');
		$this->load->library('layout', 'layout_main'); 
        $this->load->library('form_validation');        
	}
		
	/**
	 * User login form and processing for user login form
	 *
	 */
    function login() {
        // We need to set the referrer so we can send the user back to the right page after
        // having logged in:
        $this->_save_referrer();
        
        // If already logged in then just go to the referring page
        if ($this->auth_lib->is_logged_in()) {
        	$this->_redirect_to_saved_referrer();
        }
        
        //If not already logged in, process the login information
        if ($this->input->post('submit')) {
        	// We could check the username and password are non-empty here, 
        	// but that should be caught in the password check anyway, so can 
        	// live without for the time being. 
            $username = $this->input->post('user_name');
            $password = $this->input->post('password');
            
            $login_success = $this->auth_lib->login($username, $password);
            if ($login_success) {
            	 $this->_redirect_to_saved_referrer();
            } else {
            	$data['error'] = t("Username or password is not recognised");
            } 
        } 
        
		$data['title'] = t("Login");   
        $this->layout->view('auth/login_form', $data);

    }
    
    /**
     * Save the referring page so we can redirect to it later
     */
    function _save_referrer() {
        if (isset($_SERVER['HTTP_REFERER'])) {
	        $referrer = $_SERVER['HTTP_REFERER'];
	        // Only save the referre if it's actually a page from the site, 
	        // and don't save if it's the login or an activation page as will cause 
	        // problems with redirect loops
	        if (strpos($referrer, 'login') === FALSE
	            && strpos($referrer, 'activation') === FALSE
	            && strpos($referrer, base_url()) === 0) {
	            $this->db_session->set_userdata('referrer', $referrer);
	        } 
        }
    }
    
    /**
     * Redirect to a saved referring page
     */
    function _redirect_to_saved_referrer() {
    	// Redirect to the home page if no referrer has been saved
    	$saved_referrer = $this->db_session->userdata('referrer');
        if ($saved_referrer) {
            redirect($saved_referrer);
        } else {
            redirect('/');
        }
    }
    
    /**
     * Logout a user 
     */
    function logout() {
        $referrer = $_SERVER['HTTP_REFERER'];
        $this->auth_lib->logout();
        redirect($referrer);
    }	
 
	/**
	 * User registration form and registration form processing
	 */
    function register() {	
		$display_form = TRUE;

    	$data['title'] = t("Register");

    	// Set up form validation.
		// user_name - alpha_dash matches HTML5 @pattern in views/auth/register_form.php [#128].
        $this->form_validation->set_rules('user_name', t("Username"),
                      'trim|required|max_length[45]|min_length[4]|alpha_dash|callback__username_duplicate_check');
        $this->form_validation->set_rules('fullname', t("Full name"), 
                                         'trim|required|max_length[140]|callback__fullname_check');        
        $this->form_validation->set_rules('email', t("Email"), 
                                  'trim|required|valid_email|callback__email_duplicate_check|max_length[320]');
        $this->form_validation->set_rules('institution', t("Institution"), 
                                         'trim|required|max_length[140]');        
        $this->form_validation->set_rules('country_id', t("Country"), 'required');
        $this->form_validation->set_rules('password', t("Password"), 
                                                'trim|required|min_length[6]|max_length[50]');
        $this->form_validation->set_rules('password_confirm', t("Password Confirm"),
                                                  'trim|required|matches[password]');
        if (config_item('x_captcha')) {
       		$this->form_validation->set_rules('captcha', t("captcha Code"), 
       		                                  'trim|required|callback__captcha_check');
        }        

        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');  
  
    	if ($this->input->post('submit')) {
    		$user->user_name   = trim($this->input->post('user_name'));
    		$user->email       = trim($this->input->post('email'));
    		$user->fullname    = trim($this->input->post('fullname'));
    		$user->institution   = trim($this->input->post('institution'));
    		$user->country_id  = $this->input->post('country_id');
    		$user->password    = $this->input->post('password'); 
    		
    		if ($this->form_validation->run()) {
    			$this->auth_lib->register($user);
    			$this->layout->view('auth/register_success', $data); 
    			$display_form = FALSE; 			
    		}
    	}

		if (config_item('x_captcha')) {
            $this->auth_lib->captcha_init('_register');
            $data['captcha'] = $this->config->item('FAL_captcha_image');   
		} 		
		
		if ($display_form) {
			$data['countries'] = $this->auth_model->get_countries();
			$data['user']      = $user;
			$this->layout->view('auth/register_form', $data);
		}
    }


    /**
     * Process the user activiation. This is the link that the user is sent in 
     * the activation e-mail
     *
     * @param integer $temp_user_id The temporary id assigned to the user before activation
     * @param string $code The activation code 
     */
    function activation($temp_user_id = 0, $code = '') {	
		$success = $this->auth_lib->activate($temp_user_id, $code);
		
		if ($success) {
			$data['title'] = t("Activation Successful");
			$this->layout->view('auth/activation_success', $data);
		} else {
			$data['title'] = t("Activation Unsuccessful");
			$this->layout->view('auth/activation_failed', $data);
		}
    }
    
    /**
     * Form to request a forgotten password and processing for the forgotten password
     * form
     */
    function forgotten_password() {	
    	$data['title'] = t("Forgotten Password");
    	
    	// Form validation 
    	$this->form_validation->set_rules('email', t("Email"), 'trim|required|callback__email_exists');

    	if ($this->input->post('submit') && $this->form_validation->run()) {
    		$email = $this->input->post('email');
    		$success = $this->auth_lib->forgotten_password($email);
    		if ($success) {
    			$this->layout->view('auth/forgotten_password_success', $data);
    		} else {
    			$this->layout->view('auth/forgotten_password_failed', $data);
    		}
    	} else {
    		$this->layout->view('auth/forgotten_password_form', $data);
    	} 
    }
    
    /**
     * Resets a user password - this is the link sent to the user in the password 
     * reset e-mail
     *
     * @param integer $user_id The id of the user
     * @param string $code The reset code
     */
    function new_password($user_id = 0, $code = '') {	
       $data['title'] = t("Reset forgotten password");
       $success = $this->auth_lib->new_password($user_id, $code);
       if ($success) {
           $this->layout->view('auth/new_password_success', $data);  
       } else {
	   	   $this->layout->view('auth/new_password_failed', $data);  
       }    
    }
   
   /**
    * Form for a user to change their password and to process the change password form
    */
   function change_password() {
        $user_id = $this->db_session->userdata('id');
        $this->auth_lib->check_logged_in();
        
        $data['user']= $this->user_model->get_user($user_id);
        
        $this->form_validation->set_rules('old_password', t("Old Password"), 
                                          'trim|required');
        $this->form_validation->set_rules('password', t("New Password"), 
                      'trim|required|min_length[6]|max_length[50]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', t("Confirm New Password"), 
                                                      'trim|required');   
        // Check new passwords match
        $this->form_validation->set_rules($rules);
    
        if ($this->input->post('submit')) {
        	$old_password = $this->input->post('old_password');
        	$new_password = $this->input->post('password');
            if ($this->form_validation->run()) {
                $password_correct = $this->auth_model->password_valid_for_userid($user_id, 
                                                                                $old_password);
                if (!$password_correct) {
                    $this->layout->view('auth/change_password_failed');
                    return;
                } else {
                    $this->auth_model->update_password($user_id, $new_password);
                    $this->layout->view('auth/change_password_success', $data);
                    return;
                }
            }
        }
           
        $data['title'] = t("Change Password");
        $this->layout->view('auth/change_password_form', $data); 
    }          
    
    /*******************************************************************************
     * Form Validation Functions used by this controller
     ********************************************************************************/
    
    /**
     * Form validation function to check if a username is already in use
     *
     * @param string $username The username to check
     * @return boolean TRUE if not in use, FALSE otherwise 
     */
    function _username_duplicate_check($username) {
    	$duplicate = $this->auth_model->username_exists($username, TRUE);
    	
    	if ($duplicate) {
    		$this->form_validation->set_message('_username_duplicate_check', 
    		                   t('The username is already in use.'));
		    
    	}
    	
        return !$duplicate;     	
    }
       
   /**
     * Form validation function to check if aan e-mail address is already in use
     *
     * @param string $username The email address to check
     * @return boolean TRUE if not in use, FALSE otherwise 
     */
    function _email_duplicate_check($email) {
    	$duplicate = $this->auth_model->email_exists($email, TRUE);
    	
    	if ($duplicate) {
    		$this->form_validation->set_message('_email_duplicate_check', 
    		                   t('The email address is already in use.'));
		    
    	}
    	
        return !$duplicate; 
    }
    
    /**
     * Form validation function to check that an e-mail address exists for 
     * some active user of the site
     *
     * @param string $email The e-mail address
     * @return boolean TRUE if it exists, FALSE otherwise
     */
    function _email_exists($email) {
    	$email_exists = $this->auth_model->email_exists($email, FALSE);
    	if (!$email_exists) {
    		$this->form_validation->set_message('_email_exists', 
    		                   t('We have no user with that e-mail address registered.'));
    	}
    	
    	return $email_exists;
    }
    
    /**
     * Form validation function to check that the value given for the captcha/captcha code is correct
     *
     * @param string $value The string entered by the user for the captcha
     * @return TRUE if the user-entered string is correct, FALSE otherwise
     */
    function _captcha_check($value) {
    	$value_correct = TRUE;
    	$captcha = $this->db_session->userdata('FreakAuth_captcha');
    	$this->db_session->unset_userdata('FreakAuth_captcha');
    	$control= strcmp($value, $captcha);
	    if ($control != 0) {
	        $this->form_validation->set_message('_captcha_check', 
	                           t('Please retype the letters in the image below'));
		    $value_correct = FALSE;
		}

		return $value_correct;
    } 
    
    /**
     * Validation function to check if a string contains a space, used as a callback for 
     * form validation for the fullname
     *
     * @param string $str The fullname
     * @return boolean TRUE if the name contains a space, FALSE otherwise
     */
    function _fullname_check($str) {
        $contains_space = TRUE;
        if (strpos($str, ' ') === FALSE) {
           $contains_space = FALSE;
           $this->form_validation->set_message('_fullname_check', 
           t("Your fullname must contain a space"));
        }

        return $contains_space;
    }  
}