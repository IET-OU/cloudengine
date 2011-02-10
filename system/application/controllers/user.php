<?php
/**
 * Controller for functionality related to users and user profiles (but not user 
 * authentication)
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */
class User extends MY_Controller {

	function User() {
		parent::MY_Controller();
		$this->load->model('user_model');
		$this->load->library('layout', 'layout_main'); 
        $this->load->model('event_model');   
	}
	
	/**
	 * Display a user's favourited items
	 *
	 * @param integer $user_id The ID of the user to display the favourites for
	 */
    function favourites($user_id = 0) { 
        $this->load->model('favourite_model');
        $current_user_id  = $this->db_session->userdata('id');
        if (!$user_id) {
            $user_id = $current_user_id;
        }
        
        if (!$user_id) {
        	show_error("You need to be logged on to view this page");
        }
        
        if ($user_id == $current_user_id) {
            $data['current_user'] = true;
        }
        
        $data['clouds']      = $this->favourite_model->get_favourites($user_id, 'cloud');
        $data['cloudscapes'] = $this->favourite_model->get_favourites($user_id, 'cloudscape');
        $data['user']        = $this->user_model->get_user($user_id);
        if ($data['current_user']) {
            $data['title']   = 'My Favourites';
        } else {
            $data['title']       = $data['user']->fullname."'s Favourites";
        }
        $this->layout->view('user/favourites', $data);
    }	

    /**
     * 'Delete' a user 
     *
     * @param integer $user_id The ID of the user to delete
     */
    function delete($user_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $this->user_model->delete($user_id);
        redirect(base_url().'user/view/'.$user_id);
    } 
    
    /**
     * 'Undelete' a user 
     *
     * @param integer $user_id The ID of the user to undelete
     */
    function undelete($user_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $this->user_model->undelete($user_id);
        redirect(base_url().'user/view/'.$user_id);
    }    
    
    /**
     * Ban a user 
     *
     * @param integer $user_id The ID of the user to whitelist
     */
    function ban($user_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $this->user_model->ban($user_id);
        redirect(base_url().'user/view/'.$user_id);
    }    
    
    /**
     * Unban a user 
     *
     * @param integer $user_id The ID of the user to whitelist
     */
    function unban($user_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $this->user_model->unban($user_id);
        redirect(base_url().'user/view/'.$user_id);
    }      
    
    /**
     * Whitelist a user so their items aren't moderated for spam - for admins only
     *
     * @param integer $user_id The ID of the user to whitelist
     */
    function whitelist($user_id = 0) {
    	if (!config_item('x_moderation')) {
    		show_404(); 
    	}
        $this->auth_lib->check_is_admin(); 
        $this->user_model->whitelist($user_id);
        redirect(base_url().'user/view/'.$user_id);
    }

	/**
	 * Display a list of the clouds for a user
	 *
	 * @param integer $user_id The ID of the user whose clouds to display
	 */
    function clouds($user_id = 0) {
        $data['clouds'] = $this->user_model->get_clouds($user_id);
        $data['profile'] = $this->user_model->get_user($user_id);
        $data['title']  = t("!person's clouds", array('!person'=>$data['profile']->fullname));
        $this->layout->view('user/cloud_list', $data);
    }  	
    
	/**
	 * Display the RSS feed for a user
	 *
	 * @param intger $user_id The ID of the user
	 */
    function rss($user_id = 0) {
        $this->load->helper('xml');
        $profile                  = $this->user_model->get_user($user_id);
        $data['clouds']           = $this->user_model->get_clouds($user_id, 10); 
        $data['encoding']         = $this->config->item('charset');
        $data['feed_name']        = $this->config->item('site_name').': '.$profile->fullname;
        $data['feed_url']         = base_url().'user/rss.'.$user_id;
        $data['page_description'] = $this->config->item('site_name').' clouds by '.$profile->fullname;
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');   
        header("Content-Type: application/rss+xml");       
        $this->load->view('rss/rss', $data);
    }
    
    /**
     * Display list of users that a person is following
     *
	 * @param intger $user_id The ID of the user
     */
    function following($user_id = 0) {
       $data['profile'] = $this->user_model->get_user($user_id);
       $data['users']   = $this->user_model->get_following($user_id);
       $data['title']   = t("!person's follows", array('!person'=>$data['profile']->fullname));
       $this->layout->view('user/following_list', $data); 
    }

