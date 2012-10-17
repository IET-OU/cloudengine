<?php 
/**
 * Controller for badge-related functionality 
 * 
 * @copyright 2012 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Badge
 * 
 * Two types of badges can be created: 'verifier' or 'crowdsource'. 
 * For verifier badges, a set of verifiers can be specified for the badge. 
 * Badge applications can be approved or rejected by any verifier from that 
 * set. 
 * For crowdsource badges, any member of the site may approve or reject badge
 * applications but a certain number (specified when the badge is created) of
 * approvals are needed for the badge to be approved overall. (Rejecting a 
 * badge does not have any effect but does remove the application from the list
 * of applications for that user). 
 *
 * Badges awarded to a user are displayed on their profile. 
 *  
 * The Mozilla Open Badges Infrastructure is also supported in that an 
 * assertion is created for each badge awarded and the Issuer API is used to 
 * issue the badge.  See https://wiki.mozilla.org/Badges for more on Mozilla 
 * Open Badges
 */
class Badge extends MY_Controller {

    function Badge() {
        parent::MY_Controller();
        $this->load->helper('url');
        $this->load->helper('format');
        $this->load->helper('form');
        $this->load->library('layout', 'layout_main'); 
        $this->load->model('user_model'); 
        $this->load->model('badge_model');
    }
    
    /**
     * Display a list of all the badges on the site 
     */
    function badge_list() {
        $data['title']      = 'Badges';
        $data['navigation'] = 'badges';
        $data['badges']     = $this->badge_model->get_badges();
        $this->layout->view('badge/list', $data);
    }
    
    /**
     * Display the information about a badge
     * @param integer $alpha The ID of the badge to display
     */    
    function view($badge_id = 0) {
        $user_id  = $this->db_session->userdata('id'); 
        $badgeid_valid = FALSE;
        if (is_numeric($badge_id)) {
            $data['badge'] = $this->badge_model->get_badge($badge_id);
            if ($data['badge']->name) {
                $badgeid_valid = TRUE;
            }            
        }
        if ($badgeid_valid) {
            $data['edit_permission']  = $this->badge_model->has_edit_permission($user_id, 
                                                                    $badge_id);
            $data['admin']            = $this->auth_lib->is_admin();
            $data['can_apply']        = $this->badge_model->can_apply($user_id, 
                                                                    $badge_id);
            $data['title']            = $data['badge']->name;                                                        
            $this->layout->view('badge/view', $data); 
        } else {
            // If invalid badge id, display error page
            show_404();
        }
    }
    
