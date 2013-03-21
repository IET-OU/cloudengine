<?php 
/**
 *  Model file for functions related to flagging items as spam
 * 
 * @copyright 2013 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Flag
 */
class Flag_model extends Model {   
    
	function Flag_model() {
        parent::Model();
    }
	
	/** 
	 * Record that an item has been flagged as spam 
	 * @param string $item_type The item type e.g. 'cloud', 'cloudscape'
	 * @param int $item_id The ID of the item
	 * @param int $user_id The ID of the user who flagged the item as spam
	 */
	function add($item_type, $item_id, $user_id) {
		if (!$this->is_flagged($item_type, $item_id, $user_id)) {
			$flagged->item_id = $item_id;
			$flagged->item_type = $item_type;
			$flagged->user_id = $user_id;
			$flagged->timestamp = time();
			$this->db->insert('flagged_spam', $flagged);
		}
	}
	
	/** 
	 * Determines if a user has flagged an item as spam
	 * @param string $item_type The item type e.g. 'cloud', 'cloudscape'
	 * @param int $item_id The ID of the item
	 * @param int $user_id The ID of the user who flagged the item as spam
	 * @param boolean TRUE if the user has flagged the item as spam, FALSE otherwise
	 */
	function is_flagged($item_type, $item_id, $user_id) {
        $flagged = FALSE;
        $this->db->where('item_type', $item_type);
		$this->db->where('item_id', $item_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('flagged_spam');
        if ($query->num_rows() > 0) {
            $flagged = TRUE;
        }

        return $flagged;	
	}
	
	/**
	 * Get all items that have been flagged as spammed together with the details of the user who flagged the item as spam
	 * @return array of incidences of items being flagged as spam
	 */
	function get_flagged() {
        
	    $this->db->join('user_profile', 'user_profile.id = flagged_spam.user_id');
	    $query = $this->db->get('flagged_spam');
	    // Join with user table
	    // Order by data flagged
	    // Remove deleted items?
	    return $query->result();
	}
}