    /**
     * Display list of followers of a user 
     *
	 * @param intger $user_id The ID of the user
     */
    function followers($user_id = 0) {
       $data['profile'] = $this->user_model->get_user($user_id);
       $data['users']   = $this->user_model->get_followers($user_id);
       $data['title']   = t("!person's followers", array('!person'=>$data['profile']->fullname));
       $this->layout->view('user/followers_list', $data); 
    }	
    
    /**
     * Follow a user 
     *
     * @param integer $followed_user_id The ID of the user to follow
     */
    function follow($followed_user_id = 0) {
        // Check logged in 
        $this->auth_lib->check_logged_in();
 
        // Follow the user
        $following_user_id = $this->db_session->userdata('id');
        $this->user_model->follow($followed_user_id, $following_user_id);
        $this->event_model->add_event('user', $following_user_id, 'follow', $followed_user_id);

        // Send an e-mail notifiying the person who is being followed unless they 
        // have unsubscribed from such notifications
        $followed_user  = $this->user_model->get_user($followed_user_id);
        $following_user = $this->user_model->get_user($following_user_id);
        if ($followed_user->email_follow) {
            $data['followed_user']  = $followed_user;
            $data['following_user'] = $following_user;
            $message = $this->load->view('email/follow', $data, true);  
            
            $subject = $following_user->fullname.' is now following 
                                                   you on '.config_item('site_name');
            // only send the e-mail if it's the live site otherwise send it 
            // to the site e-mail address for debug purposes.
            $to = $this->config->item('x_live') ? $followed_user->email : 
                                                  $this->config->item('site_email');
            
            send_email($to,  $this->config->item('site_email'), $subject, $message);
        }
        
        
        
        // Redirect to the followed user's profile page
        redirect('/user/view/'.$followed_user_id);
    }
    
    /**
     * Unfollow a user
     *
     * @param integer $followed_user_id The ID of the user to stop following
     */
    function unfollow($followed_user_id = 0) {
        // Check logged in 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        

        // Unfollow the user
        $following_user_id = $this->db_session->userdata('id');
        $this->user_model->unfollow($followed_user_id, $following_user_id);
        // Redirect to the unfollowed user's profile page
        redirect('/user/view/'.$followed_user_id);        
    }
    
    /**
     * Display a paged list of institutions alphabetically
     *
     * @param string $alpha The letter of the alphabet
     */
    function institution_list($alpha = 'A') {
        // Get the data for the list and display it
        $data['institutions'] = $this->user_model->get_institutions($alpha);
        $data['title']        = t("Institutions");
        $data['navigation']   = 'people';
        $data['alpha']        = $alpha;
        $this->layout->view('user/institution_list', $data);        
    }
    
    /**
     * Display the users in a particular institution
     *
     * @param string $institution The name of the institution (URL-encoded)
     */
    function institution($institution = '') {
        $institution         = urldecode($institution);
        $data['institution'] = $institution;
        $data['users']       = $this->user_model->get_users_in_institution($institution);
        $data['title']       = $institution;
        $data['navigation']  = 'people';
        $this->layout->view('user/institution', $data);          
    }
    
    /**
     * Edit a user's picture 
     */
    function edit_picture() {
        // Check user logged in        
        $user_id = $this->db_session->userdata('id');      
        $this->auth_lib->check_logged_in();
        
        $data['title'] = t("Edit Picture");
        $data['user_id'] = $user_id;
        $upload_path = $this->config->item('upload_path_user');
        if ($this->input->post('submit')) {
            $config['upload_path'] = $upload_path;
		    $config['allowed_types'] = 'gif|jpg|png';
		    $config['max_size']	= '500';
		    $config['max_width']  = '1024';
		    $config['max_height']  = '768';
		    $this->load->library('upload', $config);
    	
    		if (!$this->upload->do_upload()) {
    			$data['error'] = $this->upload->display_errors();
                $data['picture'] = $this->user_model->get_picture($user_id);
                $this->layout->view('user/edit_picture', $data);
    		} else {
    		    $upload_data = $this->upload->data();
    			$data = array('upload_data' => $upload_data);
                $data['user_id'] = $user_id;

                // Create the icons
                $file_name = $upload_data["file_name"];
                $original_height = $upload_data["image_height"];
                $original_width = $upload_data["image_width"];
    			$this->load->library('image_lib');
                $this->image_lib->crop_to_square($upload_path.$file_name, $original_height, 
                                                 $original_width);
                $this->image_lib->create_icon($upload_path, $file_name, 64, '');
                $this->image_lib->create_icon($upload_path, $file_name, 32, '32-');
                $this->image_lib->create_icon($upload_path, $file_name, 16, '16-');

    			$this->user_model->update_picture($user_id, $upload_data["file_name"]);
    			$this->layout->view('user/edit_picture_success', $data);
    		}  
    		 
        } else {
            $data['picture'] = $this->user_model->get_picture($user_id);

            $this->layout->view('user/edit_picture', $data);
        }
    } 
    
