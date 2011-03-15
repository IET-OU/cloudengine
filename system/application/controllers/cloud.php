<?php 
/**
 * Controller for cloud-related functionality 
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Cloud
 */
class Cloud extends MY_Controller {

    function Cloud() {
        parent::MY_Controller();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('format');
        $this->load->model('cloud_model');
        $this->load->model('tag_model');
        $this->load->library('layout', 'layout_main'); 
        $this->load->model('user_model'); 
    }
    
    /**
     * Display a paged list of all the clouds on the site starting with 
     * a specified letter of the alphabet
     *
     * @param string $alpha The letter of the alphabet
     */
    function cloud_list($alpha = 'A') {
        $data['title']      = 'Clouds';
        $data['navigation'] = 'clouds';
        $data['alpha']      = $alpha;
        $data['clouds']     = $this->cloud_model->get_clouds_alpha($alpha);
        $data['new_clouds'] = $this->cloud_model->get_new_clouds(15);

        $this->layout->view('cloud/list', $data);
    }

    /**
     * View a specific cloud
     *
     * @param mixed $cloud Either the ID of the cloud or the title of the cloud (URL-encoded)
     * @param string $view The type of items to display in the tabbed section i.e. 'comments',
     * 'links' or 'references'. 
     */
    function view($cloud = 0, $view = 'comments') {
        // The URL may specify either the cloud ID or cloud name, find out which
        $user_id  = $this->db_session->userdata('id');
        $this->load->model('comment_model');
        $this->load->model('content_model');
        $this->load->model('link_model');
        $this->load->model('embed_model');
        $this->load->model('favourite_model');

        // Get the cloud
        if (is_numeric($cloud)) {
            $data['cloud'] = $this->cloud_model->get_cloud($cloud);
            $cloud_id      = $cloud;
        } else {           
            $data['cloud'] = $this->cloud_model->get_cloud_by_title(urldecode($cloud));
            $cloud_id      = $data['cloud']->cloud_id;
        }

        $cloud = $data['cloud'];

         // Set up form validation for the comment form 
        $this->load->library('form_validation');
        ///Translators: Form field names.
        $this->form_validation->set_rules('body', t("Comment"), 'required');
        
        // See if the comment form has been submitted
        if ($this->input->post('submit')) {
            $this->auth_lib->check_logged_in();
            $cloud_id = $this->input->post('cloud_id');
            if (!is_numeric($cloud_id)) {
                show_error(t("An error occurred."));
            }
            
            if ($this->form_validation->run()) {
                // Get the form data 
                $body     = $this->input->post('body');
                
                // Moderate for spam
                $moderate = $this->_moderate_comment($body, $user_id); 

                // Add the comment
                $comment_id = $this->comment_model->insert_comment($cloud_id, $user_id, $body,                                                                   $moderate);
                
                // If moderated, tell the user, otherwise send notifications and redirect 
                if (config_item('x_moderation') && $moderate) {
                	$data['item']         = 'comment';
                    $data['continuelink'] = '/cloud/view/'.$cloud_id;
                    $this->layout->view('moderate', $data);
                    return;
                } else {                  
                    redirect('/cloud/view/'.$cloud_id); // Return to the main cloud view page 
                }
            }
        }
        
        // If the cloud exists, get all the other information needed for the page
        if ($data['cloud']->cloud_id) {
            
            // For each comment figure out if the current user has edit permission for that
            // comment and add the information to the comment 
            $comments = $this->comment_model->get_comments($cloud_id);
            if ($comments) {
                foreach ($comments as $comment) {
                    $comment->edit_permission = 
                                         $this->comment_model->has_edit_permission($user_id, 
                                                                        $comment->comment_id);
                    $modified_comments[] = $comment;
                }
            }
            
            // Do the same for content 
            $contents = $this->content_model->get_content($cloud_id);
            if ($contents) {
                foreach ($contents as $content) {
                    $content->edit_permission = 
                                          $this->content_model->has_edit_permission($user_id, 
                                                                        $content->content_id);
                    $modified_contents[] = $content;
                }
            }
            
            // Do the same for links 
            $links = $this->link_model->get_links($cloud_id);
            if ($links) {
                foreach ($links as $link) {
                    $link->edit_permission = 
                             $this->link_model->has_edit_permission($user_id, $link->link_id);
                    $link->show_favourite_link  = 
                                        $this->favourite_model->can_favourite_item($user_id, 
                                                                      $link->link_id, 'link');

                    $modified_links[] = $link;
                }
            }  

            // Do the same for links 
            $embeds = $this->embed_model->get_embeds($cloud_id);
            if ($embeds) {
                foreach ($embeds as $embed) {
                    $embed->edit_permission = $this->embed_model->has_edit_permission($user_id,                                                                             $embed->embed_id);
                    $modified_embeds[] = $embed;
                }
            }   
            
            // Get the data for gadgets for this cloud
            if ($this->config->item('x_gadgets')) {
                $this->load->model('gadget_model');
                // Get the gadgets added to this cloud, and the gadgets added to all the cloud 
                // owner's clouds - we can't combine the lists as the links for deleting the 
                // gadgets are slightly different for each
                $data['gadgets_cloud'] = $this->gadget_model->get_gadgets_for_cloud($cloud_id); 
                $data['gadgets_user'] = $this->gadget_model->get_gadgets_for_user(
                                                                              $cloud->user_id);
                $data['current_user_id'] = $user_id;      
            }
            
            // We need to log the view before we calculate the number of views 
            $this->cloud_model->log_view($cloud_id);  
            
            $data['total_views']      = $this->cloud_model->get_total_views($cloud_id);
            $data['comments']         = $modified_comments;
            $data['total_comments']   = $this->comment_model->get_total_comments($cloud_id);
            $data['tags']             = $this->tag_model->get_tags('cloud', $cloud_id);
            $data['links']            = $modified_links;
            $data['references']       = $this->cloud_model->get_references($cloud_id);
            $data['contents']         = $modified_contents;
            $data['embeds']           = $modified_embeds;
            $data['cloudscapes']      = $this->cloud_model->get_cloudscapes($cloud_id);
            $data['total_favourites'] = $this->favourite_model->get_total_favourites($cloud_id,                                                                                      'cloud');
            $data['favourite']        = $this->favourite_model->is_favourite($user_id, 
                                                                           $cloud_id, 'cloud');
            $data['show_favourite_link']   = $this->favourite_model->can_favourite_item(
                                                                 $user_id, $cloud_id, 'cloud');
            $data['title']            = $data['cloud']->title;
            $data['navigation']       = 'clouds';
            $data['edit_permission']  = $this->cloud_model->has_edit_permission($user_id, 
                                                                                    $cloud_id);
            $data['admin']            = $this->auth_lib->is_admin();
            $data['following']        = $this->cloud_model->is_following($cloud_id, $user_id);
            $data['view']             = $view;
            $this->layout->view('cloud/view', $data);

        } else { 
            // If invalid cloud id, display error page
            show_error(t("An error occurred."));
        }
    }
    
