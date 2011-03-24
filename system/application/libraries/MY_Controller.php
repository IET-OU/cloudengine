<?php
/**
 * Most controllers in CloudEngine should be extended from this one.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package
 */

class MY_Controller extends Controller {

  public function MY_Controller ()  {
    parent::Controller();
    
      //initalise
      $prevent_access = 0;
      
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
      
      //*********************************************************************
      // Maintenance mode processing - start
      //*********************************************************************
      //public site offline message
      $temp = str_replace('!site-name!', config_item('site_name'), t(config_item('offline_message_public')));
      $this->config->set_item('offline_message_public', str_replace(
                         '!site-email!', mailto(config_item('site_email')), $temp)
                         );
      //admin site offline message
      $this->config->set_item('offline_message_admin', str_replace( '!site-name!',
                         config_item('site_name'), t(config_item('offline_message_admin')))
                         );

      //if the site is offline and we have a numeric value for site_live (i.e. we are not in install script)
      if (!config_item('site_live') && is_numeric(config_item('site_live'))) {
        //if there is session data for the user and the user is admin
        if ($this->db_session->userdata('role') && $this->db_session->userdata('role') == 'admin') {
          //allow access
          $prevent_access = 0;
        }
        //otherwise site 'prevent access' flag
        else {
          $prevent_access = 1;
        }
      }
      
      //if the site is offline
      if ($prevent_access) {
        //if the page is not one of the auth pages e.g. login
        if (!($this->uri->segment(1) === 'auth')) {     
          //call method to show the site offline page
          $error =& load_class('Exceptions');
          $message = nl2br($this->config->item('offline_message_public'));
          echo $error->show_error('Information', $message, 'site_offline', 200);
          exit; 
        }        
      }                        
      //*********************************************************************
      // Maintenance mode processing - end
      //*********************************************************************

  }

  /** A wrapper around the Mollom anti-spam library.
   *    Initial use: Cloud::_moderate_cloud, Message::compose.
   */
  protected function _moderate($item_type, $item_id=0, $user_id=0, $title=null, $body=null, $authorName=null, $authorUrl=null, $authorEmail=null, $authorOpenId=null, $authorId=null) {
    $moderate = FALSE;
	if (config_item('x_moderation')) {
        $threshold = 0.5;
        $thres_raw = config_item('moderation_less_than');
        // Sanity checks.
        if ($thres_raw && is_float($thres_raw) && $thres_raw > 0 && $thres_raw < 0.9) {
            $threshold = $thres_raw;
        }

        if ($user_id) {
            $this->load->model('user_model');
            $user = $this->user_model->get_user($user_id);
        }
		if (!is_object($user) || !$user->whitelist) {
		    $this->load->library('mollom');
		    try {
                // The Mollom library call.
		        $status = (object)$this->mollom->checkContent($title, $body, $authorName, $authorUrl, $authorEmail, $authorOpenId, $authorId);
			    if ($status->quality < $threshold) { #($spam_status['quality'] < 0.5) {
			        $moderate = TRUE;
			    }
                // Data-logging: should be 'debug' or 'important'!
                log_message('error', "Anti-spam check. $item_type:$item_id '$title'; $status->spam, $status->quality (threshold:$threshold)");
			} catch (Exception $e) {
			    log_message('error', 'Anti-spam exception, Mollom: '.$e->getMessage());
			}
		}
	}
	return $moderate;
  }

}
