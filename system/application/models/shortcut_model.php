<?php 
/**
 * Models for functions related to shortcuts
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Shortcut
 */
class Shortcut_model extends Model {   
	
    function Shortcut_model() {
        parent::Model();
    }
    
    /**
     * Get the long URL corresponding to a specified shortcut
     *
     * @param string $shortcut The short cut
     * @return string The URL corresponding to the shortcut
     */
    function get_url($shortcut) {
        $url = FALSE;
        $this->db->where('shortcut', trim($shortcut));
        $query = $this->db->get('shortcut');
        
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $url = $row->URL;
        }
        
        return $url; 
    }
}