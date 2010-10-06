<?php
/**
 * 
 * Model filefor API client related functions
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package API
 */
class Api_client_model extends Model {

    /** 
     * Check if an API-key is valid, and return the user.
     * @param string $api_key A key.
     * @return mixed An api_client object or FALSE if no such object
     */
    public function is_valid_key($api_key) {
        $api_client = false;
        $this->db->from('api_client');
        $this->db->where('api_client.api_key', $api_key);
        $this->db->join('user', 'api_client.user_id = user.id', 'left');
        $query = $this->db->get();
        
        if ($query->num_rows() !=  0 ) {
            $api_client = $query->row();
        }
        
        return $api_client;
    }

    /**
     * Log information about an API call
     *
     * @param integer $client_id The ID 
     * @param string $level Either 'error', 'debug' or 'info'
     * @param string $message The message to log
     * @param string $request The URL used for the request
     * @param string $ua The user agent string
     * @param string $ref The referrer
     * @param string $ip The IP address 
     */
    public function log($client_id, $level, $message, $request = NULL, $ua = NULL, $ref = NULL, 
                        $ip = NULL) {
        $this->load->library('user_agent');
        $levels = array('error' => 1, 'debug' => 2, 'info' => 3);
        $level = $levels[$level];
        
        $this->db->set('client_id', $client_id);
        $this->db->set('timestamp', time());
        $this->db->set('level', $level);
        $this->db->set('message', $message); #The message includes the error code.
        $this->db->set('request', $this->uri->uri_string().'?'.$_SERVER['QUERY_STRING']);
        $this->db->set('user_agent', $this->agent->agent_string());
        $this->db->set('referrer', $this->agent->referrer());
        $this->db->set('ip', $this->input->ip_address()); 
        $this->db->insert('api_log');
    }

}
