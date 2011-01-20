<?php
/**
 * Most controllers in CloudEngine should be extended from this one.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package
 */

class MY_Controller extends Controller {

  function MY_Controller ()  {
    parent::Controller();
      
      //get message unread count for the user, this is called on nearly every page and is used to
      //update the message count in the primary navigation for a user 
      //(most controllers extend MY_Controller)
      if ($this->config->item('x_message') && $this->db_session) { 
        if (is_numeric($this->db_session->userdata('id'))) {
          $this->load->model('message_model');
          $user_message_count = $this->message_model->get_user_new_message_count($this->db_session->userdata('id'));
          $this->db_session->set_userdata('user_message_count', $user_message_count);
        }
      }
      
      //***********************************************************************************
      // Maintenance mode processing - start
      //***********************************************************************************   
      //public site offline message
      
      $this->config->set_item('offline_message_public', str_replace(  '!site-name!',
                                                                      config_item('site_name'),
                                                                      t(config_item('offline_message_public'))
                                                                    )
      
                             );                                                                                                                                                           
      //admin site offline message
      $this->config->set_item('offline_message_admin', str_replace( '!site-name!',
                                                                    config_item('site_name'),
                                                                    t(config_item('offline_message_admin'))
                                                                  )
                             );                           
      //site offline message update date
      $this->config->set_item('offline_message_created', date('G:i, d/m/Y',config_item('offline_message_created')));                                                 
    
      //if the site is offline
      if (!config_item('site_live')) {
        //if there is session data for the user
        if ($this->db_session->userdata('role') && $this->db_session->userdata('role') == 'admin') {
          //allow access
          $prevent_access = 0;
        }
        //
        else {
          $prevent_access = 1;
        }
      }
      
      //if the site is offline
      if ($prevent_access) {
        //if the page is not one of the auth pages e.g. login
        if (!($this->uri->segment(1) === 'auth')) {         
          $error =& load_class('Exceptions');
          $message = $this->config->item('offline_message_public') .'<br /><br />' .$this->config->item('offline_message_created');
          echo $error->show_error('Information', $message, 'site_offline', 200);
          exit; 
        }        
      }                        
      //***********************************************************************************
      // Maintenance mode processing - end
      //***********************************************************************************   

  }
}
