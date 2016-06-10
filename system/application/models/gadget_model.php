<?php 
/**
 *  Model file for functions related to Google Gadgets on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Gadgets
 */
class Gadget_model extends Model {

    function __construct() {
        parent::Model();
    }

    /**
     * Get the Google gadgets for a cloud 
     *
     * @param integer $cloud_id The ID of the cloud
     * @return array of gadgets 
     */
    function get_gadgets_for_cloud($cloud_id) {
        $this->db->where('cloud_gadget.cloud_id', $cloud_id);
        $this->db->join('cloud_gadget', 'cloud_gadget.gadget_id = gadget.gadget_id');
        $query = $this->db->get('gadget');
        return $query->result();
    }
    
    /**
     * Get the Google gadgets for a user
     *
     * @param integer $user_id The ID of the user
     * @return array of gadgets 
     */
    function get_gadgets_for_user($user_id) {
        $this->db->where('user_gadget.user_id', $user_id);
        $this->db->join('user_gadget', 'user_gadget.gadget_id = gadget.gadget_id');
        $query = $this->db->get('gadget');
        return $query->result();
    }    
    
    /**
     * Get the information about a specific gadget
     *
     * @param integer $gadget_id The ID of the gadget
     * @return object The gadget information 
     */
    function get_gadget($gadget_id) {
        $gadget = null;
        $this->db->join('cloud_gadget', 'cloud_gadget.gadget_id = gadget.gadget_id', 'left outer');
        $this->db->where('gadget.gadget_id', $gadget_id);
        $query = $this->db->get('gadget');
        if ($query->num_rows() !=  0 ) {
            $gadget = $query->row();
        }
        return $gadget;
    }
    
    /**
     * Add a Google gadget to a cloud
     * 
     * @param integer $cloud_id The ID of the cloud to add the gadget to
     * @param string $url The URL of the XML file defining the Google Gadget
     * @param integer $user_id The ID of the user adding the gadget
     * @param string $title The title of the gadget
     */
    function add_gadget_to_cloud($cloud_id, $url, $user_id, $title, $accessible_alternative) {
        $gadget_id = $this->add_gadget($url, $user_id, $title, $accessible_alternative);
        
        $cloud_gadget->cloud_id  = $cloud_id;
        $cloud_gadget->gadget_id  = $gadget_id;
        $this->db->insert('cloud_gadget', $cloud_gadget);
    }

    /**
     * Add a gadget to a user (so the the gadget is associated with all of that user's clouds)
     *
     * @param string $url The URL of the XML file defining the Google Gadget
     * @param integer $user_id The ID of the user adding the gadget
     * @param string $title The title of the gadget
     */
    function add_gadget_to_user($url, $user_id, $title, $accessible_alternative) {
        $gadget_id = $this->add_gadget($url, $user_id, $title, $accessible_alternative);
        
        $user_gadget->user_id  = $user_id;
        $user_gadget->gadget_id  = $gadget_id;
        $this->db->insert('user_gadget', $user_gadget); 
    }
    
    /**
     * Store the details of a gadget
     *
     * @param string $url The URL of the XML file defining the Google Gadget
     * @param integer $user_id The ID of the user adding the gadget
     * @param string $title The title of the gadget
     * @return integer The ID of the gadget
     */
    function add_gadget($url, $user_id, $title, $accessible_alternative) {
        $gadget->url                    = $url;
        $gadget->user_id                = $user_id;
        $gadget->title                  = $title;        
        $gadget->accessible_alternative = $accessible_alternative;
        $gadget->timestamp = time();
        $this->db->insert('gadget', $gadget); 
        $gadget_id = $this->db->insert_id();
        return $gadget_id; 
    }
    
    /**
     * Delete a Google Gadget 
     *
     * @param integer $gadget_id The ID of the gadget to delete
     */
    function delete_gadget($gadget_id) {
        $this->db->where('gadget_id', $gadget_id);
        $this->db->delete('gadget');
        
        $this->db->where('gadget_id', $gadget_id);
        $this->db->delete('cloud_gadget');
           
        $this->db->where('gadget_id', $gadget_id);
        $this->db->delete('user_gadget');        
    }
}