    /**
     * Display a picture for a user
     *
     * @param string $filename The filename of the image
     */
    function picture($filename) {
          $extension = end(explode(".", $filename));  
          $type = 'jpeg';
          if ($extension == 'png') {
              $type = 'png';
          }
          
          if ($extension == 'gif' || $extension == 'GIF') {
              $type = 'gif';
          }
          echo $this->config->item('upload_path_user'.$filename);
          header("Content-Type: application/$type");  
          readfile($this->config->item('upload_path_user').$filename); 
    }
    
    /**
     * Show the main people page
     */
    function people() {
        if ($this->input->post('name')) {
            $query_string = $this->input->post('name');
            $data['users'] = $this->user_model->search($query_string);
            $data['query_string'] = $query_string;
        }
        $data['title'] = t("People");
        $data['navigation'] = 'people';
        $this->layout->view('user/people', $data);
    }
    
    /**
     * Show the user list for specified letter of the alphabet
     *
     * @param string $alpha The letter of the alphabet
     */
    function user_list($alpha = 'A') {
        // Get the data for the list and display it
        if($this->auth_lib->is_admin()) {
          $only_active = FALSE;
        } else {
          $only_active = TRUE;
        }        
        $data['alpha'] = $alpha;
        $data['users'] = $this->user_model->get_users_alpha($alpha,$only_active);
        $data['title']  = t("Users");
        $data['navigation'] = 'people';
        $this->layout->view('user/user_list', $data);          
    }
    
    /**
     * Display a user's profile
     *
     * @param integer $user_id The ID of the user
     * @param string $type The type of items to display in the user's activity stream
     */
    function view($user_id = 0, $type = '') {
        $this->load->model('favourite_model');
        $this->load->model('events_model');
        $this->load->model('cloudscape_model');
        $this->load->model('tag_model');
        $current_user_id = $this->db_session->userdata('id');    
          
        if (!$user_id) {
            $user_id = $current_user_id;
        }
        
        if($this->auth_lib->is_admin()) {
          $only_active = FALSE;
        } else {
          $only_active = TRUE;
        }
        
        $user = $this->user_model->get_user($user_id, $only_active);
        
        // If the user id given is not a valid user id, display an error 
        if (!$user) {
            show_error(t("This user does not exist"));
        }
        
        // Get this user's cloudstream 
        $this->load->model('event_model');   
        $events = $this->event_model->get_events_for_user($user_id, 20, $type);
        if ($type) {
            $simple = true;
        }  
        $events = $this->event_model->display_format($events, $simple); 
        if ($events) {
            $events = array_slice($events , 0, 10); 
        }
        
        $data['type']             = $type;
        $data['events']           = $events; 
        $data['type']             = $type;
        $data['basepath']         = $this->config->site_url('user/view/'.$user_id);
        $data['rss_event']        = $this->config->site_url('event/user_rss/'.$user_id.'/'.$type);
        $data['user']             = $user;
        $data['display_email']    = $user->display_email;
        $data['title']            = $user->fullname;
        $data['rss']              = $this->config->site_url('user/rss/'.$user_id);
        $data['clouds']           = $this->user_model->get_clouds($user_id, 10);
        $data['picture']          = $this->user_model->get_picture($user_id);
        $data['cloud_total']      = $this->user_model->get_cloud_total($user_id);
        $data['cloudscape_total'] = $this->user_model->get_cloudscape_total($user_id);
        $data['cloudscapes']      = $this->cloudscape_model->get_cloudscapes_owner($user_id);
        $data['following']        = $this->user_model->get_following($user_id);
        $data['followers']        = $this->user_model->get_followers($user_id);
        $data['reputation']       = $this->favourite_model->get_reputation($user_id);
        $data['total_favourites'] = count($this->favourite_model->get_favourites($user_id, 
                                    'cloud')) + 
                                    count($this->favourite_model->get_favourites($user_id, 
                                    'cloudscape'));
        $data['admin']            = $this->auth_lib->is_admin();
        $data['past_events']      = $this->events_model->get_past_events_attended($user_id);
        $data['current_events']   = $this->events_model->get_current_events_attended($user_id);
        $data['tags']             = $this->tag_model->get_tags('user', $user_id);
        
        // Determine if the user is the current user 
        $data['current_user'] = FALSE;
        if ($user_id == $current_user_id) {     
            $data['current_user']    = TRUE; 
            $data['edit_permission'] = TRUE;
        }
        
        // Determine if the current user is following this user
        $data['isfollowing'] = $this->user_model->is_following($user_id, $current_user_id);    
        $this->layout->view('user/view', $data);
    }    
  	
    
    /**
    * Edit Profile
    */
    function edit() {
        $user_id = $this->db_session->userdata('id');
        $user = $this->user_model->get_user($user_id);
        if ($user_id !== $this->db_session->userdata('id') && 
            !$this->auth_lib->is_admin()) {     
            show_error(t("You do not have permission to view this page."));
        }        
    
        // Set the form validation rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules('fullname', t("Full Name"), 
                                          'required|max_length[140]|callback_fullname_check');
        $this->form_validation->set_rules('institution', t("Institution"), 'required');
        $this->form_validation->set_rules('department', t("Department"), 'max_length[140]');
        $this->form_validation->set_rules('twitter_username', t("Twitter Username"),
                                          'max_length[140]');
        $this->form_validation->set_rules('homepage', t("Homepage"), 
                                          'valid_url|max_length[140]');
        $this->form_validation->set_rules('description', t("Description"), '');
            