    /**
     * Hide a cloud from the main site cloudstream and from the list of new clouds
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function hide($cloud_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $this->cloud_model->hide_cloud($cloud_id);
        redirect('/cloud/view/'.$cloud_id);
    }
    
    /**
     * Add a new cloud, either standalone or also add it to a cloudscape if specified 
     *
     * @param integer $cloudscape_id The ID of the cloudscape to add the new cloud to, 
     * or FALSE if it should not be added to a cloudscape
     */
    function add($cloudscape_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        

        $this->load->model('cloudscape_model');
        $this->load->model('link_model');
        
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        
        // If add cloud form has been submitted then process it
        if ($this->input->post('submit')) {

            // Get the information from the form and add the cloud
            $cloud->title        = $this->input->post('title');
            $cloud->body         = $this->input->post('body');
            $cloud->primary_url  = $this->input->post('url');
            $cloud->user_id      = $user_id;
          
            if ($this->form_validation->run()) {
	            // Moderate for spam 
	            $cloud->moderate = $this->_moderate_cloud($cloud, $user_id);
	                 
                // Add the cloud 
                $cloud_id = $this->cloud_model->insert_cloud($cloud);

                if (config_item('x_moderation') && $cloud->moderate) {
                    $data['title']= t('Your !item is being moderated', array('!item'=>'cloud'));
                    $data['item'] = 'cloud';
                    $data['continuelink'] = site_url('cloud/cloud_list');
                    $this->layout->view('moderate', $data);
                    return;
                }
                
                if ($cloud_id) {
                    // If a cloudscape has been specified also add the cloud to that
                    //  cloudscape
                    $cloudscape_id = $this->input->post('cloudscape_id');
                    if ($cloudscape_id) {
                        $this->cloudscape_model->check_post_permission($cloudscape_id, 
                                                                       $user_id);
                        $this->cloudscape_model->add_cloud($cloudscape_id, $cloud_id);

                    }
                   
                    // Redirect to the page for the new cloud
                    $cloud->cloud_id = $cloud_id;
                    $data['cloud'] = $cloud;
                    $this->layout->view('cloud/cloud_added', $data);
                    return;
                } else {
                    show_error(t("An error occurred adding the new cloud.")); 
                }
            } else {
                $data['cloud'] = $cloud;
            }
        }
		$data['cloudscape_id'] = $cloudscape_id;
        // Check to see if a cloudscape id has been specified
        if ($data["cloudscape_id"]) {
            $data["cloudscape"] = $this->cloudscape_model->get_cloudscape(
                                                                       $data["cloudscape_id"]);
        }
        $data['new']   = true;
        $data['title'] = t("Create Cloud");
        $data['navigation'] = 'clouds';

        // Display the form for creating a cloud
        $this->layout->view('cloud/edit', $data);

    }
    
