<?php
/**
 * Most controllers in CloudEngine should be extended from this one.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package
 */

class MY_Controller extends Controller {

  protected $moderation_provider;

  /** Initalize the CloudEngine application.
   */
  public function MY_Controller() {
    parent::Controller();

    $this->_set_cloudengine_debug();

    $this->_get_message_unread_count();

    $this->_set_maintenance_mode();
  }


  /** Decide whether we're in debug mode, and if so, initialize Fire PHP, set error_reporting etc.
   *
   * @return null
   */
  protected function _set_cloudengine_debug() {
      //initalise
      $debug          = false;
      $show_debug     = config_item('debug');

      //debug value of 1 is debug output for admin users
      //debug value of 2 is debug output for all users (emergency use only)
      if (($this->auth_lib && $this->auth_lib->is_admin() && $show_debug == 1) || $show_debug == 2) {
        $debug = true;
      }

      //firephp - should we enable it?
      if ($debug) {
        $this->load->library('firephp');
        // This overrides the settings in the index.php file
      	ini_set("display_errors", 'On');
      	error_reporting(E_ALL & ~E_NOTICE);
      } else {
        $this->load->library('firephp_fake');
        $this->firephp =& $this->firephp_fake;
        ini_set("display_errors", 'Off');
        error_reporting(0);
      }
      $this->firephp->fb($_SERVER,'Server info','INFO');
  }


  /** Decide whether we're in maintenance mode. If we are, set the appropriate message.
   *
   * @return null
   */
  protected function _set_maintenance_mode() {

      //initalise
      $prevent_access = 0;

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
  }


  /**
   * Get message unread count for the user, this is called on nearly every page
   * and is used to update the message count in the primary navigation for a user.
   *
   * @return null
   */
  protected function _get_message_unread_count() {
      //(most controllers extend MY_Controller)
      if ($this->config->item('x_message') && $this->db_session) {
        if (is_numeric($this->db_session->userdata('id'))) {
          $this->load->model('message_model');
          $user_message_count = $this->message_model->get_user_new_message_count($this->db_session->userdata('id'));
          $this->db_session->set_userdata('user_message_count', $user_message_count);
        }
      }
  }


  protected function _getModerationProvider() {
    if (! $this->moderation_provider) {
      $this->load->library('ModerationProvider');

      $this->moderation_provider = new ModerationProvider();
      $this->_debug(get_class( $this->moderation_provider ));
    }
    return $this->moderation_provider;
  }

  /** Do moderation: a wrapper around appropriate anit-spam moderation library
   *    Note: it is up to the specific controller to manage the user interaction.
   *
   * @return boolean Whether the item has been flagged for moderation.
   */
  protected function _moderate($message) {
    $is_spam = FALSE;
    if (config_item('x_moderation')) {
      $this->load->model('user_model');

      $user = $this->user_model->get_user($this->db_session->userdata('id'));

      $moderation_provider = $this->_getModerationProvider();
      if (is_object($moderation_provider)) {
        $is_spam = $moderation_provider->checkSpam($user, $message);
      }
    }
    return  $is_spam;
  }

  /** Output a HTTP header with debug information.
   * @param mixed $obj  Object (stdClass), array, string etc.
   */
  public function _debug( $obj ) {
    static $count = 0;
    header(sprintf( 'X-my-controller-%02d: %s', $count, json_encode( $obj )));
    $count++;
  }
}
