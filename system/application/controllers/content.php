<?php

/**
 * Controller for functionality related to extra content items on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Content
 */

class content extends MY_Controller {

	function content ()
	{
		parent::MY_Controller();
		$this->load->model('content_model');
	    $this->load->model('cloud_model');
		$this->load->library('layout', 'layout_main'); 	
	}

	/**
	 * Add an item of extra content to a cloud
	 *
	 * @param integer $cloud_id The ID of the cloud
	 */
    function add($cloud_id = 0) {
        // Check user logged in
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('body', t("Content"), 'required');
        
        if ($this->input->post('submit')) {
            if ($this->form_validation->run()) {
                // Get the form data 
                $body = $this->input->post('body');
                
                // Moderate for spam
				$moderate = $this->_moderate_content($body, $user_id);

                $this->content_model->insert_content($cloud_id, $body, $user_id, $moderate);
                              
                // If moderated, tell the user, otherwise redirect 
                if (config_item('x_moderation') && $moderate) {
                    $data['item'] = 'link';
                    $data['continuelink'] = '/cloud/view/'.$cloud_id;
                    $this->layout->view('moderate', $data);
                    return;
                } else {                  

                    redirect('/cloud/view/'.$cloud_id); // Return to the main cloud view page 
                }                
            }    
        }
        // Display the add link form
        $data['cloud'] = $this->cloud_model->get_cloud($cloud_id); 
        $data['title'] = t("Add Extra Content");
        $this->layout->view('content/add', $data); 
    }
    
    /**
     * Edit an existing item of extra content
     *
     * @param integer $content_id The ID of the extra content item
     */
    function edit($content_id = 0) {
        $user_id  = $this->db_session->userdata('id');
        $this->content_model->check_edit_permission($user_id, $content_id);

        if ($this->input->post('submit')) {
            // Get the form data 
            $content->content_id = $content_id;
            $content->body       = $this->input->post('body');

           // Set up form validation rules (empty rules needed for set_value() 
            $this->load->library('form_validation');

           $this->form_validation->set_rules('body', t("Content"), 'required');
            // Validate the data, if fine, update the news and redirect to the news page,
            // otherwise keep the submitted ata to repopulate the form
            if ($this->form_validation->run()) {
                $this->content_model->update_content($content);
                $content = $this->content_model->get_content_item($content_id);
                redirect('/cloud/view/'.$content->cloud_id);
            } else {
                $data['content'] = $content;
            }
        } 
        
        // If no data already set from invalid form submission, get the data for the news
        if (!isset($data['content'])) {
            $data['content'] = $this->content_model->get_content_item($content_id);
        }
        
        $data['admin'] = $this->auth_lib->is_admin();
        $data['new']   = false; 
        $data['title'] = t("Edit content");
        
        // Display the edit form 
        $this->layout->view('content/edit', $data); 
    }

    /**
     * Delete an item of extra content
     *
     * @param integer $content_id The ID of the extra content item
     */
    function delete($content_id = 0) {
        $user_id  = $this->db_session->userdata('id');
        $data['content'] = $this->content_model->get_content_item($content_id);
        $this->content_model->check_edit_permission($user_id, $content_id);

        
        // If confirmation form submitted delete the newsscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->content_model->delete_content($content_id);
            $this->layout->view('content/delete_success', $data);
        } else {
            $data['title'] = t("Delete content");
            $data['content_id'] = $content_id;
            $this->layout->view('content/delete_confirm', $data);
        }
    }
    
    /**
     * Move an item of extra content to a comment on the same cloud
     * Admin access only - to move content accidentally put in the wrong place
     *
     * @param integer $content_id The ID of the extra content item
     */
    function move_to_comment($content_id = 0) {
    	$this->auth_lib->check_is_admin(); 
        $this->load->model('comment_model');
        $content = $this->content_model->get_content_item($content_id); 

        $comment_id = $this->comment_model->insert_comment($content->cloud_id, 
                      $content->user_id, $content->body, 0, $content->created, 
                      $content->modified); 
        $this->content_model->delete_content($content_id);                
        redirect('/cloud/view/'.$content->cloud_id); // Return to the main cloud view page 
    }
    
    /**
     * Check if an extra content item has a high likelihood of containing spam 
     * @param string $body The body of the extra content item
     * @param  integer $user_id The id of the user adding or editting the extra content item
     * @return boolean TRUE if the extra content item is likely to contain spam and should be 
     * moderated, FALSE otherwise
     */
    function _moderate_content($body, $user_id) {
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
        	$this->load->model('user_model');
            $user = $this->user_model->get_user($user_id); 
            if (!$user->whitelist) {
                $this->load->library('mollom');
                try {                              
	        		$spam_status = $this->mollom->checkContent($title, false, false, $link);	 
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
