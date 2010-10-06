<?php

/**
 * Controller for functionality related to comments on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Comment
 */

class Comment extends Controller {

	function Comment ()
	{
		parent::Controller();
		$this->load->model('comment_model');
		$this->load->library('layout', 'layout_main'); 	
	}

    /**
     * Edit an existing comment 
     *
     * @param integer $comment_id The ID of the comment
     */
    function edit($comment_id = 0) {
        $user_id  = $this->db_session->userdata('id');
        $this->comment_model->check_edit_permission($user_id, $comment_id);


        if ($this->input->post('submit')) {
            // Get the form data 
            $comment->comment_id = $comment_id;
            $comment->body       = $this->input->post('body');

           // Set up form validation rules (empty rules needed for set_value() 
            $this->load->library('form_validation');

           $this->form_validation->set_rules('body', t("comment"), 'required');
            // Validate the data, if fine, update the news and redirect to the news page,
            // otherwise keep the submitted ata to repopulate the form
            if ($this->form_validation->run()) {
                $this->comment_model->update_comment($comment);
                $comment = $this->comment_model->get_comment($comment_id);
                redirect('/cloud/view/'.$comment->cloud_id);
            } else {
                $data['comment'] = $comment;
            }
        } 
        
        // If no data already set from invalid form submission, get the data for the news
        if (!isset($data['comment'])) {
            $data['comment'] = $this->comment_model->get_comment($comment_id);
        }
        
        $data['admin'] = $this->auth_lib->is_admin();
        $data['new']   = false; 
        $data['title'] = t("Edit comment");
        
        // Display the edit form 
        $this->layout->view('cloud_comment/edit', $data); 
    }

    /**
     * Delete a comment
     *
     * @param integer $comment_id The ID of the comment
     */
    function delete($comment_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $data['comment'] = $this->comment_model->get_comment($comment_id);
        
        // If confirmation form submitted delete the newsscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->comment_model->delete_comment($comment_id);
            $this->layout->view('cloud_comment/delete_success', $data);
        } else {
            $data['title'] = t("Delete comment");
            $data['comment_id'] = $comment_id;
            $this->layout->view('cloud_comment/delete_confirm', $data);
        }
    }
    
    /**
     * Move a comment onto a cloud into the extra content for a cloud 
     * Admin access only - for moving items accidentally put in the wrong place
     *
     * @param integer $comment_id The ID of the comment
     */
    function move_to_content($comment_id = 0) {
    	$this->auth_lib->check_is_admin(); 
        $this->load->model('content_model');
        $comment = $this->comment_model->get_comment($comment_id); 
        $content_id = $this->content_model->insert_content($comment->cloud_id, 
                      $comment->body, $comment->user_id,  0, $comment->timestamp, 
                      $comment->modified); 
        $this->comment_model->delete_comment($comment_id);                
        redirect('/cloud/view/'.$comment->cloud_id); // Return to the main cloud view page 
    }    
}
