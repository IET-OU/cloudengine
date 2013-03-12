<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Installer library.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Install
 * 
 * Some of the install code was inspired by or copied with permission from
 * Phil Sturgeon's PyroCMS install code. The PyroCMS library can be found here: 
 * http://github.com/pyrocms/pyrocms/blob/master/installer/libraries/installer_lib.php 
 *
 * Note that The code does not currently do the following, but ought to! 
 * - Check MySQL server version.
 * - Check the MySQL client version (tested on v5.0.77)
 * - Check the GD version (tested on 2.0.28)
 * - Check the il8n requirements e.g. gettext
 * - Check the Apache version
 */

// THE application version number. 
// This needs to be changed for new releases and also whenever there is a new upgrade function 
// that needs to be run e.g. if there are database changes. 
define("APP_VERSION", "1.1.8");


class Install_lib {
	const EXAMPLE_IMAGE = 'flickr_nickrussil_146743083.jpg'; // An example cloudscape image
	// to use in the default install
	const INSTALL_LIVE  = TRUE; // If FALSE, continues the install even if database tables
	// already exist
	
	private $ci;
	private $db;
	
	protected $messages = array();
	protected $install_path;
	
	function __construct() {
		$this->ci =& get_instance();
		$this->install_path = dirname(__FILE__); // Set the install path to the directory 
		// of this file
	}

	public function get_messages() {
		return $this->messages;
	}

	/** 
	 * Function to retrieve the PHP version. we need at least v 5.0.
	 * @return boolean TRUE if the PHP version is v 5.0 or higher, FALSE otherwise
	 */
	function get_php_version() {
		// Set the PHP version
		$php_version = phpversion();

		// Validate the version
		return ($php_version >= 5) ? $php_version : FALSE;
	}

	/** 
	* Check php.ini configuration settings and PHP extensions. 
	*/
	function _check_php_ini() {
		$ini_path = 'php.ini'; // Only used for user-messages. For older version of PHP, we 
		// can't give full path in error messages :(
		if (function_exists('php_ini_loaded_file')) {
			$ini_path = php_ini_loaded_file();
		}
		$required_ini = array('short_open_tag' => 'On',);
		$required_extensions = array('gd2'  => 'gd_info', 
		                             'cURL' => 'curl_version', 
		                             'MySQL'=> 'mysql_get_client_info',
		);
	
		// Check each of the php.ini settings.
		foreach ($required_ini as $key => $required) {
	  		$agree = false;
	  		$setting = ini_get($key);
			
			if (is_numeric($setting)) {
				$agree = $setting == ($required=='On' ? true: false);
			} elseif (is_string($setting)) {
				$agree = ($setting == $required);
		    }
  		
		    if (!$agree) {
      		     $error = <<<MSG
<p>The php.ini configuration needs changing. '<code><strong>$key = $required</strong></code>' is required (currently '$setting'). Aborting.</p>
<pre>\t$ini_path</pre><p>Please fix the issue in the above file, restart the web-server then try again.</p>
MSG;

				install_error($error);
  			}
		}

		// Check each of the extensions.
		foreach ($required_extensions as $extension => $function) {
			if (!function_exists($function)) {
				$is_windows = ("\\"==DIRECTORY_SEPARATOR);
				$ext_name = strtolower($extension);
				$ext_name = $is_windows ? "php_$extension.dll" : "$extension.so";
		    	$error = <<<MSG
<p>A PHP extension needs to be enabled in php.ini. '<code>extension=<strong>$ext_name</strong></code>' is required. Aborting.</p>
<pre>\t$ini_path</pre>
<p>Please fix the issue in the above file, restart the web-server then try again.</p>
MSG;
		
		  	install_error($error);
			}
		}
		
		// Everything was ok, so set an appropriate message
	    $this->messages[] = "OK, checked php.ini configuration. <code>[$ini_path]</code>";
	    $this->messages[] = "OK, checked required PHP extensions (".implode(", ", 
	                         array_keys($required_extensions)).")";
	}