    /**
     * Move a link in the links section for a cloud to the main link for a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $link_id The ID of the link 
     */
    function make_link_primary($cloud_id = 0, $link_id = 0) {
    	$this->auth_lib->check_is_admin(); 
        $this->load->model('link_model');
        $this->link_model->make_link_primary($cloud_id, $link_id);
        redirect(base_url().'cloud/view/'.$cloud_id.'/links#contribute');
    }
    
    /**
     * Edit an existing cloud 
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function edit($cloud_id = 0) {
        $user_id  = $this->db_session->userdata('id');
        
        $this->cloud_model->check_edit_permission($user_id, $cloud_id);
        
        // Set up form validation rules (empty rules needed for set_value() 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');

        if ($this->input->post('submit')) {
            // Get the form data 
            $cloud->cloud_id    = $cloud_id;
            $cloud->title       = $this->input->post('title');
            $cloud->body        = $this->input->post('body');
            $cloud->summary     = $this->input->post('summary');
            $cloud->primary_url = $this->input->post('url');
            $call_deadline_str  = trim($this->input->post('call_deadline'));
            if ($call_deadline_str) {
                $cloud->call_deadline  = strtotime($call_deadline_str);
            } else {
                $cloud->call_deadline = null;
            }
            
            // Validate the data, if fine, update the cloud and redirect to the cloud page,
            // otherwise keep the submitted ata to repopulate the form
            if ($this->form_validation->run()) {
            	// Moderate for spam
            	$cloud->moderate = $this->_moderate_cloud($cloud, $user_id);  
            	                  
                $this->cloud_model->update_cloud($cloud);
                
                if (config_item('x_moderation') && $cloud->moderate) {
                    $data['title']= t('Your !item is being moderated', array('!item'=>'cloud'));
                    $data['item'] = 'cloud';
                    $data['continuelink'] = site_url('cloud/cloud_list');
                    $this->layout->view('moderate', $data);
                    return;                     
                }
                redirect('/cloud/view/'.$cloud_id);
            } else {
                $data['cloud'] = $cloud;
            }
        } 
        
        // If no data already set from invalid form submission, get the data for the cloud
        if (!isset($data['cloud'])) {
            $data['cloud'] = $this->cloud_model->get_cloud($cloud_id);
        }
        
        $data['new']   = false; 
        $data['title'] = t("Edit Cloud");
        $data['navigation'] = 'clouds';
        
        // Display the edit form 
        $this->layout->view('cloud/edit', $data); 
    }

    /**
     * Delete a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function delete($cloud_id = 0) {        
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');      

        $this->cloud_model->check_edit_permission( $user_id, $cloud_id);
        
        // If confirmation form submitted delete the cloudscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $data['cloud'] = $this->cloud_model->get_cloud($cloud_id);
            $this->cloud_model->delete_cloud($cloud_id);
            $this->layout->view('cloud/delete_success', $data);
        } else {
            $data['title'] = t("Delete Cloud");
            $data['navigation'] = 'clouds';
            $data['cloud_id'] = $cloud_id;
            $data['cloud'] = $this->cloud_model->get_cloud($cloud_id);
            $this->layout->view('cloud/delete_confirm', $data);
        }
    }
    
    /**
     * Add a link to a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function add_link($cloud_id = 0) {
        $this->auth_lib->check_logged_in();

        $user_id  = $this->db_session->userdata('id');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        $this->form_validation->set_rules('url', t("URL"), 
                                             'valid_url|callback_does_not_use_url_shortener');
        $this->load->model('link_model');
        
        if ($this->input->post('submit')) {

            if ($this->form_validation->run()) {
                // Get the form data 
                $url = $this->input->post('url');
                $title = $this->input->post('title');
                
                // Moderate for spam
                $moderate = $this->_moderate_link($title, $url, $user_id);   
                
                $duplicate = $this->link_model->is_duplicate($cloud_id, $url);
                
                if (!$duplicate) {

                    $this->link_model->add_link($cloud_id, $url, $title, $user_id, $moderate);
           
                    // If moderated, tell the user 
                    // Otherwise redirect 
                    if (config_item('x_moderation') && $moderate) {
                    	$data['item'] = 'link';
                        $data['continuelink'] = '/cloud/view/'.$cloud_id;
                        $this->layout->view('moderate', $data);
                        return;
                    } else {                  
                        // Return to the main cloud view page    
                        redirect('/cloud/view/'.$cloud_id.'/links#contribute'); 
                    }  
                }              
            }
        }       
        
        // Display the add link form
        $data['cloud']      = $this->cloud_model->get_cloud($cloud_id); 
        $data['duplicate']  = $duplicate;
        $data['title']      = t("Add Link");
        $data['new']        = true;
        $this->layout->view('link/edit', $data);         
    }

    /**
     * Determines if a URL uses a URL shortener
     *
     * @param string $url The URL ot check
     * @return boolean TRUE if does not use a URL shortner, FALSE otherwise
     */
    function does_not_use_url_shortener($url) {
        $real_url = true;
        
        $regex = "/.*(bit\.ly|tinyurl\.com|is\.gd|tr\.im|ow\.ly|twurl\.nl).*/";
        if (preg_match($regex, $url)) {
            $real_url = false;
            $this->form_validation->set_message('does_not_use_url_shortener', 
                t("The URL you have specified uses a URL shortener. Please give the original URL
instead since URLs from URL shorteners may not exist forever."));
        }
        
        return $real_url;
    }    
    
    /**
     * Delete a link from a cloud
     *
     * @param integer $link_id The ID of the link 
     */
    function delete_link($link_id = 0) {
        $this->load->model('link_model');
        // Check if the user has edit permission for the cloud that this link belongs to
		$this->auth_lib->check_logged_in();
        $link = $this->link_model->get_link($link_id);
        $user_id  = $this->db_session->userdata('id');
        $this->cloud_model->check_edit_permission($user_id, $link->cloud_id);
           
         // If confirmation form submitted delete the link, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->load->model('link_model');
            $this->link_model->delete_link($link_id);
            redirect('/cloud/view/'.$link->cloud_id.'/links#contribute');
        } else {
            $data['title'] = t("Delete Link");
            $data['link'] = $link;
            $data['cloud'] = $this->cloud_model->get_cloud($link->cloud_id);
            $this->layout->view('link/delete_confirm', $data);
        }       
        
    }
    
    /**
     * Edit a link for a cloud
     *
     * @param integer $link_id The ID of the link 
     */
    function edit_link($link_id = 0) {
        $this->load->model('link_model');
        // Check if the user has edit permission for the cloud that this link belongs to
        $this->auth_lib->check_logged_in();

        $link = $this->link_model->get_link($link_id);
        if ($this->input->post('submit')) {
            $title = $this->input->post('title');
            $url = $this->input->post('url');
            $this->link_model->update_link($link_id, $url, $title);
            redirect('/cloud/view/'.$link->cloud_id.'/links#contribute'); 
        } else {
            $user_id       = $this->db_session->userdata('id'); 
            $data['cloud'] = $this->cloud_model->get_cloud($link->cloud_id);  
            $data['title'] = t("Edit Link"); 
            $data['link']  = $link;
            $this->layout->view('link/edit', $data);
        }
    }   
  
    /**
     * Add a reference to a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function add_reference($cloud_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('reference_text', t("Reference"), 'required');
        
        if ($this->input->post('submit')) {
            if ($this->form_validation->run()) {
                $reference_text = $this->input->post('reference_text');
                // Moderate for spam and then add
                $moderate = $this->_moderate_reference($reference_text, $user_id);
                $this->cloud_model->add_reference($cloud_id, $reference_text, $user_id, 
                                                  $moderate);
                              
                // If moderated, tell the user, otherwise redirect 
                if ($moderate) {
                     $data['item'] = 'link';
                    $data['continuelink'] = '/cloud/view/'.$cloud_id;
                    $this->layout->view('moderate', $data);
                    return;
                } else {                  
                     // Return to the main cloud view page 
                    redirect('/cloud/view/'.$cloud_id.'/references#contribute');
                }                
            }
        }       
        
        // Display the add link form
        $data['cloud'] = $this->cloud_model->get_cloud($cloud_id); 
         
        $data['title'] = t("Add Reference");
        $this->layout->view('reference/add', $data);         
    }

    /**
     * Delete a reference from a cloud 
     *
     * @param integer $reference_id The ID of the reference
     */
    function delete_reference($reference_id = 0) {
        // Check if the user has edit permission for the cloud that this link belongs to
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->cloud_model->check_edit_permission($user_id, $reference->cloud_id);
           
         // If confirmation form submitted delete the link, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {

            $this->cloud_model->delete_reference($reference_id);
            redirect('/cloud/view/'.$reference->cloud_id.'/references#contribute');
        } else {
            $data['title']         = t("Delete Reference");
            $data['reference']     = $reference;
            $data['cloud'] = $this->cloud_model->get_cloud($reference->cloud_id);
            $this->layout->view('reference/delete_confirm', $data);
        }
    }

