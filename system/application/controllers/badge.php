<?php 
/**
 * Controller for badge-related functionality 
 * 
 * @copyright 2011 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Badge
 */
class Badge extends MY_Controller {

    function Badge() {
        parent::MY_Controller();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('format');
        $this->load->library('layout', 'layout_main'); 
        $this->load->model('user_model'); 
        $this->load->model('badge_model');
    }
    
    function view($badge_id = 0) {
        $user_id  = $this->db_session->userdata('id'); 
        $data['badge'] = $this->badge_model->get_badge($badge_id);
        $data['badge']->badge_id = $badge_id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('evidence', t("Evidence"), 'required');
        
        if ($this->input->post('submit')) { // Process badge application
            $evidence        = $this->input->post('evidence');
            if ($this->form_validation->run()) { 
                $this->badge_model->insert_application($badge_id, $user_id, $cloud_id);
                $this->layout->view('badge/application_accepted', $data); 
                return;
            } else {
                show_error(t("An error occurred."));
            }
        } else {
            if ($data['badge']) {
                $data['edit_permission']  = $this->badge_model->has_edit_permission($user_id, 
                                                                        $badge_id);
                $data['admin']            = $this->auth_lib->is_admin();
                $data['can_apply'] = true;
                if (!$user_id) {
                    $data['can_apply'] = false;
                }
                // Also need to check that hasn't already applied for or been 
                // awarded the badge
                if ($data['can_apply']) {


                    // Put the cloudscapes into a suitable form for a select
                    $clouds = $this->user_model->get_clouds_with_contributions($user_id);

                    foreach ($clouds as $cloud) {
                       $options[$cloud->cloud_id] = $cloud->title;
                    }
                    
                    $data['options'] = $options;
                }
                $this->layout->view('badge/view', $data); 
            } else {
                // If invalid badge id, display error page
                show_error(t("An error occurred."));
            }
        }
    }
    
    /**
     * Add a new badge
     */
    function add() {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id'); 
        
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', t("Badge Name"), 'required');
        $this->form_validation->set_rules('description', 
                                          t("Badge Description"), 'required'); 
        $this->form_validation->set_rules('criteria', t("Criteria"), 'required');  
        $upload_path = $this->config->item('upload_path_badge');
        
        if ($this->input->post('submit')) {
            $badge->name        = $this->input->post('name');
            $badge->description = $this->input->post('description');
            $badge->criteria    = $this->input->post('criteria');
            $badge->user_id     = $user_id;
            $data['badge'] = $badge;

            if ($this->form_validation->run()) {
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'png';
                $config['max_size']  = '256';
                $config['max_width']  = '90';
                $config['max_height']  = '90';
                $this->load->library('upload', $config);

                 if (!$this->upload->do_upload('filename')) {
                    $data['error'] = $this->upload->display_errors();

                } else {
                    $upload_data = $this->upload->data();
                    $data = array('upload_data' => $upload_data);
                    $data['error']      = NULL;
                    $data['image_path'] = $upload_data['file_name'];            
                    // Check the width and height 
                    $dimensions_valid = true;
                    
                    if ($upload_data["image_height"] != 90) {
                        $dimensions_valid = false;
                    }  
                    if ($upload_data["image_width"] != 90) {
                        $dimensions_valid = false;
                    }        
                    
                    if ($dimensions_valid) {
                        $badge->image = $data['image_path'];
                    
                        $badge_id = $this->badge_model->insert_badge($badge);
                        
                        if ($badge_id) {
                            $badge->badge_id = $badge_id;
                            $data['badge'] = $badge;
                            $this->layout->view('badge/badge_added', $data);
                            return;
                        } else {
                            show_error(t("An error occurred creating the badge.")); 
                        }
                    } else {
                        $data['error'] = t('The image must be 90x90 pixels');
                    }
                } 
            } 
        }  

        $data['new']   = true;
        $data['title'] = t("Create Badge");
        $data['navigation'] = 'badges';

        // Display the form for creating a badge
        $this->layout->view('badge/edit', $data);  
    }
    
    function edit($badge_id = 0) {

        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id); 
        // Get the badge 
        $badge = $this->badge_model->get_badge($badge_id);
        $badge->badge_id = $badge_id;
        $data['badge'] = $badge;   
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', t("Badge Name"), 'required');
        $this->form_validation->set_rules('description', 
                                          t("Badge Description"), 'required'); 
        $this->form_validation->set_rules('criteria', t("Criteria"), 'required');  
        
        if ($this->input->post('submit')) {
            $badge->name        = $this->input->post('name');
            $badge->description = $this->input->post('description');
            $badge->criteria    = $this->input->post('criteria');
            $badge->user_id     = $user_id;
            $data['badge'] = $badge;

            if ($this->form_validation->run()) {
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'png';
                $config['max_size']  = '256';
                $config['max_width']  = '90';
                $config['max_height']  = '90';
                $this->load->library('upload', $config);

                 if (!$this->upload->do_upload('filename')) {
                    $data['error'] = $this->upload->display_errors();

                } else {
                          
                    if ($dimensions_valid) {
                        $badge->image = $data['image_path'];
                    
                        $badge_id = $this->badge_model->insert_badge($badge);
                        
                        if ($badge_id) {
                            $badge->badge_id = $badge_id;
                            $data['badge'] = $badge;
                            $this->layout->view('badge/badge_added', $data);
                            return;
                        } else {
                            show_error(t("An error occurred creating the badge.")); 
                        }
                    } else {
                        $data['error'] = t('The image must be 90x90 pixels');
                    }
                } 
            } 
        }  

        $data['new']   = false;
        $data['title'] = t("Edit Badge");
        $data['navigation'] = 'badges';

        // Display the form for creating a badge
        $this->layout->view('badge/edit', $data);          
    }
    


    /**
     * Delete a badge
     *
     * @param integer $badge_id The ID of the badge
     */
    function delete($badge_id = 0) {        
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');      

        $this->badge_model->check_edit_permission($user_id, $badge_id);
        
        // If confirmation form submitted delete the cloudscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $data['badge'] = $this->badge_model->get_badge($badge_id);
            $this->badge_model->delete_badge($badge_id);
            $this->layout->view('badge/delete_success', $data);
        } else {
            $data['title'] = t("Delete Badge");
            $data['navigation'] = 'badges';
            $data['badge_id'] = $badge_id;
            $data['badge'] = $this->badge_model->get_badge($badge_id);
            $this->layout->view('badge/delete_confirm', $data);
        }
    }    
    
    function edit_image($badge_id = 0) {
        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id); 
        // Get the badge 
        $badge = $this->badge_model->get_badge($badge_id);
        
        $this->layout->view('badge/edit_image', $data);
    }
    
    /**
     * Display a list of all the badges on the site 
     *
     */
    function badge_list() {
        $data['title']      = 'Badges';
        $data['navigation'] = 'badges';
        $data['badges']     = $this->badge_model->get_badges();
        $this->layout->view('badge/list', $data);
    }   
}