    /**
     * Get the application config items from the config files that are needed for 
     * Step 0 of the installation and that need to be validated in Step 1. 
     * specifically, get the site_email and data_dir config items
     *
     * @return array Array of the config items 
     */
	function get_app_config() {
		$config = array();
		$vars   = array('site_email', 'data_dir'); 
		foreach ($vars as $var) {
		  	$config[$var] = $this->ci->config->item($var);
		}

		return $config; 
	}

	/** 
	 * Validates the config returned by get_app_config()
	 * In particular, check that
	 * - site_email is a non-empty string and is a valid e-mail
	 * - data_dir os a non-empty string and exists as a directory 
	 * This function is called in Step 1 of the installation. 
	 */
	function validate_app_config() {
		$this->ci->load->library('form_validation');
		$validation = $this->ci->form_validation;
		
		$configs = $this->get_app_config();
		
		$message_end = PHP_EOL."<p>Please fix <code>config/cloudengine.php</code> and try the 
		               installation again.</p>";
		// All the config variables are 'required' - check this 
		foreach ($configs as $key => $value) {
		    if (!$validation->required($value)) {
		        install_error("<p>Error, the configuration variable '$key' is required.</p>$message_end");
		    }
		    
		    switch ($key) {
		    	case 'site_email':
			        if (!$validation->valid_email($value)) {
			        	install_error("<p>Error, the configuration variable '$key' is not 
			        	              valid.</p>$message_end");
			        }
		        break;
			    case 'data_dir':
			        if (!is_dir($value)) {
			        	install_error("<p>Error, the configuration variable '$key' is not a 
			        	               directory.</p>$message_end");
			        }
			        break;
			}
		}
		
		$this->messages[] = "OK, application configuration validates. <code>".implode(', ', array_keys($config))."</code>";
		return TRUE;
	}



	/** 
	 * Check if the data directory and required subdirectories data exist and are writeable.
	 */
	function _check_writeable() {
		// This array contains a list of directories that need to be checked along with 
		// default locations to be used in case there are none set in the config file. 
        // These are currently OS-dependent but should not be. 
		$writeable_dirs = array("data_dir"         => "/var/www/_data/", 
								"upload_path"      => "/var/www/_data/uploads/",
								"upload_path_user" => "/var/www/_data/uploads/user/",
								"upload_path_cloudscape" => "/var/www/_data/uploads/cloudscape/",
								"search_index_path"=> "/var/www/_data/search/index",
								"log_path" => BASEPATH.'logs/',  
								"FAL_captcha_image_path" => BASEPATH."../tmp", );
	
		$error_paths = null; // Used to record any paths for directories that
		// give errors during the checking process

		// Check each of the directories (with the location set in the config file
		// or the default set above if no config is set) exists and is writeable
		foreach ($writeable_dirs as $conf_key => $default) {
			$path = $this->ci->config->item($conf_key);

			if (!$path) {
				$path = $default;
			}
			$success = is_dir($path) && is_really_writable($path); 
			if ($success) {
				$this->messages[] = "OK, writeable, $path";
			} else {
				$error_paths[] = $path;
			}
		}

		if ($error_paths) {
	  		$error_paths = implode("\n", $error_paths);
      		$message =<<<MSG
<p>The following directories either do not exist or are not writeable.</p>
<pre>$error_paths</pre>
<p>Please check directory permissions and check <code>config/cloudengine.php</code>. Then try the installation again.</p>
MSG;
    		install_error($message);
		}
	}

    /**
     * Test the database server connection. Currently only MySQL supported. 
     *
     * @param unknown_type $db_conf The database connection
     * @return unknown
     */
	function _test_db_connection($db_conf) {
		$this->db = @mysql_connect("$db_conf->hostname:$db_conf->port", $db_conf->username, $db_conf->password);
		if (!$this->db) {
		  	install_error('The database connection failed. '.mysql_error());
		}
		return $this->db;
	}

	/**
	 * If the database exists and contains tables (or more specifically the user
	 * table) then display an error. 
	 * Also store any relevant informational/error messages for debug purposes when
	 * connecting to the database and making the query. 
	 *
	 * @param unknown_type $db_conf
	 * @return boolean FALSE if no tables exist in the database or the database does 
	 * not exist, should not return anything else
	 */
	function _db_tables_exist($db_conf) {
		//Note, we can't use the built in 'CI' functions, as they automatically 
		// give a CI error page on error.
		if (mysql_select_db($db_conf->database, $this->db)) {
	    	// The database exists, so try querying it. 
	    	$this->messages[] = 'OK, database exists. Try loading and querying.';
	
	    	// If the user table exists, assume it's not a clean install and abort
	    	$result = @mysql_query("SELECT * FROM user WHERE id = 1", $this->db);

		    if ($result) {
		        log_message('error', __CLASS__.": installation attempted. DB tables already exist. Aborting.");
		        if (self::INSTALL_LIVE) {
		        	show_404('install');  // Security - 'hide' the installer when 
		        	// installation is complete.
		        }
		        $this->messages['error'] = "[ Debug, database _does_ exist. ]";
		    } else {
	        	$this->messages[] = mysql_error($this->db); 
	    	}
		 } else {
		   $this->messages[] = "OK, I'll create the database (it does not exist).";
		 }
	 	return FALSE; 
	}

	/**
	 * Install the database.
	 *
	 * @param unknown_type $db_conf
	 * @return TRUE if no errors occurred
	 */
	function install_db($db_conf) {
    	$messages = array();
		// Make sure there are no tables in the database if it exists
		// and then create the database if it does not exist
		if (!$this->_db_tables_exist($db_conf)) {
			$this->messages[] = "OK, creating database, $db_conf->database.";
			if (!@mysql_query('CREATE DATABASE IF NOT EXISTS '.$db_conf->database, $this->db)) {
			    install_error(' 1. '.mysql_error($this->db));
			}
			// Check the database exists now
			if (!mysql_select_db($db_conf->database, $this->db)) {
			    install_error(' 2. '.mysql_error($this->db));
			}
		}

		// Now create the tables	
    	$this->messages[] = "Creating tables...";
	    $num_tables = $this->_process_schema('tables'); 
    
	    if (!$num_tables) {
        	install_error(' 3. '.mysql_error($this->db));
    	}
	    // $num_tables is actually the number of queries in the schema file. We happen
	    // to know that half the queries drop tables and half create them so the actual
	    // number of tables is half this 
	    $num_tables = 0.5 * $num_tables;
	    $this->messages[] = "OK, $num_tables tables created successfully.";

    	return TRUE;
	}

	/**
	 * Insert the admin user.
	 *
	 * @param array $user The admin user details
	 * @return integer The ID of the admin user, or TRUE if an error occurred. 
	 */
	function _insert_admin_user($user) {
	    $this->ci->load->model('auth_model');
	    $user['password'] = $this->ci->auth_model->_hash_password($user['password']);

	    // Take the relevant information from the $user array and interpolate it 
	    // into the two SQL statements below appropriately
      	$user_sql = <<<EOF
  INSERT INTO `user` (`user_name`, `password`, `email`, `role`, `banned`)
  VALUES ('__USER_NAME__', '__PASSWORD__', '__EMAIL__', 'admin', 0)
EOF;
  	    $profile_sql = "INSERT INTO `user_profile`(`id`,`fullname`,`institution`,
  	    `whitelist`) VALUES(__ID__,'Administrator','An organization',1)";

		foreach ($user as $key => $value) {
		  	$search[] = '__'.strtoupper($key).'__';
		}
		$user_sql = str_replace($search, $user, $user_sql);
		
		// Run the first of the SQL statements adding the user data
		$success = $this->_process_schema($user_sql, FALSE);

		if ($success) {
			// If that was successful, run the second statement adding the profile data
		  	$profile['id'] = $user_id = mysql_insert_id($this->db);
		  	$profile_sql = str_replace('__ID__', $user_id, $profile_sql);
		  	$success = $this->_process_schema($profile_sql, FALSE);
		
		  	if ($success) {
		      	$this->messages[] = "Administrator account added (ID $user_id).";
		      	return $user_id;
		  	}
		}
		
    	install_error('Insert error - user, '.mysql_error($this->db));
      	return TRUE;
	}

	/**
	 * Insert example data into the database so new users don't face a completely empty site
	 *
	 * @param integer $user_id The ID of the user to use as the creator of the example
	 * content
	 * @return boolean TRUE if no errors occurred
	 */
	function _insert_data($user_id) {
		$data_sql = file_get_contents("$this->install_path/data.sql.php", FALSE);

		// Make sure the times when the example data is marked as being added is the 
		// same as the current time and make the user specified the creator
    	$event_time = time() + 7 *24*3600;
	  	$data_sql = str_replace(array('__USER_ID__', '__TIME__', '__EVENT_TIME__', 
	  	                        '__EXAMPLE_IMAGE__'), array($user_id, time(), $event_time, 
	  	                        self::EXAMPLE_IMAGE), $data_sql);

	  	$num_inserts = $this->_process_schema($data_sql, FALSE);

	    if ($num_inserts) {
			$this->messages[] = "OK, example cloud and cloudscape added to the site.";
	    } else {
	    	install_error('Insert error - data, '.mysql_error($this->db));
	    }

	    // Copy the example Cloudscape image into the appropriate directory
	    $cloudscape_upload = $this->ci->config->item("upload_path_cloudscape");
	    if ($cloudscape_upload) { //&& $image_file) {
	      	$image_dest  = $cloudscape_upload . self::EXAMPLE_IMAGE;  
	      	$image_source= $this->install_path. "/example/".self::EXAMPLE_IMAGE;  
	      	if (@copy($image_source, $image_dest)) {
	        	$this->messages[] = "OK, copied example Cloudscape image, ".self::EXAMPLE_IMAGE;
	      	} else {
	        	install_error('Error copying example Cloudscape image. '.$php_errormsg); #error_get_last() #PHP Warning: "failed to open stream: No such file or directory"
	      	}
	    } else {
	     	 install_error('Error copying example Cloudscape image (2).');
	    }
	    
    	return TRUE;
	}

	/**
	 * Load the dataabase config file
	 *
	 * @param unknown_type $group
	 * @return object The database connection group
	 */
	function _load_db_config($group = 'default') {
		$file = 'database';
		$fail_gracefully = FALSE;
	
		// Check to see if the database.php config file exists and if so include the file
		if (!file_exists(APPPATH.'config/'.$file.EXT)) {
			if ($fail_gracefully === TRUE) {
				return FALSE;
			}
			install_error('The configuration file <code>config/'.$file.EXT.'</code> does not 
			               exist.');
		}
	    
		include(APPPATH.'config/'.$file.EXT);
		
		// Check that the $db class variable has been set
		if (!isset($db) OR !is_array($db)) {
			if ($fail_gracefully === TRUE) {
				return FALSE;
			}
			install_error('Your <code>config/'.$file.EXT.'</code> file does not appear to 
			               contain a valid DB configuration array.');
		}
	
		// Check that the database connection group is set
		if (!isset($db[$group]) OR !is_array($db[$group])) {
			if ($fail_gracefully === TRUE) {
				return FALSE;
			}
			install_error('Your <code>config/'.$file.EXT.'</code> file does not appear to 
			               contain the DB connection group, '.$group);
		}
		
		$this->ci->input->xss_clean($db);
	
		return (object) $db[$group]; 
	}

	/**
	* Validate the database configuration. This is fairly basic at the moment. We might
	* want to check things like the character encoding in future. 
	*
	* @param unknown_type $db_conf
	* @return TRUE if no errors occurred
	*/
	function _validate_db_config($db_conf) {
		$vars = array('hostname', 'database', 'username', 'dbdriver', 'char_set', 'dbcollat');
		foreach ($vars as $var) {
			if (!isset($db_conf->{$var}) || empty($db_conf->{$var})) {
				install_error("Error in database configuration. <code>config/database.php :
			                 \$db[GROUP]['$var'] Please check and try again.");
			}
		}
		// It is ok for a database password to be empty e.g. the default database password
		// for XAMPP is empty. 
		$var = 'password';
		if (!isset($db_conf->{$var}) || !is_string($db_conf->{$var})) {
			install_error("Error in database configuration. <code>config/database.php : 
		                 \$db[GROUP]['$var'] Please check and try again.");
		}
		
		return TRUE;
	}

	/**
	 * Process a DB schema, returning number of queries or FALSE on error.
	 *
	 * @param string $schema_file Path of the schema file or a string containing
	 * the SQL to be processed
	 * @param boolean $is_file TRUE if the first parameter is the path to a file, 
	 * FALSE if it a string containing SQL queries. 
	 * @return integer The number of queries processed, or FALSE if an error occurred. 
	 */
	function _process_schema($schema_file, $is_file = TRUE) {
		if ($is_file) {
		    $schema = file_get_contents("$this->install_path/$schema_file.sql.php");
		} else {
		    $schema = $schema_file;
		}
	
		$queries = explode('-- command split --', $schema);
	
		foreach ($queries as $query) {
			$query = rtrim(trim($query), " \n;"); #?
			
			@mysql_query($query, $this->db);
			log_message('debug', __CLASS__.", SQL: $query");
			if (mysql_errno($this->db) > 0) {
				return FALSE;
			}
		}
		return count($queries);
	}

	/**
	 * Create the search index. 
	 * HACK: Currently disabled due to reported errors returned
	 * by file_get_contents in Zend../Document/Html.php (though 'allow_url_fopen' was 
	 * 1 in php.ini).
	 *
	 * @return boolean TRUE if successful
	 */
	function _create_search_index() {
		return TRUE;
		/*
		$this->ci->load->model('search_model');
		$message = $this->ci->search_model->create_index();
		if (is_string($message)) {
		  $this->messages['error'] = "Error creating search index. $message";
		  return FALSE;
		} else {
		  $this->messages[] = "OK, created search index.";
		}
		return TRUE;
		*/
	}

	/**
	 * Set the 'app_version' in the 'settings' table.
	 */
	function _set_version() {
		$version_sql =  "INSERT INTO settings (name, value, title, description, admin_output_section, type) 
                    VALUES	('app_version','__VERSION__', 'Software version', 'Latest software version', 'software_version', 'text'), 
                            ('app_created', '__TIME__', 'Version release date', 
                                'Release date of the latest software version', 'software_version', 'date')
                    ";
		$version_sql = str_replace(array('__VERSION__', '__TIME__'), array(APP_VERSION, time()), $version_sql);
		$num_inserts = $this->_process_schema($version_sql, FALSE);
		if ($num_inserts) {
			$this->messages[] = "OK, set application version number.";
		} else {
			install_error("Error writing application version number.");
		}
	}
}

/**
 * Output an error page, with a "Try again" button, and exit.
 *
 * @param string $message The error message to display
 * @param unknown_type $config
 */
function install_error($message, $config = array()) {
    @header("HTTP/1.1 500 Internal Server Error", TRUE, 500);
    $view_data = array('site_name'=> Install::SITE_NAME,
                       'message'  => $message,
                       'config'   => $config);
    $CI =& get_instance();
    $output = $CI->layout->view('install/install_error', $view_data, $return = TRUE);
	die($output);
}