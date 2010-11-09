<?php 

/**
 * Controller for cloudscape-related functionality
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Cloudscape
 */

class Cloudscape extends MY_Controller {

    function Cloudscape() {
        parent::MY_Controller();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('format');
        $this->load->model('cloudscape_model');
        $this->load->model('user_model'); 
        $this->load->model('event_model');
        $this->load->model('tag_model');
		$this->load->library('layout', 'layout_main');
    }
    
    /**
     * Display all the cloudscapes beginning with a particular letter
     *
     * @param string $alpha The letter of the alphabet
     */
    function cloudscape_list($alpha = 'A') {
        // Get the list of cloudscapes
        $data['alpha']           = $alpha;
        $data['cloudscapes']     = $this->cloudscape_model->get_cloudscapes_alpha($alpha);
        $data['title']           = t("All cloudscapes");
        $data['navigation']      = 'cloudscapes';
        $data['new_cloudscapes'] = $this->cloudscape_model->get_new_cloudscapes(15);      
        $this->layout->view('cloudscape/cloudscape_list', $data);
    }
    
    /**
     * View a specified cloudscape 
     *
     * @param string $cloudscape Either the ID of the cloudscape of the cloudscape title 
     * (URL-encoded)
     * @param string $type The item type to display in the cloudscape activity stream
     * @param integer $section_id The section of the cloudscape to display
     */
    function view($cloudscape = 0, $type = 'all', $section_id = 0) {        
        $this->load->model('favourite_model');
        $this->load->model('events_model');
                
        $user_id = $this->db_session->userdata('id');
        
        // Get the cloudscape 
        if (is_numeric($cloudscape)) {
            $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape);
            $cloudscape_id = $cloudscape;
        } else {
           $data['cloudscape'] = $this->cloudscape_model->get_cloudscape_by_title(
                                                                    urldecode($cloudscape)); 
           $cloudscape_id = $data['cloudscape']->cloudscape_id;
        }
        $cloudscape = $data['cloudscape'];    
        if ($data['cloudscape']) {
            // Get tags, followers with images and number of followers for the cloudscapes 
            $data['tags'] = $this->tag_model->get_tags('cloudscape',  $cloudscape_id);
            $data['followers'] = $this->cloudscape_model->get_followers($cloudscape_id);
            $data['total_followers'] = $this->cloudscape_model->get_total_followers(
                                                                             $cloudscape_id);
            
            // Figure out what permissions the current user has for this cloudscape         
            if ($user_id) {
                $data['admin_permission'] = $this->cloudscape_model->has_admin_permission(
                                                                   $cloudscape_id, $user_id);
                $data['edit_permission']  = $data['admin_permission'];
                $data['post_permission']  = $this->cloudscape_model->has_post_permission(
                                                                   $cloudscape_id, $user_id);
                $data['owner'] = $this->cloudscape_model->has_owner_permissions(
                                                                   $cloudscape_id, $user_id);
            }
    
            // Get the clouds to display
            $data['sections'] = $this->cloudscape_model->get_sections($cloudscape_id);

            if ($data['sections'] && $section_id) {
                // A section has been specified - only get the clouds for that section
                // Really ought to check the section  belongs to the cloudscape
                $data['clouds'] = $this->cloudscape_model->get_clouds_in_section($section_id); 
                $data['section_id'] = $section_id; 
            } else {
                $data['clouds'] = $this->cloudscape_model->get_clouds($cloudscape_id);     
            }
                        
            // Get the tweets for the cloudscape if a tag has been defined
            if (config_item('x_twitter') && $data['cloudscape']->twitter_tag) {
                $this->load->library('twitter');
                $this->twitter->auth(config_item('x_twitter_username'), 
                                     config_item('x_twitter_password'));
                $tweets = $this->twitter->search('search', 
                                  array('q' => urlencode($data['cloudscape']->twitter_tag)));
                $data['tweets'] = $tweets->results;
            }
            
            // Get all the information to display the cloudscape's cloudstream 
            $this->load->model('event_model');  
            
            $events = $this->event_model->get_events_for_cloudscape($cloudscape_id, 20, 
                                                                    $type);
            $simple = false;
            if ($type) {
                $simple = true;
            }
            
            $events = $this->event_model->display_format($events, $simple); 
            $data['type']   = $type;
            
            if ($events) {
                $events = array_slice($events , 0, 10);
            }
            
            // Make sure the view has everything it needs 
            $data['user_id']             = $user_id;
            $data['total_views'] = $this->cloudscape_model->get_total_views($cloudscape_id);          
            $data['events']              = $events; 
            $data['type']                = $type;
            $data['basepath']            = '/cloudscape/view/'.$cloudscape_id;
            $data['omit_cloudscapes']    = true;          
            $data['rss']                 = '/event/cloudscape_rss/'.$cloudscape_id.'/'.$type;
            $data['total_favourites']    = $this->favourite_model->get_total_favourites(
                                                              $cloudscape_id, 'cloudscape'); 
            $data['favourite']           = $this->favourite_model->is_favourite($user_id, 
                                                            $cloudscape_id, 'cloudscape');  
            $data['show_favourite_link'] = $this->favourite_model->can_favourite_item(
                                                     $user_id, $cloudscape_id, 'cloudscape');
            $data['title']               = $data['cloudscape']->title;
            $data['admin']               = $this->auth_lib->is_admin();
            $data['following']           = $this->cloudscape_model->is_following(
            												       $cloudscape_id, $user_id);
            $data['attended']            = $this->events_model->is_attending($cloudscape_id,
                                                                                   $user_id);
            $data['navigation']          = 'cloudscapes';

            // If this cloudscape is an event, determine if it is a past event
            if ($cloudscape->start_date) {
   
                if (($cloudscape->end_date && $cloudscape->end_date < time()) ||
                     (!$cloudscape->end_date && $cloudscape->start_date < time())) {
                    $data['past_event'] = true;
                }
                
                $data['attendees'] = $this->events_model->get_attendees($cloudscape_id);
            }
            
            $this->layout->view('cloudscape/view', $data);
            $this->cloudscape_model->log_view($cloudscape_id);         
        } else {
            $this->layout->view('notfound');            
        }
    }
    
    /**
     * Display the RSS feed for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function rss($cloudscape_id = 0) {
        $this->load->helper('xml');
        $cloudscape               = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        $data['cloudscape']       = $cloudscape;
        $data['clouds']           = $this->cloudscape_model->get_clouds($cloudscape_id, 10);
        $data['encoding']         = $this->config->item('charset');;
        $data['feed_name']        = $this->config->item('site_name').': '.
                                    $cloudscape->title;
        $data['feed_url']         = base_url().'/cloudscape/rss.'.$cloudscape_id;
        $data['page_description'] = $this->config->item('site_name').' in '.
                                    $cloudscape->title;
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email'); 
        header("Content-Type: application/rss+xml");       
        $this->load->view('rss/rss', $data);
    }
    
    /**
     * Add a new cloudscape 
     */
    function add() {
        // Check logged in 
        $user_id  = $this->db_session->userdata('id');        
        $this->auth_lib->check_logged_in(); 
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        $this->form_validation->set_rules('body', t("Description"), 'required'); 
        
        if ($this->input->post('submit')) {
            // Get the form data
            $cloudscape->title       = $this->input->post('title');
            $cloudscape->summary     = $this->input->post('summary');
            $cloudscape->body        = $this->input->post('body');
            $cloudscape->user_id     = $this->db_session->userdata('id');
            $cloudscape->twitter_tag = $this->input->post('twitter_tag');
            $start_date_str          = trim($this->input->post('start_date'));
            if ($start_date_str) {
                $cloudscape->start_date  = strtotime($start_date_str);
            } else {
                $cloudscape->start_date = null;
            }
            $end_date_str          = trim($this->input->post('end_date'));
            if ($end_date_str) {
                $cloudscape->end_date  = strtotime($end_date_str);
            } else {
                $cloudscape->end_date = null;
            }
            
            $cloudscape->location    = $this->input->post('location'); 
            $cloudscape->open = 0;
            
            // Check that we don't have an end date but no start date
            $end_date_but_not_start_date = false;
            if ($cloudscape->end_date && !$cloudscape->start_date) {
                $end_date_but_not_start_date = true;
            }
            
            $start_date_after_end_date = false;
            if ($cloudscape->end_date && ($cloudscape->end_date < $cloudscape->start_date)) {
                $start_date_after_end_date = true;
            }
            
            if (!$end_date_but_not_start_date && !$start_date_after_end_date 
                && $this->form_validation->run()) {
                // Moderate for spam   
                $cloudscape->moderate = $this->_moderate_cloudscape($cloudscape, $user_id);       
           
                $cloudscape_id = $this->cloudscape_model->insert_cloudscape($cloudscape);
                if (config_item('x_moderation') && $cloudscape->moderate) {
                    $data['item'] = 'cloudscape';
                    $data['continuelink'] = '/cloudscape/cloudscape_list';
                    $this->layout->view('moderate', $data);
                    return;                    
                }                

                redirect('/cloudscape/view/'.$cloudscape_id);
            } else {
                $data['cloudscape'] = $cloudscape;
            }
        }

        $data['new'] = true;
        $data['title'] = t("Create Cloudscape");
        $data['navigation'] = 'cloudscapes';
        $data['end_date_but_not_start_date'] = $end_date_but_not_start_date;
        $data['start_date_after_end_date']   = $start_date_after_end_date;        
        $this->layout->view('cloudscape/edit', $data);
    }
    
    /**
     * Edit an existing cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function edit($cloudscape_id = 0) {        
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');     

        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        $this->form_validation->set_rules('body', t("Description"), 'required');  
              
        if ($this->input->post('submit')) {
            $cloudscape->cloudscape_id = $cloudscape_id;
            $cloudscape->title         = $this->input->post('title');
            $cloudscape->summary       = $this->input->post('summary');
            $cloudscape->body          = $this->input->post('body');
            $cloudscape->twitter_tag   = $this->input->post('twitter_tag');
            $cloudscape->colour        = $this->input->post('colour');
            $cloudscape->location      = $this->input->post('location');                       
            $start_date_str            = trim($this->input->post('start_date'));
            $end_date_str              = trim($this->input->post('end_date'));            
            
            // Turn the start and end dates from strings into unix timestamps 
            if ($start_date_str) {
                $cloudscape->start_date  = strtotime($start_date_str);
            } else {
                $cloudscape->start_date = null;
            }

            if ($end_date_str) {
                $cloudscape->end_date  = strtotime($end_date_str);
            } else {
                $cloudscape->end_date = null;
            }
               
            // Check that we don't have an end date but no start date
            $end_date_but_not_start_date = false;
            if ($cloudscape->end_date && !$cloudscape->start_date) {
                $end_date_but_not_start_date = true;
            }
            
            $start_date_after_end_date = false;
            if ($cloudscape->end_date && ($cloudscape->end_date < $cloudscape->start_date)) {
                $start_date_after_end_date = true;
            }
            
            if (!$end_date_but_not_start_date && !$start_date_after_end_date 
                && $this->form_validation->run()) {
                // Moderate for spam 
                $cloudscape->moderate = $this->_moderate_cloudscape($cloudscape, $user_id);
                               
                $this->cloudscape_model->update_cloudscape($cloudscape);
                
                if (config_item('x_moderation') && $cloudscape->moderate) {
                    $data['item'] = 'cloudscape';
                    $data['continuelink'] = '/cloudscape/cloudscape_list';
                    $this->layout->view('moderate', $data);
                    return;                     
                }                 
                
                redirect('/cloudscape/view/'.$cloudscape_id);
            } else {
                $data['cloudscape'] = $cloudscape;
            }
        }

         if (!isset($data['cloudscape'])) {
            $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
         }

         $data['new'] = false;
         $data['title'] = t("Edit Cloudscape");
         $data['navigation'] = 'cloudscapes';
         $data['end_date_but_not_start_date'] = $end_date_but_not_start_date;
         $data['start_date_after_end_date']   = $start_date_after_end_date;
         $this->layout->view('cloudscape/edit', $data);
    }
    
    /**
     * Display list of followers of a user 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function followers($cloudscape_id = 0) {
       $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
       $data['users'] = $this->cloudscape_model->get_followers($cloudscape_id);
       $data['title']  = t("Followers for the cloudscape !title", 
                         array('!title'=>$data['cloudscape']->title));
       $this->layout->view('cloudscape/followers_list', $data); 
    }    
    
    /**
     * Display list of attendees for an event clousdcape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function attendees($cloudscape_id = 0) {
       $this->load->model('events_model');
       $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
       $data['users'] = $this->events_model->get_attendees($cloudscape_id);
       $data['title']  = t("Attendess for the cloudscape !title", 
                         array('!title'=>$data['cloudscape']->title));
       $this->layout->view('events/attendees_list', $data); 
    }

    /**
     * Delete a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function delete($cloudscape_id = 0) {
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');  
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        
        // If confirmation form submitted delete the cloudscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
            $this->cloudscape_model->delete_cloudscape($cloudscape_id);
            $this->layout->view('cloudscape/delete_success', $data);
        } else {
            $data['title'] = t("Delete Cloudscape");
            $data['navigation'] = 'cloudscapes';
            $data['cloudscape_id'] = $cloudscape_id;
            $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
            $this->layout->view('cloudscape/delete_confirm', $data);
        }
    }
    
    /**
     * Add a cloud to a cloudscape
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function add_cloud($cloud_id = 0, $cloudscape_id = 0) {        
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');   
        
        // If search form submitted, search for users 
        if ($this->input->post('submit')) {
            $search_string = $this->input->post('search_string', TRUE);
            $data['cloudscapes'] = $this->cloudscape_model->search_post_permission($user_id,                                    $search_string);  
            $data["search_string"] = $search_string;        
        } elseif ($cloudscape_id) {
            $this->cloudscape_model->check_post_permission($cloudscape_id, $user_id);
            $this->cloudscape_model->add_cloud($cloudscape_id, $cloud_id);
            redirect('/cloud/view/'.$cloud_id);           
        }
        $data['title'] = t("Add Cloud to Cloudscapes");
        $data['navigation'] = 'cloudscapes';
        $this->load->model('cloud_model');
        $data['cloud'] = $this->cloud_model->get_cloud($cloud_id);
        $data['recent_cloudscapes'] = 
                      $this->cloudscape_model->get_recently_viewed_cloudscapes($user_id, 10);
        $this->layout->view('cloudscape/add_cloud', $data);           
    }
    
    /**
     * Remove a cloud from a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $cloud_id The ID of the cloud
     */
    function remove_cloud($cloudscape_id = 0, $cloud_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id'); 
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        $this->cloudscape_model->remove_cloud($cloudscape_id, $cloud_id);    
        redirect('/cloudscape/manage_clouds/'.$cloudscape_id);    
    }

    /**
     * Display page for managing clouds
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function manage_clouds($cloudscape_id = 0) {
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id'); 
       $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
       $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
       $data['clouds'] = $this->cloudscape_model->get_clouds($cloudscape_id);   
       $this->layout->view('cloudscape/manage_clouds', $data);
    }
    
    /**
     * Follow a cloudscape 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function follow($cloudscape_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id = $this->db_session->userdata('id');        
        $this->cloudscape_model->follow($cloudscape_id, $user_id);
        redirect(base_url().'cloudscape/view/'.$cloudscape_id);
    }
    
    /**
     * Unfollow a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function unfollow($cloudscape_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id = $this->db_session->userdata('id');
        $this->cloudscape_model->unfollow($cloudscape_id, $user_id);
        redirect(base_url().'cloudscape/view/'.$cloudscape_id);        
    }    
    
    /**
     * Manage permissions for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function permissions($cloudscape_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        
        // If search form submitted, search for users 
        if ($this->input->post('submit')) {
            $user_search_string = $this->input->post('user_search_string', TRUE);
            $data['users'] = $this->user_model->search($user_search_string);  
            $data["user_search_string"] = $user_search_string;        
        } 

        // Get the information to display existing permissions
        $data['title'] = t("Manage Cloudscape Permissions");
        $data['navigation'] = 'cloudscapes';
        $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        $data['admins'] = $this->cloudscape_model->get_admins($cloudscape_id);
        $data['posters'] = $this->cloudscape_model->get_posters($cloudscape_id); 

        $this->layout->view('cloudscape/permissions', $data);
    }
    
    /**
     *  Add a user as an admin of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $add_user_id The ID of the user to add
     */
    function admin_add($cloudscape_id = 0, $add_user_id = 0) {
        // Check permissions        
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);

        // Add the user as an admin and redirect to the permissions page
        $this->cloudscape_model->add_admin($cloudscape_id, $add_user_id);
        redirect('cloudscape/permissions/'.$cloudscape_id);
        
    }
    
    /**
     * Remove a user as an admin of a cloudscape 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $remove_user_id The ID of the user to remove
     */
    function admin_remove($cloudscape_id = 0, $remove_user_id = 0) {        
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');          
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        
        // Remove the user as an admin and redirect to the permissions page
        $this->cloudscape_model->remove_admin($cloudscape_id, $remove_user_id);
        redirect('cloudscape/permissions/'.$cloudscape_id);  
    }
    
    /**
     * Add a user as a poster of a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $add_user_id The ID of the user to add
     */
    function poster_add($cloudscape_id =0, $add_user_id = 0) {
        // Check permissions        
        $user_id  = $this->db_session->userdata('id');
        $this->auth_lib->check_logged_in();      

        // Add the user as poster and redirect to permissions page
        $this->cloudscape_model->add_poster($cloudscape_id, $add_user_id);
        redirect('cloudscape/permissions/'.$cloudscape_id);
        
    }
    
    /**
     * Remove a user as a poster of a cloudscape 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $remove_user_id The ID of the user to remove
     */
    function poster_remove($cloudscape_id = 0, $remove_user_id = 0) {                
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);

        // Remove the user as a poster and redirect to the permissions page
        $this->cloudscape_model->remove_poster($cloudscape_id, $remove_user_id);
        redirect('cloudscape/permissions/'.$cloudscape_id);
        
    }

    /**
     * Edit a Cloudscape picture 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function edit_picture($cloudscape_id = 0) {
        $cloudscape = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        // Check user logged in
        $this->auth_lib->check_logged_in();        
        $user_id = $this->db_session->userdata('id');      
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);

        $data['user_id'] = $user_id;
        $data['cloudscape_id'] = $cloudscape_id;
        $upload_path = $this->config->item('upload_path_cloudscape');
        if ($this->input->post('submit')) {
            $data['image_attr_name'] = $this->input->post('attr_name');
            $data['image_attr_link'] = $this->input->post('attr_link');

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size']  = '1500';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload()) {
                $data['error'] = $this->upload->display_errors();
                $data['image_path']= $cloudscape->image_path;
                $this->layout->view('cloudscape/edit_picture', $data);
            } else {
                $upload_data = $this->upload->data();
                $data = array('upload_data' => $upload_data);
                $data['error'] = NULL;
                $data['image_path']      = $upload_data['file_name'];
                $data['image_attr_name'] = $this->input->post('attr_name');
                $data['image_attr_link'] = $this->input->post('attr_link');
                $data['cloudscape_id']   = $cloudscape_id;

                // Create the smaller image, 240x180, 256x192.
                $file_name       = $upload_data['file_name'];
                $original_height = $upload_data["image_height"]; 
                $original_width  = $upload_data["image_width"];
                $this->load->library('image_lib');
                $result = $this->image_lib->resize_to_fit_panel($upload_path.$file_name, 
                                                          $original_height, $original_width);
                $data['error'] .= $this->image_lib->display_errors();
                $data['cloudscape'] = $cloudscape;
                $data['title']      = t("Cloudscape picture edited");
                $this->cloudscape_model->update_picture($cloudscape_id, $data);
                $this->layout->view('cloudscape/edit_picture_success', $data);
            }
        } else {
            $data['cloudscape'] = $cloudscape;
            $this->layout->view('cloudscape/edit_picture', $data);
        }
    }
    /**
     *  Make a cloudscape open for anybody to add clouds to 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function open($cloudscape_id = 0) {
        // Check permisisons
        $this->auth_lib->check_logged_in();   
        $user_id  = $this->db_session->userdata('id');      
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        
        // Make the cloudscape open and redirect to the permissions page
        $this->cloudscape_model->set_open($cloudscape_id);
        redirect('cloudscape/permissions/'.$cloudscape_id);
        
    }

    /*  
     * Make a cloudscape not open for anybody to add clouds to 
     * Still possible to individually specify people who can add clouds to the cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function close($cloudscape_id = 0) {
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);

        // Set the cloudscape closed and redirect to the permissions page
        $this->cloudscape_model->set_closed($cloudscape_id);
        redirect('cloudscape/permissions/'.$cloudscape_id);
    }
    
    /**
     * View and edit sections for the cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function manage_sections($cloudscape_id = 0) {          
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);

        // Get the sections and clouds in the sections to display       
        $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        $sections   = $this->cloudscape_model->get_sections($cloudscape_id);
        if ($sections) {
            foreach ($sections as $section) {
                $cloud_sections[$section->section_id] = 
                        $this->cloudscape_model->get_clouds_in_section($section->section_id);
            }
        }
        
        // Display the page
        $data['sections']       = $sections;
        $data['cloud_sections'] = $cloud_sections;
        $this->layout->view('cloudscape/manage_sections', $data);
    }
    
    /**
     * Add a section to a cloudscape
     * Note: Currently not limiting the number of sections for a cloudscape, but might need 
     * to. 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function section_add($cloudscape_id = 0) {        
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        
        // Set form validation rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required'); 
        
        // If form submitted then create the section and redirect to the main manage sections
        // page      
        if ($this->input->post('submit')) {
           if ($this->form_validation->run()) {
               $title = $this->input->post('title');
               $this->cloudscape_model->create_section($cloudscape_id, $title);
               redirect(base_url().'cloudscape/manage_sections/'.$cloudscape_id);
           }
        }
        
        // Otherwise display the form
        $this->layout->view('cloudscape/add_section', $data); 
    }
    
    /**
     * Remove a section from a cloudscape. This also removes the clouds from the section but 
     * does not delete the clouds
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $section_id The ID of the section
     */
    function section_remove($cloudscape_id = 0, $section_id = 0) {         
         // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        if (!$this->cloudscape_model->section_belongs_to_cloudscape($cloudscape_id, 
                                                                    $section_id)) {
            show_error(t('You do not have permission to view this page.'));
        }
        
       // If form submitted, delete the section and redirect to the main manage sections page 
       if ($this->input->post('submit')) {
           $this->cloudscape_model->delete_section($section_id); 
           redirect(base_url().'cloudscape/manage_sections/'.$cloudscape_id);
       }   
       
       // Show a page to confirm the deletion
       $data['section'] = $this->cloudscape_model->get_section($section_id);            
       $this->layout->view('cloudscape/remove_section_confirm', $data);  
    }
    
    /**
     * Rename a section
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $section_id The ID of the section
     */
    function section_rename($cloudscape_id = 0, $section_id = 0) {
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);       
        if (!$this->cloudscape_model->section_belongs_to_cloudscape($cloudscape_id, 
                                                                    $section_id)) {
            show_error(t("You do not have permission to view this page."));
        }
        
        // Set up the form validation rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        
        // If form submitted and validated, rename the section and redirect to the main manage
        // sections page 
        if ($this->input->post('submit')) {
           if ($this->form_validation->run()) {
               $title = $this->input->post('title');
               $this->cloudscape_model->rename_section($section_id, $title); 
               redirect(base_url().'cloudscape/manage_sections/'.$cloudscape_id);
           }
        }      
        
        $data['section'] = $this->cloudscape_model->get_section($section_id); 
        $this->layout->view('cloudscape/rename_section', $data);  
    }
    
    /**
     * Add clouds to a section of a cloudscape.
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function cloud_section_add($cloudscape_id = 0) {  
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);  
        
        // If the form is submitted and validated the add each of the clouds specified to 
        // the section and then redirect to the main manage sections page
        if ($this->input->post('submit')) {
            $section_id = $this->input->post('section_id');
            $clouds     = $this->input->post('clouds');
            if (!$this->cloudscape_model->section_belongs_to_cloudscape($cloudscape_id, 
                                                                        $section_id)) {
                show_error(t("You do not have permission to view this page."));
            }    
                    
            foreach ($clouds as $cloud_id) {
                if (!$this->cloudscape_model->is_cloud_in_section($cloud_id, $section_id)) {
                    $this->cloudscape_model->add_cloud_to_section($section_id, $cloud_id);
                }
        
            }
            redirect(base_url().'cloudscape/manage_sections/'.$cloudscape_id);
        }
        
        // Otherwise display the page for adding clouds to a section    
        $data['sections'] = $this->cloudscape_model->get_sections($cloudscape_id);
        $data['clouds']   = $this->cloudscape_model->get_clouds($cloudscape_id);
        $this->layout->view('cloudscape/add_cloud_section', $data);    
    }
    
    /**
     * Remove a cloud from a section
     * Note this should really use a POST not a GET as it changes state. 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $section_id The ID of the section
     * @param integer $cloud_id The ID of the cloud
     */
    function cloud_section_remove($cloudscape_id = 0, $section_id = 0, $cloud_id = 0) {       
        // Check permissions
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id); 
        if (!$this->cloudscape_model->section_belongs_to_cloudscape($cloudscape_id, 
                                                                    $section_id)) {
            show_error(t("You do not have permission to view this page."));
        }
        
        // Remove the cloud 
        $this->cloudscape_model->remove_cloud_from_section($section_id, $cloud_id); 
        
        // Redirect to the main manage sections page
        redirect(base_url().'cloudscape/manage_sections/'.$cloudscape_id);     
    }
       
    /**
     * Hide a cloudscape from the main site cloudstream and from the list of new cloudscapes
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function hide($cloudscape_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $this->cloudscape_model->hide_cloudscape($cloudscape_id);
        redirect('/cloudscape/view/'.$cloudscape_id);
    }
    
    /**
     * Mark as attending a cloudscape that is an event
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function attend($cloudscape_id = 0) {
       $this->load->model('events_model');
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id');
       $this->events_model->attend($cloudscape_id, $user_id);
       redirect(base_url().'cloudscape/view/'.$cloudscape_id);  
    }
    
    /**
     * Mark as not attending a cloudscape that is an event
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function unattend($cloudscape_id = 0) {
       $this->load->model('events_model');
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id');
       $this->events_model->unattend($cloudscape_id, $user_id);
       redirect(base_url().'cloudscape/view/'.$cloudscape_id);
    }
    
    /**
     * Add this cloudscape as a favourite for this user
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function favourite($cloudscape_id = 0) {
       $this->load->model('favourite_model'); 
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id');
        $can_favourite = $this->favourite_model->can_favourite($user_id, $cloudscape_id, 
                                                               'cloudscape');
       if (!$can_favourite) {
           show_error('You cannot add this item as a favourite - either you created the item 
                       yourself, you have already favourited it or you do not have high 
                       enough reputation on the site to add favourites.');
       }      
       $this->favourite_model->add_favourite($user_id, $cloudscape_id, 'cloudscape');
       redirect(base_url().'cloudscape/view/'.$cloudscape_id);
    }
      
     /**
      * Remove this cloudscape as a favourite for this user
      *
     * @param integer $cloudscape_id The ID of the cloudscape
      */
    function unfavourite($cloudscape_id = 0) {
       $this->load->model('favourite_model'); 
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id');
       $this->favourite_model->remove_favourite($user_id, $cloudscape_id, 'cloudscape');
       redirect(base_url().'cloudscape/view/'.$cloudscape_id);
    }
       
    /**
     * Show the users who have favourited the cloudscapes
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function favourited($cloudscape_id = 0) {
        $this->load->model('favourite_model');
        $data['cloudscape'] = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        $data['users']      = $this->favourite_model->get_users_favourited($cloudscape_id, 
                                                                           'cloudscape');
        $this->layout->view('cloudscape/favourited', $data);
    } 
    
    /**
     * Form and form processing for a cloudscape admin to email all the attendees of an event
     *
     * @param integer $cloudscape_id The ID of the cloudscape for the event
     */
    function email_attendees($cloudscape_id = 0) {
        $this->load->model('events_model');
        
        // Check permissions, check cloudscape is an event (and therefore also
        // a valid cloudscape), check feature flag set 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
        if (!$this->config->item('x_email_events_attending') || 
            !$this->events_model->is_event($cloudscape_id)) {
            show_404();
        }
        // As an anti-spam measure, check that the user hasn't sent too many e-mails to 
        // attendees of this particular event in the last hour
        if ($this->events_model->check_email_limit_exceeded($user_id, $cloudscape_id)) {
            show_error(t('You are only allowed to send !count emails per hour to attendees of 
                          a specific event. Please wait and send this e-mail later.', 
            array('!count' => $this->config->item(email_event_attending_limit_per_hour))));
        }
        
        $cloudscape = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('subject', t("Subject"), 'required|max_length[100]');
        $this->form_validation->set_rules('body', t("Message"), 'required');         
        
        // If form submitted send out the e-mail
        if ($this->input->post('submit') && $this->form_validation->run()) {
            $subject            = $this->input->post('subject');
            $body               = $this->input->post('body');
            $data['subject']    = $subject; 
            $data['body']       = $body;
            $data['cloudscape'] = $cloudscape;
            $data['title']      = t('E-mail Sent');
            $data['navigation'] = 'cloudscapes';
            
            // Store a copy of the e-mail sent for logging purposes and for checking timestamps
            // (not used to repopulate the form on next use) 
            $this->events_model->insert_event_email($cloudscape_id, $user_id, $subject, $body);
            
            $sender = $this->user_model->get_user($user_id);
            $data['sender']     = $sender;
            $message = $this->load->view('email/event_attendees', $data, true);
              
            // Get the list of people to send the message too by getting the list of attendees
            // and filtering them based on their e-mail preferences 
            $email_list = $this->events_model->get_attendees_email($cloudscape_id);
        
            foreach ($email_list as $email) {
                // only send the e-mail if it's the live site otherwise send it to the site 
                // e-mail address for debug purposes 
                $to = $this->config->item('x_live')? $email->email : 
                                                    $this->config->item('site_email');
                send_email($to, $this->config->item('site_email'), $subject, $message);
        	}                                                                                                                     
             // Display success message 
            $this->layout->view('cloudscape/email_attendees_success', $data);
        } else {
            // Otherwise display the form 
            
            // If we have reached the form because of validation errors, pass the data 
            // entered in the form back to the form so that the user does not have to reenter 
            // the data. 
            $email->subject          = $this->input->post('subject'); 
            $email->body             = $this->input->post('body');
            
            $data['email']           = $email;
            $data['total_attendees'] = $this->events_model->get_total_attendees(
                                                                               $cloudscape_id);
            $data['title']           = t("E-mail Attendees");
            $data['navigation']      = 'cloudscapes';
            $data['cloudscape']      = $cloudscape;
            $this->layout->view('cloudscape/email_attendees', $data);
        }
    }
    
    /**
     * Check if a cloudscape has a high likelihood of containing spam 
     *
     * @param object$cloudscape The cloudscape
     * @param  integer $user_id The id of the user adding or editting the cloudscape
     * @return boolean TRUE if the cloudscape is likely to contain spam and should be 
     * moderated, FALSE otherwise
     */
    function _moderate_cloudscape($cloudscape, $user_id) {
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
            $user = $this->user_model->get_user($user_id); 
            if (!$user->whitelist) {
                $this->load->library('mollom');
                try {
                	$spam_status = $this->mollom->checkContent($cloudscape->title, $cloudscape->summary.' '.$cloudscape->body); 	  
	                if ($spam_status['quality'] < 0.5) { 
	                    $moderate = TRUE;     
	                }    
                } catch (Exception $e) {
                    
                }
            }
        }
        return $moderate;  		
    }
}