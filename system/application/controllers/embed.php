<?php 

/**
 * Controller for functionality related to embeds on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Embed
 */

class Embed extends MY_Controller {

    function Embed() {
        parent::MY_Controller();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('format');
        $this->load->model('cloud_model');
        $this->load->model('embed_model');
        $this->load->library('layout', 'layout_main'); 
    }
    
    /**
     *  Add an item of embedded content to a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function add($cloud_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('url', t("URL"), 'valid_url');
        
        if ($this->input->post('submit')) {

            if ($this->form_validation->run()) {
                // Get the form data 
                $url                    = $this->input->post('url');
                $title                  = $this->input->post('title');
                $accessible_alternative = $this->input->post('accessible_alternative');
                
                // Moderate for spam
                $moderate = $this->_moderate_embed($title, $url, $user_id);

                $this->embed_model->add_embed($cloud_id, $url, $title, $user_id, 
                                              $accessible_alternative , $moderate);
                              
                // If moderated, tell the user 
                // Otherwise redirect 
                if (config_item('x_moderation') && $moderate) {
                    $data['item'] = 'embed';
                    $data['continueembed'] = '/cloud/view/'.$cloud_id;
                    $this->layout->view('moderate', $data);
                    return;
                } else {                  

                    redirect('/cloud/view/'.$cloud_id); // Return to the main cloud view page 
                }                
            }
        }       
        
        // Display the add embed form
        $data['cloud'] = $this->cloud_model->get_cloud($cloud_id); 
        $data['title'] = t("Add Embedded Content");     
        $data['new']   = true;
        $this->layout->view('embed/edit', $data);         
    }

    /**
     * Delete an item of embedded content 
     *
     * @param integer $embed_id The ID of the embed
     */
    function delete($embed_id = 0) {
        $this->load->model('embed_model');
        $embed = $this->embed_model->get_embed($embed_id);        
        // Check if the user has edit permission for the cloud that this embed belongs to
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->embed_model->check_edit_permission($user_id, $embed->embed_id);
           
         // If confirmation form submitted delete the embed, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->load->model('embed_model');
            $this->embed_model->delete_embed($embed_id);
            redirect('/cloud/view/'.$embed->cloud_id);
        } else {
            $data['title'] = t("Delete Embedded Content");
            $data['embed'] = $embed;
            $data['cloud'] = $this->cloud_model->get_cloud($embed->cloud_id);
            $this->layout->view('embed/delete_confirm', $data);
        }       
    }
    
    /**
     * Edit an item of embedded content 
     *
     * @param integer $embed_id The ID of the embed
     */
    function edit($embed_id = 0) {
        // Check if the user has edit permission for the cloud that this embed belongs to
        $this->auth_lib->check_logged_in();
        $embed = $this->embed_model->get_embed($embed_id);
        
        if ($this->input->post('submit')) {
            $title                  = $this->input->post('title');
            $url                    = $this->input->post('url');
            $accessible_alternative = $this->input->post('accessible_alternative');
            $this->embed_model->update_embed($embed_id, $url, $title, $accessible_alternative);
            redirect('/cloud/view/'.$embed->cloud_id); 
        } else {
            $user_id       = $this->db_session->userdata('id'); 
            $data['cloud'] = $this->cloud_model->get_cloud($embed->cloud_id);  
            $data['title'] = t("Edit Embedded Content"); 
            $data['embed']  = $embed;
            $this->layout->view('embed/edit', $data);
        }
    }
    
     /**
     * View the accessible alternative to an embed
     *
     * @param integer $embed_id The ID of the embed
     */
    function accessible_alternative($embed_id = 0) {
        $data['embed'] = $this->embed_model->get_embed($embed_id);
        $this->layout->view('embed/accessible_alternative', $data);
    }   
    
    /**
     * Check if an embed has a high likelihood of containing spam 
     *
     * @param string $title The title of the embed
     * @param string $url The URL of the embed
     * @param  integer $user_id The id of the user adding or editting the embed
     * @return boolean TRUE if the embed is likely to contain spam and should be moderated, 
     * FALSE otherwise
     */
    function _moderate_embed($title, $url, $user_id) {
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
        	$this->load->model('user_model');
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
}