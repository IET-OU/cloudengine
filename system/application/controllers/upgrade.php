<?php

/**
 * Controller for site upgrades 
 * 
 * Version numbers have the form "MAJOR.MINOR.REVISION", eg. 1.0.1. 
 * These are converted to an integer with padding e.g.10001
 * The upgrade function for an upgrade to a version is then e.g. _upgrade_NNNNN() 
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Install
 */
class Upgrade extends MY_Controller {

    protected $messages = array();

    public function __construct() {
        parent::MY_Controller();

        $this->auth_lib->check_is_admin(); 
        $this->load->library('layout', 'layout_main');     
        $this->load->library('database_lib');                     
        $this->load->dbforge();
        $this->load->model('settings_model'); 
        require_once APPPATH."/libraries/install/install_lib.php";
    }

    /**
     * The index methods holds the upgrade logic.
     *
     */
    public function index() {      
       
        $continue = false;
        
        // Get version numbers both from code and from the database 
        $version_code= APP_VERSION;
        $version_obj = $this->settings_model->get_setting('app_%', TRUE);
        $version_db  = isset($version_obj->app_version) ? $version_obj->app_version : FALSE;

        // Check these two version numbers and only continue if the code version
        // number is more recent than the database version number
        if (!$version_db) {
            show_error("Error, 'app_version' is missing from the 'settings' table.");
        }
        if ($version_code < $version_db) {
            show_error("Error, the version number in code is less than the version in the 
                       database ($version_code &lt; $version_db->app_version)");
        }
        if ($version_code == $version_db) {
            $this->message("OK, there are no upgrades to run (the version number in code is 
                           the same as in the database, $version_code)");
        }
        if ($version_code > $version_db) {
            $this->message("There are upgrades to run. Searching...");
            $continue   = true;
        }
        
        if ($continue) {
			// Start a database transaction.
			$this->db->trans_start();
			
			// Change the version numbers into integer form (see comment in header)
			$version_try = $this->parse_version($version_db);
			$version_end = $this->parse_version($version_code);

			// Search for the relevant upgrade methods in this class.
			$functions = NULL;
			while ($version_try < $version_end) {
				$version_try++;
				
				$method = "_upgrade$version_try";
				if (method_exists($this, $method)) {
				    // If the method is found, run it and record the result
				    $result = call_user_func(array($this, $method));
				    $functions[ $method ] = $result;
				    if (TRUE !== $result) {
				        $continue = false;
				    }
				}
			}
			
			if (!$functions) {
				$this->message("Oh dear, no upgrade functions found. This may be an error.", 
			                   'warn');
				$continue = false;
			} 
			
          // End the database transaction i.e. either auto-commit or rollback
          $db_status = $this->db->trans_status();
          $this->db->trans_complete();
        }
        if ($continue) {
            $success = $this->settings_model->replace_setting('app_version',  APP_VERSION);
            $success = $this->settings_model->replace_setting('app_modified', time());
            // Log the result.
            $message_final = "OK, successfully upgraded database from version $version_db 
                              to $version_code";
            log_message("debug", "Upgraded database from version $version_db 
                                  to $version_code");
        } else {
            $message_final = "Failed to upgrade database from version $version_db 
                              to $version_code";
            log_message("error", "Failed to upgrade database from version $version_db 
                                 to $version_code");
        }

        $view_data = array(
          'title'    => 'Upgrading',
          'messages' => $this->messages,
          'message_final'=>$message_final,
          'functions'=> $functions,
          'success'  => $continue,
        );
        
        $this->layout->view('upgrade', $view_data);
    }

    /**
     * Add a message to the queue (use 'show_error' for fatal errors).
     * @param string $text
     * @param string $class One of 'info', 'warn', 'error'.
     */
    protected function message($text, $class='info') {
        $backtrace = debug_backtrace();
        $callee = $backtrace[1]['function'];
        $this->messages[] = (object) array('class'=>$class, 'text'=>$text, 
                                           'context'=>$callee);
    }

    /**
     * Convert "MAJOR.MINOR.REVISION" to a meaningful integer.
	 *     The function handles version strings suffixes, eg. "1.1.0-beta".
     *
     * @param string $dotted The version number in the form "MAJOR.MINOR.REVISION".
     * @return integer The version number as an integer
     */
    protected function parse_version($dotted) {
        #Naive:
		//(Not required: str_ireplace(array('-beta', '-dev', '-rc1'), '', $dotted); )
        $parts = explode('.', $dotted);
        //Was: $version = sprintf("_%d%02d%02d", $parts[0], $parts[1], $parts[2]);
        $version = sprintf("_%d%d%d", $parts[0], $parts[1], $parts[2]);
        return $version;
    }

    /**
     * Add the Message system tables
     *
     *  @return mixed TRUE on success; FALSE or string on error.
     */
    protected function _upgrade_103() {
        
        //***** Message table - start *****
        $table = 'message';
        if ($this->db->table_exists($table)) {
            $this->message("Woops, '$table' table already exists.", 'error');
        }
        else {
          $fields = array(
              'message_id' => array(
                  'type'            => 'INT',
                  'constraint'      => 10,
                  'auto_increment'  => TRUE
              ),
              'thread_id' => array(
                  'type'            => 'INT',
                  'constraint'      => 10,
                  'null'            => FALSE,
              ),
              'author_user_id' => array(
                  'type'            => 'INT',
                  'constraint'      => 10,
                  'null'            => FALSE,
              ),
              'content' => array(
                  'type'            => 'longtext',
                  'null'            => TRUE,       
              ),
              'created' => array(
                  'type'            => 'INT',
                  'constraint'      => 10,
                  'null'            => FALSE,
              ),
          );
  
          $this->dbforge->add_field($fields);
          $this->dbforge->add_key('message_id', TRUE);
          $this->dbforge->create_table($table);
          $this->message("OK, created table '$table'.");
        }
        //***** Message table - end *****

        //***** Message_recipient table - start *****
        $table = 'message_recipient';
        if ($this->db->table_exists($table)) {
            $this->message("Woops, '$table' table already exists.", 'error');
        }
        else {
          $fields = array(
              'message_id' => array(
                  'type'        => 'INT',
                  'constraint'  => 10,
              ),
              'recipient_user_id' => array(
                  'type'        => 'INT',
                  'constraint'  => 10,
                  'null'        => FALSE,
              ),
              'is_new' => array(
                  'type'        => 'TINYINT',
                  'null'        => FALSE,  
                  'default'     => 1,
                  'constraint'  => 1,
              ),
              'is_spam' => array(
                  'type'        => 'TINYINT',
                  'null'        => FALSE,
                  'default'     => 0,
                  'constraint'  => 1,
              ),
              'is_deleted' => array(
                  'type'        => 'TINYINT',
                  'null'        => FALSE,
                  'default'     => 0,
                  'constraint'  => 1,
              ),
          );
  
          $this->dbforge->add_field($fields);
          $this->dbforge->add_key('message_id', TRUE);
          $this->dbforge->add_key('recipient_user_id', TRUE);
          $this->dbforge->create_table($table);
          $this->message("OK, created table '$table'.");
        }
        //***** Message_recipient table - end *****

        //***** Message_thread_participant table - start *****
        $table = 'message_thread_participant';
        if ($this->db->table_exists($table)) {
            $this->message("Woops, '$table' table already exists.", 'error');
        }
        else {
          $fields = array(
              'thread_id' => array(
                  'type'        => 'INT',
                  'constraint'  => 10,
              ),
              'participant_user_id' => array(
                  'type'        => 'INT',
                  'constraint'  => 10,
                  'null'        => FALSE,
              ),
              'is_deleted' => array(
                  'type'        => 'TINYINT',
                  'null'        => FALSE,  
                  'default'     => 0,
                  'constraint'  => 1,
              ),
              'is_archived' => array(
                  'type'        => 'TINYINT',
                  'null'        => FALSE,
                  'default'     => 0,
                  'constraint'  => 1,
              ), 
          );
  
          $this->dbforge->add_field($fields);
          $this->dbforge->add_key('thread_id', TRUE);
          $this->dbforge->add_key('participant_user_id', TRUE);
          $this->dbforge->create_table($table);
          $this->message("OK, created table '$table'.");
        }
        //***** Message_thread_participant table - end *****         

        //***** message_thread table - start *****
        $table = 'message_thread';
        if ($this->db->table_exists($table)) {
            $this->message("Woops, '$table' table already exists.", 'error');
            //return FALSE;
        }
        else {
          $fields = array(
              'thread_id' => array(
                  'type'        => 'INT',
                  'constraint'  => 10,
                  'auto_increment'  => TRUE                
              ),
              'subject' => array(
                  'type'        => 'VARCHAR',
                  'constraint'  => 255,
                  'null'        => TRUE,
              ),
              'author_user_id' => array(
                  'type'        => 'INT',
                  'null'        => FALSE,
                  'constraint'  => 10,
              ),
              'created' => array(
                  'type'        => 'INT',
                  'null'        => TRUE,
                  'constraint'  => 10,
              ),
          );
  
          $this->dbforge->add_field($fields);
          $this->dbforge->add_key('thread_id', TRUE);
          $this->dbforge->create_table($table);
          $this->message("OK, created table '$table'.");
        }
        //***** message_thread table - end *****     

        //***** user_profile table - start *****                               
        $table        = 'user_profile';
        $field        = 'email_message_notify';
        $field_exists = $this->database_lib->check_field_exists($table,$field);
        if(!$field_exists) {
          $fields = array(
              'email_message_notify'  => array(
                'type'                  => 'INT',
                'default'               => 1,
                'constraint'            => 1,            
                'null'                  => FALSE,          
              ),      
          );
          $this->dbforge->add_column($table, $fields);
          $this->message("OK, altered table '$table' by adding column '$field'.");
        } 
        else {
          $this->message("Did not alter table '" .$table ."', column '" .$field ."' already exists.", 'error');
        }                    
        //***** user_profile table - start *****
                            
        return TRUE;
    }

}