    /**
     * Follow a cloud 
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function follow($cloud_id = 0) {
        // Check logged in 
        $this->auth_lib->check_logged_in();
        
        // Follow the cloudscape
        $user_id = $this->db_session->userdata('id');        
        $this->cloud_model->follow($cloud_id, $user_id);
        
        // Redirect to the cloud page 
        redirect('/cloud/view/'.$cloud_id);
    }
    
    /**
     * Unfollow a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function unfollow($cloud_id = 0) {
        // Check logged in 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        

        // Unfollow the cloudscape
        $this->cloud_model->unfollow($cloud_id, $user_id);
        
        // Redirect to the cloudscape page
        redirect('/cloud/view/'.$cloud_id);        
    }        
    
    /**
     * Add this cloud as a favourite for this user
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function favourite($cloud_id = 0) {
       $this->load->model('favourite_model'); 
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id');
       $can_favourite = $this->favourite_model->can_favourite($user_id, $cloud_id, 'cloud');
       if (!$can_favourite) {
           show_error(t(
		   'You cannot add this item as a favourite - either you created the item yourself, you have
already favourited it or you do not have high enough reputation on the site to add favourites.'));
       }
       $this->favourite_model->add_favourite($user_id, $cloud_id, 'cloud');
       redirect('/cloud/view/'.$cloud_id);
    }
    
    /**
     * Remove this cloud as a favourite for this user.
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function unfavourite($cloud_id = 0) {
       $this->load->model('favourite_model'); 
       $this->auth_lib->check_logged_in();
       $user_id  = $this->db_session->userdata('id');
       $this->favourite_model->remove_favourite($user_id, $cloud_id, 'cloud');
       redirect('/cloud/view/'.$cloud_id);
    }    
    
    /**
     * Add a link on a cloud as a favourite for this user
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $link_id The ID of the link
     */
    function link_favourite($cloud_id = 0,  $link_id = 0) {
        $this->load->model('favourite_model'); 
		$this->auth_lib->check_logged_in(); 
        $user_id  = $this->db_session->userdata('id');
        $this->favourite_model->add_favourite($user_id, $link_id, 'link');
        redirect('/cloud/view/'.$cloud_id.'/links#contribute');
    }      