    /**
     * Make an application to be awarded a badge
     * @param integer $badge_id The ID of the badge that the application is for
     */
    function apply($badge_id = 0) {
        $user_id  = $this->db_session->userdata('id'); 
        $can_apply = $this->badge_model->can_apply($user_id, $badge_id);
        
        if (!$can_apply) {
            show_error(t("You cannot apply for this badge, either because you 
            already have a pending application for it or because you have 
            already been awarded it."));
        }
        $data['badge'] = $this->badge_model->get_badge($badge_id);
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('evidence_url', t("Evidence URL"), 
                                          'valid_url|required');
        
        if ($this->input->post('submit')) { // Process badge application
            $evidence_url       = $this->input->post('evidence_url');
            if ($this->form_validation->run()) { 
                $this->badge_model->insert_application($badge_id, $user_id, 
                                                       $evidence_url);
                $data['title'] = t('Application received');                                       
                $this->layout->view('badge/application_received', $data); 
                return;
            } 
        } 
                
        if ($data['badge']->name) {
            $data['title'] = t('Apply for badge');     
            $this->layout->view('badge/apply_form', $data); 
        } else {
            // If invalid badge id, display error page
            show_error(t("An error occurred."));
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
        $this->form_validation->set_rules('criteria', t("Criteria"), 
                                          'required'); 
        $this->form_validation->set_rules('type', 
                              t("Type of Badge approval process"), 'required');      
        if ($this->input->post('type') == 'crowdsource') {
            $this->form_validation->set_rules('num_approves', 
                  t("number of users who need to approve a badge application"),
                  'required|is_natural_no_zero');
        }
                              
        $upload_path = $this->config->item('upload_path_badge');
        
        if ($this->input->post('submit')) {
            $badge->name        = $this->input->post('name');
            $badge->description = $this->input->post('description');
            $badge->criteria    = $this->input->post('criteria');
            $badge->type        = $this->input->post('type');
            $badge->issuer_name = $this->input->post('issuer_name');
            $badge->num_approves = $this->input->post('num_approves');
            $badge->user_id     = $user_id;
            $data['badge'] = $badge;

            if ($this->form_validation->run()) {
                // Process the badge image
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
                            $data['title'] = t('Badge Created');
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
    
    /**
     * Edit a badge
     * @param integer $badge_id The ID of the badge 
     */
    function edit($badge_id = 0) {
        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id); 
        // Get the badge 
        $badge = $this->badge_model->get_badge($badge_id);
        $badge->badge_id = $badge_id;
        $data['badge'] = $badge;   
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', t("Badge Name"), 
                                          'required');
        $this->form_validation->set_rules('description', 
                                          t("Badge Description"), 
                                          'required'); 
        $this->form_validation->set_rules('criteria', t("Criteria"), 
                                          'required');  
        
        if ($this->input->post('submit')) {            
            $updated_badge->badge_id    = $badge_id;
            $updated_badge->name        = $this->input->post('name');
            $updated_badge->description = $this->input->post('description');
            $updated_badge->criteria    = $this->input->post('criteria');
            $badge->issuer_name         = $this->input->post('issuer_name');

            $data['badge'] = $updated_badge;

            if ($this->form_validation->run()) {                   
                $this->badge_model->update_badge($updated_badge);
                redirect('/badge/view/'.$badge_id);
            } 
        }  

        $data['new']   = false;
        $data['title'] = t("Edit Badge");
        $data['navigation'] = 'badges';

        // Display the form for creating a badge
        $this->layout->view('badge/edit', $data);          
    }
    
    
    /**
     * Edit the image associated with a badge
     * @param integer $badge_id The ID of the badge
     */
    function edit_image($badge_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id); 
        // Get the badge 
        $data['badge'] = $this->badge_model->get_badge($badge_id);
        if ($this->input->post('submit')) {
            $upload_path = $this->config->item('upload_path_badge');
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
                    $badge->badge_id = $badge_id;

                    $this->badge_model->update_badge($badge);
                    $data['badge'] = $this->badge_model->get_badge($badge_id);
                    $data['title'] = t('Badge image changed');
                    $this->layout->view('badge/edit_image_success', $data);
                    return;
                    
                }            
            }
        }
        $data['title'] = t('Edit Badge Image');
        $this->layout->view('badge/edit_image', $data);
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
        
        // If confirmation form submitted delete the badge, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $data['badge'] = $this->badge_model->get_badge($badge_id);
            $data['title'] = t('Badge deleted');
            $this->badge_model->delete_badge($badge_id);
            $this->layout->view('badge/delete_success', $data);
        } else {
            $data['navigation'] = 'badges';
            $data['badge_id']   = $badge_id;
            $data['badge']      = $this->badge_model->get_badge($badge_id);
            $data['title']      = t('Confirm badge deletion');
            $this->layout->view('badge/delete_confirm', $data);
        }
    }  

    /**
     * Delete a badge application
     *
     * @param integer $badge_id The ID of the badge
     */
    function delete_application($application_id = 0) {        
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');      

        $this->badge_model->check_application_edit_permission($user_id, 
                                                              $application_id);
        
        // If confirmation form submitted delete the application, otherwise 
        // display  the confirmation form 
        if ($this->input->post('submit')) {
            $data['badge'] = $this->badge_model->get_application($application_id);
            $this->badge_model->delete_application($application_id);
            $data['title'] = t('Application deleted');
            $this->layout->view('badge/application_delete_success', $data);
        } else {
            $data['navigation'] = 'badges';
            $data['badge'] = $this->badge_model->get_application($application_id);
            $data['title'] = t('Confirm application deletion');
            $this->layout->view('badge/application_delete_confirm', $data);
        }
    }     
    
    /**
     * View a badge application and the decisions on it
     * @param integer $application_id The ID of the application
     */
    function application($application_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        // Only the person who has applied for badges and admins can see the 
        // application
        $this->badge_model->check_application_edit_permission($user_id, 
                                                              $application_id);        
        $data['application'] = $this->badge_model->get_application($application_id);
        if ($data['application']) {
            $data['decisions'] = $this->badge_model->get_decisions($application_id);
            $data['title'] = t('Badge application');
            $this->layout->view('badge/application', $data);
        } else {
            show_error(t("An error occurred."));
        }
    }
    

    /**
     * Display page for managing the verifiers of a badge
     * @param integer $badge_id The ID of the badge
     */
    function manage_verifiers($badge_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id); 
        $badge = $this->badge_model->get_badge($badge_id);
         
        
        // If search form submitted, search for users 
        if ($this->input->post('submit')) {
            $user_search_string = $this->input->post('user_search_string', 
                                                     TRUE);
            $data['users'] = $this->user_model->search($user_search_string);  
            $data["user_search_string"] = $user_search_string;        
        } 

        // Get the information to display existing permissions
        $data['title']     = t("Manage Badge Verifiers");
        $data['verifiers'] = $this->badge_model->get_verifiers($badge_id);
        $data['badge']     = $badge;

