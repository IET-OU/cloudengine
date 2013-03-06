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
	
	function add($item_type, $item_id, $user_id) {
		if (!$this->is_flagged($item_type, $item_id, $user_id)) {
			$flagged->item_id = $item_id;
			$flagged->item_type = $item_type;
			$flagged->user_id = $user_id;
			$flagged->timestamp = time();
			$this->db->insert('flagged_spam', $flagged);
		}
	}
	
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
}