    /**
     * Display the people who have favourited this cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function favourited($cloud_id) {
        $this->load->model('favourite_model');      
        $data['cloud'] = $this->cloud_model->get_cloud($cloud_id);
        $data['users'] = $this->favourite_model->get_users_favourited($cloud_id, 'cloud');
        $this->layout->view('cloud/favourited', $data);
    }   
    
    /**
     * Check if a cloud has a high likelihood of containing spam 
     *
     * @param object $cloud The cloud
     * @param  integer $user_id The id of the user adding or editting the cloud
     * @return boolean TRUE if the cloud is likely to contain spam and should be moderated, 
     * FALSE otherwise 
     */
    function _moderate_cloud($cloud, $user_id) {
    	$item_id = isset($cloud->cloud_id) ? $cloud->cloud_id : 0;
        $summary = isset($cloud->summary) ? $cloud->summary : NULL;
        $item_url= isset($cloud->url) ? $cloud->url : NULL;
        return $this->_moderate(__CLASS__, $item_id, $user_id, $cloud->title, "$summary $cloud->body", '', $item_url);
    }
    
    /**
     * Check if a comment has a high likelihood of containing spam 
     *
     * @param string $body The body of the comment
     * @param  integer $user_id The id of the user adding or editting the comment
     * @return boolean TRUE if the commentis likely to contain spam and should be moderated, 
     * FALSE otherwise 
     */
    function _moderate_comment($body, $user_id) {
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
            $user = $this->user_model->get_user($user_id); 
            if (!$user->whitelist) {
                $this->load->library('mollom');
                try {
                    $spam_status = $this->mollom->checkContent(null, $body);
                    if ($spam_status['quality'] < 0.5) {
                        $moderate = TRUE;
                    }
                } catch (Exception $e) {
                    
                }
            }
        }
        return $moderate;
    }
    
    /**
     * Check if a link has a high likelihood of containing spam  
     *
     * @param string $title The link title
     * @param string $url The URL of the link
     * @param  integer $user_id The id of the user adding or editting the link
     * @return boolean TRUE if the link is likely to contain spam and should be moderated, 
     * FALSE otherwise 
     */
    function _moderate_link($title, $url, $user_id) {
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
            $user = $this->user_model->get_user($user_id); 
            if (!$user->whitelist) {
                $this->load->library('mollom');
                try {  
                	$spam_status = $this->mollom->checkContent($title, false, false, $url);
                    if ($spam_status['quality'] < 0.5) {
                        $moderate = TRUE;
                    }
                } catch (Exception $e) {
                    
                }
            }
       	}
        return $moderate;
    }
    
    /**
     * Check if a reference has a high likelihood of containing spam 
     *
     * @param string $reference_text The reference text 
     * @param  integer $user_id The id of the user adding or editting the reference
     * @return boolean TRUE if the reference is likely to contain spam and should be 
     * moderated, FALSE otherwise 
     */
    function _moderate_reference($reference_text, $user_id) {	
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
            $user = $this->user_model->get_user($user_id); 
            if (!$user->whitelist) {
                $this->load->library('mollom');
                try {
                    $spam_status = $this->mollom->checkContent($reference_text);
             	 
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