        $this->layout->view('badge/manage_verifiers', $data);
    }
    
    /**
     * Add a verifier to a badge
     * @param integer $badge_id The ID of the badge
     * @param integer $user_id The ID of the user to add
     */
    function verifier_add($badge_id = 0, $add_user_id = 0) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id);   
        $this->badge_model->add_verifier($badge_id, $add_user_id);
        redirect('badge/manage_verifiers/'.$badge_id);        
    }
    
    /**
     * Remove a verifier to a badge
     * @param integer $badge_id The ID of the badge
     * @param integer $user_id The ID of the user to remove
     */
    function verifier_remove($badge_id, $remove_verifier_id) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');        
        $this->badge_model->check_edit_permission($user_id, $badge_id);  
        $this->badge_model->remove_verifier($badge_id, $remove_verifier_id);
        redirect('badge/manage_verifiers/'.$badge_id);         
    }   
    
    /**
     * Display pending applications for a specified badge
     * @param integer $badge_id The ID of the badge
     */
    function applications($badge_id = FALSE) {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        
        // If no badge ID specified, produce list for all badges
        if (!$badge_id) {         
            $data['badges']              = $this->badge_model->get_badges_with_verification_permission($user_id);       
            $data['crowdsourced_badges'] = $this->badge_model->get_crowdsourced_badges();
            $data['title']               = t('Pending badge applications');
            $this->layout->view('badge/applications_all_pending', $data);  
            return;        
        } else { // Otherwise get the applications for the specified badge
            $badge = $this->badge_model->get_badge($badge_id);
            if ($badge_type == 'verifier') {
                $this->badge_model->check_verifier($user_id, $badge_id);
            }
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('decision', t("Decision"), 
                                              'required');
            
            if ($this->input->post('submit') && $this->form_validation->run()) { 
                $application_id = $this->input->post('application_id');
                $decision = $this->input->post('decision');
                $feedback = $this->input->post('feedback');
                $application = $this->badge_model->get_application($application_id);
                if ($user_id != $application->user_id) { 
                    $badge_awarded = $this->badge_model->add_decision($application_id, 
                                                   $user_id, $decision, $feedback);
                    if ($badge_awarded) {
                        $this->_send_badge_awarded_email($application_id);
                        // If the badge has been approved then issue it via 
                        // the Mozilla Open Badges infrastructure by doing an 
                        // HTTP request to badge/issue/<$application_id>
                        $this->load->helper('curl');
                        send_request(base_url().'/badge/issue/'.$application_id);
                    }
                }            
            }    
            
            $data['title']        = t("Applications for badge");
            $data['user_id']      = $user_id;
            $data['badge']        = $badge;
            $data['applications'] = $this->badge_model->get_applications($badge_id);
            $this->layout->view('badge/applications_for_badge_pending', $data);
        }
    }
    
    /**
     * Display all the applications of the current user
     */
    function user_applications() {
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $data['pending_applications'] = $this->badge_model->get_applications_for_user($user_id, 
                                                                    'pending'); 
        $data['approved_applications'] = 
                        $this->badge_model->get_applications_for_user($user_id, 
                                                                   'approved');
        $data['rejected_applications'] = 
                        $this->badge_model->get_applications_for_user($user_id, 
                                                                   'rejected');
        $data['title'] = t('Your badge applications');                                                           
        $this->layout->view('badge/applications_for_user_all', $data);        
    }
    
    /**
     * Display the json file for an Mozilla Open Badges assertion for an 
     * awarded badge
     */
    function assertion($application_id) {
        $applicationid_valid = FALSE;
        if (is_numeric($application_id)) {
            $application = $this->badge_model->get_application($application_id);
            if ($application) {
                $applicationid_valid = TRUE;
            }            
        }    
        
        if ($applicationid_valid && $application->status == 'approved') {
            $data['application']          = $application;
            $data['badge_issuer_origin']  = base_url();
            $data['salt']                 = $this->config->item('badge_salt');
            $data['recipient']            = 'sha256$'.hash('sha256', 
                                            $application->email.$data['salt']);
            $data['badge_issuer_org']     = $this->config->item('badge_issuer_org');
            $data['badge_issuer_contact'] = $this->config->item('badge_issuer_contact');
            header('Content-Type: application/json');
            $this->load->view('badge/assertion', $data);
        } else {
            show_404();
        }
    }    
    
    /**
     * Send an email to a user who has been awarded a badge
     */
    protected function _send_badge_awarded_email($application_id) {
        $data['application'] = $this->badge_model->get_application($application_id);
        $message = $this->load->view('email/badge_awarded', $data, true);
        $this->load->plugin('phpmailer');
        send_email($data['application']->email, config_item('site_email'), 
                   t('!site-name! - Badge Awarded'), $message); 
    }    
}