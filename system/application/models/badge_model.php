<?php 
/**
 * Model file for badge-related functions
 * @copyright 2012 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Badge
 */
class Badge_model extends Model {

    function Badge_model() {
        ini_set("display_errors", 'On');
        error_reporting(E_ALL);
        parent::Model();
    }
    
    /**
     * Get all badges on the site that have not been flagged for moderation
     *
     * @return Array of badges
     */
    function get_badges() {
        $query = $this->db->query(" SELECT * 
                                  FROM badge b
                                  INNER JOIN user u on u.id = b.user_id 
                                  AND u.banned = 0");
        return $query->result();
    }
    
    /**
     * Insert a badge
     *
     * @param $badge object The details of the badge
     * @return integer $badge_id The ID of the newly created badge
     */
    function insert_badge($badge) {
        $valid = TRUE;;
        
        // Check that the user_id, title and body have been specified and that the user_id is numeric
        if (!$badge->user_id || !$badge->name || trim($badge->name) == '' 
            || !$badge->description|| trim($badge->description) == '' 
            || !$badge->criteria || trim($badge->criteria) == '' 
            || !is_numeric($badge->user_id)) {
                valid = FALSE;
        }
        
        if ($valid) { 

            $badge->created = time();
            $this->db->insert('badge', $badge);
            $badge_id =  $this->db->insert_id();             
        } else {
            $badge_id = FALSE;
        }
        return $badge_id;
    } 
    
    /**
     * Delete an existing badge
     *
     * @param integer $badge_id The ID of the badge
     */
    function delete_badge($badge_id) {
        $this->db->delete('badge', array('badge_id' => $badge_id));   
    } 
    
    /**
     * Get the details of a badge for a given badge id 
     *
     * @param integer $badge_id The ID of the badge
     * @return object The details of the badge
     */
    function get_badge($badge_id) {

        $badge = false;
        $this->db->from('badge');
        // Call select() to ensure we don't include the user.created field.
        $this->db->select('badge.*, user_picture.*, user_profile.id, user_profile.fullname');
        $this->db->where('badge.badge_id', $badge_id);
        $this->db->where('user.banned',0);     
        $this->db->join('user_picture', 'badge.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = badge.user_id');
        $this->db->join('user', 'user_profile.id = user.id');        
        $query = $this->db->get();

        if ($query->num_rows() !=  0 ) {
            $badge = $query->row();
        }

        $badge->owner = false;
        
        if ($this->auth_lib->is_logged_in()) {
            $user_id = $this->db_session->userdata('id');
            $badge->owner = $this->has_edit_permission($user_id, $badge_id);

        } 
        
        return $badge;
    }

    /**
     * Determine if a user has edit permission for a specified badge - 
     * they have edit permission if they are either the owner of the badge or a site
     * admin
     *
     * @param integer $user_id The ID of the user
     * @param integer $badge_id The ID of the badge
     * @return boolean TRUE if they have edit permission, FALSE otherwise
     */

    function has_edit_permission($user_id, $badge_id) {
        $permission = FALSE;
        if ($user_id) {
            $owner_user_id = $this->get_owner($badge_id);
            if ($user_id == $owner_user_id || $this->auth_lib->is_admin()) {
                $permission = TRUE;
            }
        }
        
        return $permission;
    }
    
    /**
     * Check if a user has edit permission for a specified badge (see has_edit_permission 
     * function). If not display an error page.
     *
     * @param integer $user_id The ID of the user
     * @param integer $badge_id The ID of the badge
     */
    function check_edit_permission($user_id, $badge_id) {
        if (!$this->has_edit_permission($user_id, $badge_id)) {
            show_error(t("You do not have edit permission for that badge."));   
        } 
    }    
    
    /**
     * Get the filename of a badge's picture
     *
    * @param integer $badge_id The id of the badge
     * @return string The filename of the picture
     */
    function get_picture($badge_id) {
        $this->db->where('badge_id', $badge_id);
        $this->db->select('image');
        $query = $this->db->get('badge');
        $result = $query->result();

        return isset($result[0]->image) ? $result[0]->image : null;
    } 
    
    /**
     * Get the owner/creator of a badge
     *
     * @param integer $badge_id The ID of the badge
     * @return integer The ID of the owner
     */
    function get_owner($badge_id) {
        $this->db->where('badge_id', $badge_id);
        $query = $this->db->get('badge');
        if ($query->num_rows() > 0) {
            $badge = $query->row();
            $owner_user_id = $badge->user_id;
        }

        return $owner_user_id;
    }    
    
    function insert_application($badge_id, $user_id, $cloud_id) {
        $valid = TRUE;
        
        // Check that the user_id, title and body have been specified and that the user_id is numeric
        if (!is_numeric($badge_id) || !is_numeric($user_id) || 
            !is_numeric($cloud_id) {
            valid = FALSE;
        }
        
        if ($valid) { 
            $application->created = time();
            $application->badge_id = $badge_id;
            $application->user_id = $user_id;
            $application->cloud_id = $cloud_id;
            $this->db->insert('badge_application', $application);
            $application_id =  $this->db->insert_id();             
        } else {
            $application_id = FALSE;
        }
        return $application_id; 
    }
}