        if ($this->input->post('submit')) {
            if ($this->form_validation->run()) {
                $user->fullname         = ucwords($this->input->post('fullname'));
                $user->department       = $this->input->post('department');
                $user->institution      = $this->input->post('institution');
                $user->description      = $this->input->post('description');
                $user->twitter_username = $this->input->post('twitter_username');
                $user->homepage         = $this->input->post('homepage');  
                
                // Save the new user profile data and redirect the user to the view page for 
                // their profile
                $this->user_model->update_profile($user); 
                redirect(base_url().'user/view/'.$user_id);
            }
        }   
        
        $user = $this->user_model->get_user($user_id);
    
        if (set_value('fullname')) {
            $user->fullname = set_value('fullname');
        }
    
        if (set_value('homepage')) {
            $user->homepage = set_value('homepage');
        }
    
        if (set_value('department')) {
            $user->department = set_value('department');
        }       
    
        if (set_value('institution')) {
            $user->institution = set_value('institution');
        }    
        
        if (set_value('twitter_username')) {
            $user->twitter_username = set_value('twitter_username');
        }      
        
        if (set_value('description')) {
            $user->description = set_value('description');
        }  
            
        $data['title']= t("Edit Profile");
        $data['user'] = $user;
        
        $this->layout->view('user/edit', $data); 
    
    } 
        
    /**
     * Edit an email preferences user/preferences
     */
    function preferences() {    
        $user_id  = $this->db_session->userdata('id');
        if (!$user_id) {
            show_error(t("You need to be logged on to view this page"));
        }
        
        if ($this->input->post('submit')) {
            // Get the form data 
            $user = $this->user_model->get_user($user_id);
            $user->user_id                = $user_id;
            $user->email_follow           = $this->input->post('email_follow');
        	$user->email_comment          = $this->input->post('email_comment');
        	$user->email_comment_followup = $this->input->post('email_comment_followup');
        	$user->email_news             = $this->input->post('email_news');
        	$user->email_events_attending = $this->input->post('email_events_attending');
        	$user->display_email          = $this->input->post('display_email');
        	$user->do_not_use_editor      = $this->input->post('do_not_use_editor');
          $user->email_message_notify   = $this->input->post('email_message_notify');
        	$this->user_model->update_profile($user);
            $data['message'] = t("Your preferences have been saved");
        } 
        
        $data['title'] = t("Edit Preferences");
        $data['profile'] = $this->user_model->get_user($user_id);
        
        $this->layout->view('user/preferences', $data); 
    }

    /**
     * Checks if a string contains a space, used as a callback for form validation for the 
     * fullname
     *
     * @param string $str
     * @return boolean
     */
    function fullname_check($str) {
        $contains_space = TRUE;
        if (strpos($str, ' ') === FALSE) {
           $contains_space = FALSE;
           $this->form_validation->set_message('fullname_check', 
           t("Your fullname must contain a space"));
        }

        return $contains_space;
    }
}