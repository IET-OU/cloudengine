<?php 
/**
 * Controller for functionality related to Google Gadgets on Clouds
 * 
 * A Google gadget can be identified by the URL of the XML file defining the gadget, and can 
 * be thought of as a form of embed on the site. 
 * We use Google Friend Connect as the Gadget Container for rendering gadget. See the 
 * view gadget/gadget_block.php to see the code that uses this. 
 * To display a gadget on the site, we need a Google Friend Connect key connected to the 
 * domain for the site, which can be obtained from the Google Friend Connect site. This 
 * needs to be entered in config/cloudengine.php and gadgets enabled in order for gadgets to 
 * work correctly on the site.  
 * 
 * A user can add a Google Gadget to any cloud that they own. Once they have done this, any 
 * user of the site will view the gadget on that cloud. Alternatively a user can add a gadget 
 * to the list of all 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Gadgets
 */
class Gadget extends Controller {

    function Gadget() {
        parent::Controller();
        // Check that the Google Gadgets feature flag is enabled 
        if (!$this->config->item('x_gadgets')) {
            show_404();
        }
        
        $this->load->model('cloud_model');
        $this->load->model('gadget_model');
    }
    
    /**
     * Form and form processing for adding a Google Gadget to a cloud  
     *
     * @param integer $cloud_id The ID of the cloud
     */
    function add_to_cloud($cloud_id) {
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        $this->cloud_model->check_edit_permission($user_id, $cloud_id);
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Gadget Title"), 'required|max_length[100]');
        $this->form_validation->set_rules('url', t("URL"), 'valid_url');
        
        $url                            = $this->input->post('url');
        $title                          = $this->input->post('title');
        $accessible_alternative         =  $this->input->post('accessible_alternative');
        $gadget->url                    = $url;
        $gadget->title                  = $title;
        $gadget->accessible_alternative = $accessible_alternative; 
 
        if ($this->input->post('submit')&& $this->form_validation->run()) {
            // Add the gadget and return to the main cloud view page  
            $this->gadget_model->add_gadget_to_cloud($cloud_id, $url, $user_id, $title, $accessible_alternative);                     
            redirect('/cloud/view/'.$cloud_id);            
        }       
        
        // Display the add gadget form
         $data['type']   = 'cloud';
        $data['gadget']  = $gadget;
        $data['cloud']   = $this->cloud_model->get_cloud($cloud_id); 
        $data['title'] = t("Add Google Gadget");     

        $this->layout->view('gadget/add_to_cloud', $data);         
    }
    
    /**
     * Form and form processing for adding a gadget to all clouds added to 
     *
     */
    function add_to_user() {
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Gadget Title"), 'required|max_length[100]');
        $this->form_validation->set_rules('url', t("URL"), 'valid_url');
        
        $url                            = $this->input->post('url');
        $title                          = $this->input->post('title');
        $accessible_alternative         =  $this->input->post('accessible_alternative');
        $gadget->url                    = $url;
        $gadget->title                  = $title;
        $gadget->accessible_alternative = $accessible_alternative; 

        if ($this->input->post('submit')&& $this->form_validation->run()) {
            // Add the gadget and return to the manage gadgets page
            $this->gadget_model->add_gadget_to_user($url, $user_id, $title, $accessible_alternative);        
            redirect('/gadget/manage');              
        }       
        
        // Display the add gadget form
        $data['type']   = 'user';
        $data['gadget'] = $gadget;
        $data['cloud']  = $this->cloud_model->get_cloud($cloud_id); 
        $data['title']  = t("Add Google Gadget");     

        $this->layout->view('gadget/add_to_user', $data);          
    }

    /**
     * Delete confirmation form and processing to delete a Google gadget that has been added to a cloud
     *
     * @param integer $gadget_id The ID of the gadget 
     */
    function delete_from_cloud($gadget_id) {
        // Check if the user has edit permission for the cloud that this gadget belongs to
        $this->auth_lib->check_logged_in();
        $gadget = $this->gadget_model->get_gadget($gadget_id);
        $user_id  = $this->db_session->userdata('id');
        if (!$gadget->cloud_id) {
            show_error(t("You do not have permission to view this page"));
        }
        $this->cloud_model->check_edit_permission($user_id, $gadget->cloud_id);
           
         // If confirmation form submitted delete the gadget, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->load->model('gadget_model');
            $this->gadget_model->delete_gadget($gadget_id);
            redirect('/cloud/view/'.$gadget->cloud_id);
        } else {
            $data['title'] = t("Delete Google Gadget"); 
            $data['gadget'] = $gadget;
            $data['cloud'] = $this->cloud_model->get_cloud($gadget->cloud_id);
            $this->layout->view('gadget/delete_confirm_cloud', $data);
        }       
    }
    
    /**
     * Delete confirmation form and processing to delete a Google gadget that a user has added to all clouds
     *
     * @param integer $user_id The ID of the user
     */
    function delete_from_user($gadget_id) {
        $this->auth_lib->check_logged_in();
        $gadget = $this->gadget_model->get_gadget($gadget_id);
        $user_id  = $this->db_session->userdata('id');
        
        if ($gadget->user_id != $user_id) {
           show_error(t("You do not have permission to view this page")); 
        }
        
        // If confirmation form submitted delete the gadget, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->load->model('gadget_model');
            $this->gadget_model->delete_gadget($gadget_id);
            redirect('gadget/manage');
        } else {
            $data['title'] = t("Delete Google Gadget"); 
            $data['gadget'] = $gadget;
            $this->layout->view('gadget/delete_confirm_user', $data);
        }         
    }
    
    /**
     * Display the page for managing the gadgets that a user has added to all their clouds
     */
    function manage() {
        // Check permissions 
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id'); 
               
        $data['title'] = t("Manage Google Gadgets");
        $data['gadgets'] = $this->gadget_model->get_gadgets_for_user($user_id);
        $this->layout->view('gadget/manage', $data);
    }
    
    /**
     * View the accessible alternative to a gadget 
     *
     * @param integer $gadget_id The ID of the gadget 
     */
    function accessible_alternative($gadget_id) {
        $data['gadget'] = $this->gadget_model->get_gadget($gadget_id);
        $this->layout->view('gadget/accessible_alternative', $data);
    }
}