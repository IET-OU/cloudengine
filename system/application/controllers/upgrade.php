<?php

/**
 * Controller for site upgrades 
 * 
 * Version numbers have the form "MAJOR.MINOR.REVISION", 
 * eg. 1.0.1. 
 * These are converted to an integer with padding e.g.10001
 * The upgrade function for an upgrade to a version is then 
 * e.g. _upgrade_NNNNN()
 * When adding a new upgrade function to this file, make sure 
 * that you update APP_VERSION in 
 * /system/application/libraries/install/install_lib.php 
 * 
 * @copyright 2009, 2010, 2012 The Open University. See CREDITS.txt
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
        // Get version numbers both from code and from the database 
        $version_code= APP_VERSION;
        $version_obj = $this->settings_model->get_setting('app_%', TRUE);
        $version_db  = isset($version_obj->app_version) ? $version_obj->app_version : FALSE;        
        //if the user has clicked to proceed with the upgrade
        
        if ($this->input->post('submit')) {
          
    			$this->db->trans_start();
    			// Start a database transaction.
    			
    			// Change the version numbers into integer form (see comment in header)
    			$version_try = $this->parse_version($version_db);
    			$version_end = $this->parse_version($version_code);
    
    			// Search for the relevant upgrade methods in this class.
    			$functions = NULL;
    			while ($version_try < $version_end) {
    				$version_try++;
    				$method = "_upgrade$version_try";
    				if (method_exists($this, $method)) {
                $result = call_user_func(array($this, $method),'do_update');
    				}
    			}
    			
          // End the database transaction i.e. either auto-commit or rollback
          $this->db->trans_complete();
          $continue = $this->db->trans_status();          
            
            if ($continue) {
                $success = $this->settings_model->replace_setting('app_version',  APP_VERSION);
                $success = $this->settings_model->replace_setting('app_modified', time());
                $message_final = t("Successfully upgraded database from version $version_db 
                                  to $version_code");
                log_message("debug", t("Upgraded database from version $version_db 
                                      to $version_code"));
            } else {
                $message_final = t("Failed to upgrade database from version $version_db 
                                  to $version_code");
                log_message("error", t("Failed to upgrade database from version $version_db 
                                     to $version_code."));
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
        //we are showing the pre-upgrade information page
        else {
          
          $continue = false;    
          
          // Check these two version numbers and only continue if the code version
          // number is more recent than the database version number
          if (!$version_db) {
              $this->message(t("Error, 'app_version' is missing from the 'settings' table."),'error');
          }
          if ($version_code < $version_db) {
              $this->message(t("Error, the version number in code is less than the version in the 
                         database ($version_code &lt; $version_db)"),'error');
          }
          if ($version_code == $version_db) {
              $this->message(t("There are no upgrades to run (the version number in code is 
                             the same as in the database), $version_code"));
          }
          if ($version_code > $version_db) {
              $continue   = true;
          }       
          
          if ($continue) {
            
      			// Search for the relevant upgrade methods in this class.
      			$functions = NULL;
            
      			// Change the version numbers into integer form (see comment in header)
      			$version_try = $this->parse_version($version_db);
      			$version_end = $this->parse_version($version_code);          
            
            while ($version_try < $version_end) {
      				$version_try++;
      				$method = "_upgrade$version_try";
      				if (method_exists($this, $method)) {
      				    // If the method is found, call it to get upgrade message only
      				    $result = call_user_func(array($this, $method),'get_message');
      				    $functions[ $method ] = $result;
      				    if (TRUE !== $result) {
      				        $continue = false;
      				    }
      				}
      			}
      			
      			if (!$functions) {
      				$this->message(t("Oh dear, no upgrade functions found. This may be an error"),'warn');
              $continue = FALSE;
      			}
            
          }
          
          $view_data = array(
            'title'         => t('Upgrade steps...'),
            'version_old'   => $version_db,
            'version_new'   => $version_code,
            'messages'      => $this->messages,
            'functions'     => $functions,
            'continue'      => $continue,
          );  
                
          $this->layout->view('upgrade_confirm', $view_data);          
          
        }
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
     function _upgrade_103($action) {
        
        if ($action == 'get_message') {        
          $this->message("Create 'message' table (messaging system)");
          $this->message("Create 'message_recipient' table (messaging system)");
          $this->message("Create 'message_thread_participant' table (messaging system)");
          $this->message("Create 'message_thread' table (messaging system)");
          $this->message("Add 'email_message_notify' field to 'user_profile' table (messaging system)");   
          return TRUE;       
        }
        
        elseif ($action == 'do_update') {
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
          //***** user_profile table - end *****
                              
          return TRUE;
        }
    }

    /**
     * Modify the 'user' table to change the 'banned' field to be called 'inactive'
     *
     *  @return mixed TRUE on success; FALSE or string on error.
     */
     function _upgrade_111($action) {
        
        $table        = 'user_profile';
        $field        = 'deleted';
        
        if ($action == 'get_message') {        
          $this->message("Modify '$table' table, add field '$field'");
          return TRUE;       
        }
        
        elseif ($action == 'do_update') {
  
          //***** user_profile table - start *****                               
          $field_exists = $this->database_lib->check_field_exists($table,$field);
          if(!$field_exists) {
            $fields = array(
                $field  => array(
                  'type'                  => 'INT',
                  'default'               => 0,
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
          //***** user_profile table - end *****
                              
          return TRUE;
        }
    }


    /**
     * Add debug values to the settings table 
     *
     *  @return mixed TRUE on success; FALSE or string on error.
     */
     function _upgrade_112($action) {
        
        $table        = 'settings';
        $field        = 'name';
        $value        = 'debug';
        $new_field    = 'notes';                
        
        if ($action == 'get_message') {        
          $this->message("Add '$new_field' field to '$table' table");             
          $this->message("Add '$value' value to the '$table' table");          
          return TRUE;       
        }
        
        elseif ($action == 'do_update') {

          //***** notes field - start *****                               
          $field_exists = $this->database_lib->check_field_exists($table,$new_field);
          if(!$field_exists) {
            $fields = array(
                'notes' => array(
                    'type'        => 'VARCHAR',
                    'constraint'  => 128,
                    'null'        => TRUE,
                ),    
            );
            $this->dbforge->add_column($table, $fields);
            $this->message("OK, altered table '$table' by adding column '$field'.");
          } 
          else {
            $this->message("Did not alter table '" .$table ."', column '" .$field ."' already exists.", 'error');
          }                    
          //***** notes field - end *****
  
          //***** debug value - start *****                               
          $value_exists = $this->database_lib->check_value_exists($table,$field,$value);
          if(!$value_exists) {
            $query        = " INSERT INTO `settings` 
                              VALUES ('debug', 
                                      '1', 
                                      'Show debug output', 
                                      'Show PHP errors and Firephp output', 
                                      'debug', 
                                      'select_list',
                                      '0 debug is off, 1 debug for admin users, 2 debug for all users (emergency use only)'
                                      );"; 
            $result       = mysql_query($query);                   
            $this->message("OK, added '$value' value to the '$table' table.");
          } 
          else {
            $this->message("Did not add data to '" .$table ."' table, value '" .$value ."' already exists.", 'error');
          }                    
          //***** debug value - end *****   
                              
          return TRUE;
        }
    }
    
    /**
     * Add moderation flag fields to the message tables
     *
     *  @return mixed TRUE on success; FALSE or string on error.
     */
     function _upgrade_113($action) {
        
        $table1       = 'message';
        $table2       = 'message_thread';
        $field        = 'moderate';                
        
        if ($action == 'get_message') {        
          $this->message("Add '$field' field to '$table1' table");
          $this->message("Add '$field' field to '$table2' table");                       
          return TRUE;       
        }
        
        elseif ($action == 'do_update') {

          //***** message table, moderate field - start *****                               
          $field_exists = $this->database_lib->check_field_exists($table1,$field);
          if(!$field_exists) {
            $fields = array(
                $field  => array(
                  'type'                  => 'INT',
                  'default'               => 0,
                  'constraint'            => 1,            
                  'null'                  => FALSE,          
                ),    
            );
            $this->dbforge->add_column($table1, $fields);
            $this->message("OK, altered table '$table1' by adding column '$field'.");
          } 
          else {
            $this->message("Did not alter table '" .$table1 ."', column '" .$field ."' already exists.", 'error');
          }                    
          //***** message table, moderate field - end *****

          //***** message table, moderate field - start *****                               
          $field_exists = $this->database_lib->check_field_exists($table2,$field);
          if(!$field_exists) {
            $fields = array(
                $field  => array(
                  'type'                  => 'INT',
                  'default'               => 0,
                  'constraint'            => 1,            
                  'null'                  => FALSE,          
                ),    
            );
            $this->dbforge->add_column($table2, $fields);
            $this->message("OK, altered table '$table2' by adding column '$field'.");
          } 
          else {
            $this->message("Did not alter table '" .$table2 ."', column '" .$field ."' already exists.", 'error');
          }                    
          //***** message table, moderate field - end *****
                              
          return TRUE;
        }
    }    
    
  /**
    * Add fields to the user table to store information so that
    * users can change their email address 	
    * Also increase size of email field to 254, the maximum
    * length of an email. 
    */  
    function _upgrade_114($action) {
        if ($action == 'get_message') {        
            $this->message("Add 'change_email_code' column to 
                            'user' table");
            $this->message("Add 'new_email' column to 'user' 
                            table");
            $this->message("Modify 'email' column in 'user' 
                            table");
       
        } elseif ($action == 'do_update') {
            // Add the email_change_code column to the user 
            // table
            $field_exists = 
            $this->database_lib->check_field_exists('user',
                                       'change_email_code');
            if (!$field_exists) {
                $fields = array(
                    'change_email_code' => array(
                    'type'              => 'VARCHAR',
                    'constraint'        => 50,                    
                    ),    
                );
                $this->dbforge->add_column('user', $fields);
                $this->message("OK, altered table 'user' by 
                                adding column 
                                'change_email_code'.");
            } else {
                $this->message("Did not alter table 'user', 
                                column 'change_email_code' 
                                already exists.", 'error');
            }
            
            // Add the new_email column to the user table
            $field_exists = 
                $this->database_lib->check_field_exists('user',
                                           'new_email');
            if (!$field_exists) {
                $fields = array(
                    'new_email'  => array(
                    'type'       => 'VARCHAR',
                    'constraint' => 254,                    
                    ),    
                );
                $this->dbforge->add_column('user', $fields);
                $this->message("OK, altered table 'user' by 
                                adding column 'new_email'.");
            } else {
                $this->message("Did not alter table 'user', 
                                column 'new_email' already 
                                exists.", 'error');
            }

            // Change the number of characters for the email
            // column of the user table to 254, the maximum 
            // length of an email address. See 
            // http://stackoverflow.com/questions/386294/maximum-length-of-a-valid-email-address
            $fields = array('email' =>
                            array(
                           'name' => 'email',
                            'type'=>'VARCHAR',
                            'constraint'=> 254));
            $this->dbforge->modify_column('user', $fields);
            $this->message("OK, altered table 'user' by 
                                modifying column 'email'.");
        }

        return TRUE;   
    }
   /**
    * Add an event_date and display_event field to the cloud table
    * and display_event field to the cloudscape table
    */    
    function _upgrade_115($action) {
        if ($action == 'get_message') {        
            $this->message("Add 'event_date' column to 'cloud' table"); 
            $this->message("Add 'display_event' column to 'cloud' table"); 
            $this->message("Add 'display_event' column to 'cloudscape' table");            
        } elseif ($action == 'do_update') {
            // Add the event_date column to the cloud table
            $field_exists = 
            $this->database_lib->check_field_exists('cloud',
                                       'event_date');
            if (!$field_exists) {
                $fields = array(
                    'event_date' => array(
                    'type'              => 'integer',
                    'constraint'        => 10,                    
                    ),    
                );
                $this->dbforge->add_column('cloud', $fields);
                $this->message("OK, altered table 'cloud' by 
                                adding column 
                                'event_date'.");
            } else {
                $this->message("Did not alter table 'cloud', 
                                column 'event_date' 
                                already exists.", 'error');
            }
            // Add the display_event column to the cloud table
            $field_exists = 
            $this->database_lib->check_field_exists('cloud',
                                       'display_event');
            if (!$field_exists) {
                $fields = array(
                    'display_event' => array(
                    'type'          => 'tinyint',
                    'constraint'    => 1, 
                    'default'       => 1,
                    ),    
                );
                $this->dbforge->add_column('cloud', $fields);
                $this->message("OK, altered table 'cloud' by 
                                adding column 
                                'display_event'.");
            } else {
                $this->message("Did not alter table 'cloud', 
                                column 'display_event' 
                                already exists.", 'error');
            }  
            
            // Add the display_event column to the cloudscape table
            $field_exists = 
            $this->database_lib->check_field_exists('cloudscape',
                                       'display_event');
            if (!$field_exists) {
                $fields = array(
                    'display_event' => array(
                    'type'          => 'tinyint',
                    'constraint'    => 1, 
                    'default'       => 1,
                    ),    
                );
                $this->dbforge->add_column('cloudscape', $fields);
                $this->message("OK, altered table 'cloudscape' by 
                                adding column 
                                'display_event'.");
            } else {
                $this->message("Did not alter table 'cloudscape', 
                                column 'display_event' 
                                already exists.", 'error');
            }              
          
        }

        return TRUE;               
    }
    
   /**
    * Add an moderate column to user_profile table
    */    
    function _upgrade_116($action) {
        if ($action == 'get_message') {        
            $this->message("Add 'moderate' column to 'user_profile' table");           
        } elseif ($action == 'do_update') {
            // Add the event_date column to the cloud table
            $field_exists = 
            $this->database_lib->check_field_exists('moderate',
                                       'user_profile');
            if (!$field_exists) {
                $fields = array(
                    'moderate' => array(
                    'type'          => 'tinyint',
                    'constraint'    => 1, 
                    'default'       => 0,                  
                    ),    
                );
                $this->dbforge->add_column('user_profile', $fields);
                $this->message("OK, altered table 'user_profile' by 
                                adding column 
                                'moderate'.");
            } else {
                $this->message("Did not alter table 'user_profile', 
                                column 'moderate' 
                                already exists.", 'error');
            }
        }
        
        return TRUE;               
    }    
    
    function _upgrade_117($action) {
        if ($action == 'get_message') {        
            $this->message("Add 'badge' table"); 
            $this->message("Add 'badge_application' table"); 
            $this->message("Add 'badge_decision' table"); 
            $this->message("Add 'badge_verifier' table");              
        } elseif ($action == 'do_update') {
          //***** Badge table - start *****
          $table = 'badge';
          if ($this->db->table_exists($table)) {
              $this->message("Woops, '$table' table already exists.", 'error');
          }
          else {
            $fields = array(
                'badge_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'auto_increment'  => TRUE
                ),
                'name' => array(
                    'type'            => 'VARCHAR',
                    'constraint'      => 128,
                    'null'            => FALSE,
                ),
                'image' => array(
                    'type'            => 'VARCHAR',
                    'constraint'      => 128,
                    'null'            => FALSE,
                ),
                'description' => array(
                    'type'            => 'VARCHAR',
                    'constraint'      => 128,
                    'null'            => FALSE,
                ),
                'criteria' => array(
                    'type'            => 'TEXT',
                    'null'            => FALSE,
                ),
                'user_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),
                'created' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),
                'modified' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                ),   
                'type' => array(
                    'type'            => 'ENUM',
                    'constraint'      => array('verifier', 'crowdsource'),
                    'default'         => 'verifier',
                    'null'            => FALSE,
                ), 
                'num_approves' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                ),                 
            );
    
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('message_id', TRUE);
            $this->dbforge->create_table($table);
            $this->message("OK, created table '$table'.");   

          //***** Badge Application table - start *****
          $table = 'badge_application';
          if ($this->db->table_exists($table)) {
              $this->message("Woops, '$table' table already exists.", 'error');
          }
          else {
            $fields = array(
                'application_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'auto_increment'  => TRUE
                ),
                'evidence_URL' => array(
                    'type'            => 'VARCHAR',
                    'constraint'      => 2048,
                    'null'            => FALSE,
                ),
                'user_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),
                'badge_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),
                'evidence_URL' => array(
                    'type'            => 'VARCHAR',
                    'constraint'      => 2048,
                    'null'            => FALSE,
                ),                
                'created' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),   
                'status' => array(
                    'type'            => 'ENUM',
                    'constraint'      => array('pending', 'approved', 'rejected')
                    'default'         => 'pending',
                    'null'            => FALSE,
                ),                 
            );
    
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('message_id', TRUE);
            $this->dbforge->create_table($table);
            $this->message("OK, created table '$table'.");   
          //***** Badge Decision table - start *****
          $table = 'badge_decision';
          if ($this->db->table_exists($table)) {
              $this->message("Woops, '$table' table already exists.", 'error');
          }
          else {
            $fields = array(
                'application_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                ),
                'user_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),
                'feedback' => array(
                    'type'            => 'TEXT',
                    'null'            => FALSE,
                ),             
                'timestamp' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),   
                'decision' => array(
                    'type'            => 'ENUM',
                    'constraint'      => array('approved', 'rejected'),
                    'default'         => 'pending',
                    'null'            => FALSE,
                ),                 
            );
    
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('message_id', TRUE);
            $this->dbforge->create_table($table);
            $this->message("OK, created table '$table'.");  

          //***** Badge Verifier table - start *****
          $table = 'badge_verifier';
          if ($this->db->table_exists($table)) {
              $this->message("Woops, '$table' table already exists.", 'error');
          }
          else {
            $fields = array(
                'user_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),
                'badge_id' => array(
                    'type'            => 'INT',
                    'constraint'      => 11,
                    'null'            => FALSE,
                ),                
            );
    
            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('message_id', TRUE);
            $this->dbforge->create_table($table);
            $this->message("OK, created table '$table'.");               
        }
    }
 }
