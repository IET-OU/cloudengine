<?php

/**
 * Controller for flagging items as spam
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Flag
 */
class Flag extends MY_Controller {

	function Flag () {
		parent::MY_Controller();	
		$this->load->library('layout', 'layout_main'); 
		
		$this->load->model('flag_model');
		$this->load->model('user_model');
		$this->load->model('item_model');
	}

	/** 
	 * Flag item as spam and redirects to original page
	 * @param string $item_type The item type e.g. 'cloud', 'cloudscape'
	 * @param int $item_id The ID of the item
	 */
	function item($item_type, $item_id) {
		$user_id  = $this->db_session->userdata('id');
		
		// Check not already flagged by user to prevent spamming lots of emails
		
		if (!$this->flag_model->is_flagged($item_type, $item_id, $user_id)) {
			$this->flag_model->add($item_type, $item_id, $user_id);
			$this->_send_flagged_email($item_type, $item_id, $user_id);
		}
		redirect($this->item_model->view($item_type, $item_id)); // Return to the original page
	}		
	
	/**
	 * Send email to admins to indicate that item has been flagged as spam
	 * @param string $item_type The item type e.g. 'cloud', 'cloudscape'
	 * @param int $item_id The ID of the item
	 * @param int $user_id The ID of the user who flagged the item as spam
	 */
	protected function _send_flagged_email($item_type, $item_id, $user_id) {

	    $flagged->item_id = $item_id;
		$flagged->item_type = $item_type;
		$flagged->user_id = $user_id;
		$data['flagged'] = $flagged;

		$data['user'] = $this->user_model->get_user($user_id);
										
		$data['url'] = $this->item_model->view($item_type, $item_id);

        $message = $this->load->view('email/flagged_spam', $data, true);
        $this->load->plugin('phpmailer');

		$admins = $this->user_model->get_admins();       
		foreach ($admins as $admin) {
			send_email($admin->email, 
					   config_item('site_email'), 
					   t('!site-name! - Item flagged as spam'), 
					   $message);         
		} 
			
	}

}
