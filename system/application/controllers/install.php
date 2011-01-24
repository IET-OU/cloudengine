<?php
/**
 * Controller for the installer
 * @see system/application/libraries/install
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Install
 */
class Install extends MY_Controller {

	const SITE_NAME = "CloudEngine installer";
	
	public function __construct() {
		parent::MY_Controller();
		
		$this->load->library('layout', array('layout_install'));
		$this->load->library('install/install_lib', NULL, 'lib');
	}
	
	/** 
	 * Form validation callback. Used in Install_lib::validate_app_config().
	 */
	function _is_dir($test) { 
		return is_dir($test); 
	}
	
	/** 
	 * Step 0 of the install script, or if specified, pass the request to the appropriate 
	 * step of the install script 
	 */
	public function index() {
		$nextstep = $this->input->post("nextstep");
		if ($nextstep) {
			    if (is_numeric($nextstep) && method_exists($this, "_step_$nextstep")) {
			      	$this->{"_step_$nextstep"}();
			      	return;
			    } else {
			      	install_error("Installer, step error, $nextstep");
			    }
		}
	
		$view_data = array(
		    'site_name'=> self::SITE_NAME,
		    'step'     => 0,
		    'config'   => $this->lib->get_app_config(),
		);
		$this->layout->view('install/step0', $view_data);
	}
	
	/** 
	 * Step 1 of the install script: pre-installation checks and display the 'admin' account 
	 * form.
	 */
	protected function _step_1() {
		# Check the PHP version 
		$php_version = $this->lib->get_php_version();
		if ($php_version) {
		    $messages[] = "OK, PHP version $php_version detected.";
		} else {
		    install_error("Error, ".self::SITE_NAME." requires PHP version 5.0 or greater.");
		}
	
		// Check the php.ini settings
		$this->lib->_check_php_ini();
		
		// Check that the required config has  been set and is valid
		$success = $this->lib->validate_app_config();
		
		// Check that the required data directories exists and are writeable
		$this->lib->_check_writeable();

		// Check database configuration
		$db_conf = $this->lib->_load_db_config();
		if ($db_conf) {
		    $messages[] = "OK, database configuration read successfully.";
		}

		$success = $this->lib->_validate_db_config($db_conf);
		
		if ($success) {
		    $messages[] = "OK, database configuration parsed successfully.";
		} 

		// Test the database connection.
		$db = $this->lib->_test_db_connection($db_conf);
		if ($db) {
		  	$messages[] = "Connection to database server is OK.";
		}
			
		// Check that the database is empty
		$db_exists = $this->lib->_db_tables_exist($db_conf);

		// Display the next page of the install script
		$view_data = array(
		  'site_name'=> self::SITE_NAME,
		  'step'     => 1,
		  'messages' => array_merge($messages, $this->lib->get_messages()),
		);
		
		$this->layout->view('install/step1', $view_data);
	}
	
	
	/** 
	 * Step 2 of the install script: Process the admin account data and install the 
	 * database 
	 */
	protected function _step_2() {
		$messages = array();
	
		# Form validation of the admin account data 
		$this->load->library('form_validation');
	
		$this->form_validation->set_rules('user_name', 'Username', 'required|trim'); 
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		$this->form_validation->set_rules('confirm_password', 'Confirm password', 
		                                  'required|trim|matches[password]');
		$this->form_validation->set_error_delimiters('<p class="error">','</p>');
		
		// If the form data is not valid, go back to the admin account form	
		if ($this->form_validation->run() == FALSE) {
			$view_data = array(
				'site_name'=> self::SITE_NAME,
				'step' => 1,
				'messages' => array_merge($messages, $this->lib->get_messages()),
				'form_ok'  => false,
				);
			$this->layout->view('install/step1', $view_data);
		
		  	return;
		}
	
		// Get the POST data with the admin account details
		$admin_user = array();
		$vars = array('email', 'user_name', 'password'); 
		foreach ($vars as $var) {
		  	$admin_user[$var] = $this->input->post($var);
		}
		
		$messages[] = "The administrator account details are OK.";
		
		// Check the database again and install the database
		$db_conf = $this->lib->_load_db_config();
		$db = $this->lib->_test_db_connection($db_conf);
		$this->lib->install_db($db_conf);
		
		// Add the admin user and add the example data in the database
		$user_id = $this->lib->_insert_admin_user($admin_user);
		$num_inserts = $this->lib->_insert_data($user_id);
	
		// If search has been enabled in the config file, create the search index.
		if (config_item('x_search')) {
			$success = $this->lib->_create_search_index();
		}
		
		# Set the 'app_version' in the database
		$this->lib->_set_version();
		
		// Display the next (and final) page of the install script
		$view_data = array(
		  'site_name'=> self::SITE_NAME,
		  'step'     => 2,
		  'messages' => array_merge($messages, $this->lib->get_messages()),
		);
		$this->layout->view('install/step2', $view_data);
	}
}