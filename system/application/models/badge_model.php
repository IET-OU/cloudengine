<?php 
/**
 * Model file for badge-related functions
 * @copyright 2012 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Badge
 */
class Badge_model extends Model {
    function Badge_model() {

        error_reporting(E_ALL);
        parent::Model();
    }
    
    /**
     * Get all badges on the site that have not been flagged for moderation
     *
     * @return Array of badges
     */
    function get_badges() {
        $query = $this->db->query("SELECT * 
                                   FROM badge b
                                   INNER JOIN user u on u.id = b.user_id 
                                   AND u.banned = 0 ORDER BY name ASC");
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
        
        // Check that the user_id, name and body have been specified and that 
        // the user_id is numeric
        if (!$badge->user_id || !$badge->name || trim($badge->name) == '' 
            || !$badge->description|| trim($badge->description) == '' 
            || !$badge->criteria || trim($badge->criteria) == '' 
            || !is_numeric($badge->user_id)) {
                $valid = FALSE;
        }
        
        if ($valid) { 

            $badge->created = time();
            $this->db->insert('badge', $badge);
            $badge_id =  $this->db->insert_id();      
            if ($badge->type == 'verifier') {
                $this->add_verifier($badge_id, $badge->user_id);
            }
        } else {
            $badge_id = FALSE;
        }
        return $badge_id;
    } 
    
    /**
     * Update the information for a badge
     * @badge object The new badge information
     */
    function update_badge($badge) {
        $badge_id       = $badge->badge_id;
        $badge->modified = time();
        $this->db->update('badge', $badge, array('badge_id'=>$badge_id)); 
    }    
    
    /**
     * Delete an existing badge
     *
     * @param integer $badge_id The ID of the badge
     */
    function delete_badge($badge_id) {
        $badge_id = (int)$badge_id;
        
        $this->db->delete('badge', array('badge_id' => $badge_id));   
        
        $this->db->query("DELETE bd.* FROM badge_decision AS bd 
                          LEFT JOIN badge_application AS ba
                          ON ba.application_id = bd.application_id
                          WHERE ba.badge_id = $badge_id");                          
        
        $this->db->delete('badge_application', array('badge_id' => $badge_id));  
        $this->db->delete('badge_verifier', array('badge_id' => $badge_id));    
    } 
    
    /**
     * Get the details of a badge for a given badge id 
     *
     * @param integer $badge_id The ID of the badge
     * @return object The details of the badge
     */
    function get_badge($badge_id) {
        $badge = FALSE;
        $this->db->from('badge');
        // Call select() to ensure we don't include the user.created field.
        $this->db->select('badge.*, user_picture.*, user_profile.id, user_profile.fullname');
        $this->db->where('badge.badge_id', $badge_id);
        $this->db->where('user.banned',0);     
        $this->db->join('user_picture', 'badge.user_id = user_picture.user_id', 
                        'left');
        $this->db->join('user_profile', 'user_profile.id = badge.user_id');
        $this->db->join('user', 'user_profile.id = user.id');        
        $query = $this->db->get();

        if ($query->num_rows() !=  0 ) {
            $badge = $query->row();
        }

        $badge->owner = FALSE;
        
        if ($this->auth_lib->is_logged_in()) {
            $user_id = $this->db_session->userdata('id');
            $badge->owner = $this->has_edit_permission($user_id, $badge_id);

        } 
        
        $query = $this->db->query("SELECT bv.user_id AS user_id,
                                  u.fullname AS fullname
                                  FROM badge_verifier bv
                                  INNER JOIN user_profile u 
                                  ON bv.user_id = u.id
                                  WHERE bv.badge_id = $badge_id");
        $badge->verifiers = $query->result();                          
        
        return $badge;
    }
    
    /**
     * Get the filename of a badge's picture
     *
     * @param integer $badge_id The id of the badge
     * @return string The filename of the picture
     */
    function get_image($badge_id) {
        $this->db->where('badge_id', $badge_id);
        $this->db->select('image');
        $query  = $this->db->get('badge');
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
            $badge         = $query->row();
            $owner_user_id = $badge->user_id;
        }

        return $owner_user_id;
    }      

    /**
     * Determine if a user has edit permission for a specified badge - 
     * they have edit permission if they are either the owner of the badge or a 
     * site admin
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
     * Check if a user has edit permission for a specified badge (see 
     * has_edit_permission function). If not display an error page.
     *
     * @param integer $user_id The ID of the user
     * @param integer $badge_id The ID of the badge
     */
    function check_edit_permission($user_id, $badge_id) {
        if (!$this->has_edit_permission($user_id, $badge_id)) {
            show_error(t("You do not have permissiont to edit this badge."));   
        } 
    }   

    /**
     * Get the verifiers for a badge
     * @param integer $badge_id The ID of the badge
     */
    function get_verifiers($badge_id) {
        $this->db->where('badge_id', $badge_id);
        $this->db->join('user_profile', 
                        'user_profile.id = badge_verifier.user_id');
        $query = $this->db->get('badge_verifier');
        return $query->result();
    }

    /**
     * Determine if a specified user is a verifier for a specified badge
     * @param integer $badge_id The ID of the badge
     * @param integer $user_id The ID of the user
     */
    function is_verifier($badge_id, $user_id) {
        $verifier = false;
        if ($user_id) {
            $this->db->where('badge_id', $badge_id);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('badge_verifier');
            if ($query->num_rows() > 0) {
                $verifier = TRUE;
            }
        }
        return $verifier;
    }
    
    /**
     * Add a verifier for a badge
     * @param integer $badge_id The ID of the badge
     * @param integer $user_id The ID of the user
     */     
    function add_verifier($badge_id, $user_id) {
        if ($user_id) {
            if (!$this->is_verifier($badge_id, $user_id)) {
                $this->db->set('badge_id', $badge_id);
                $this->db->set('user_id', $user_id);
                $this->db->insert('badge_verifier');
            }
        }    
    }
    
    /**
     * Remove a verifier from a badge
     * @param integer $badge_id The ID of the badge
     * @param integer $user_id The ID of the user
     */     
    function remove_verifier($badge_id, $user_id){
        $this->db->where('badge_id', $badge_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('badge_verifier'); 
    }  
    
    /** 
     * Check if a user is a verifier for a badge
     * @param integer $user_id The ID of the user
     * @param integer $badge_id The ID of the badge
     */     
    function check_verifier($user_id, $badge_id) {
        if (!$this->is_verifier($badge_id, $user_id)) {
            show_error(t("You do not have verifier permission for that badge."));   
        }     
    }
    
    /**
     * Get the badges for which a specified use is a verifier and the 
     * number of applications for each 
     * @param integer $user_id The ID of the user
     */     
    function get_badges_with_verification_permission($user_id) {        
        $user_id = (int) $user_id;
        $query = $this->db->query("SELECT b.name AS name, 
                                   b.badge_id AS badge_id,
                                   COUNT(ba.application_id) AS total_applications
                                   FROM badge b
                                   LEFT OUTER JOIN badge_application ba 
                                   ON ba.badge_id = b.badge_id
                                   INNER JOIN badge_verifier bv
                                   ON bv.badge_id = b.badge_id
                                   WHERE bv.user_id = $user_id
                                   AND b.type = 'verifier'
                                   AND ba.status = 'pending'
                                   AND ba.user_id <> $user_id
                                   GROUP BY badge_id");
        return $query->result();    
    }
    
    /** 
     * Get all the crowdsourced badges and number of applications for each
     */
    function get_crowdsourced_badges($user_id) {
        $query = $this->db->query("SELECT b.name AS name, 
                                   b.badge_id AS badge_id,
                                   COUNT(ba.application_id) AS total_applications
                                   FROM badge b
                                   LEFT OUTER JOIN badge_application ba 
                                   ON ba.badge_id = b.badge_id
                                   WHERE b.type = 'crowdsource'
                                   AND ba.status = 'pending'
                                   AND ba.user_id <> $user_id
                                   AND NOT EXISTS
                                   (SELECT * FROM badge_decision bd 
                                   WHERE bd.application_id = ba.application_id
                                   AND bd.user_id = $user_id)
                                   GROUP BY badge_id");   
         return $query->result();                           
    }   

    /**
     * Get all the applications for a badge
     * This needs to not include any applications by the current user or which
     * have already had decisions made by the current user
     * @param integer $badge_id The ID of the badge
     */     
    function get_applications($badge_id, $current_user_id) {
        $current_user_id = (int) $current_user_id;
        $query = $this->db->query("SELECT * 
                                   FROM badge_application ba
                                   INNER JOIN user_profile up
                                   ON up.id = ba.user_id 
                                   WHERE ba.badge_id = $badge_id
                                   AND ba.status = 'pending'
                                   AND ba.user_id <> $current_user_id
                                   AND NOT EXISTS
                                   (SELECT * FROM badge_decision bd 
                                   WHERE bd.application_id = ba.application_id
                                   AND bd.user_id = $current_user_id)
                                   ");
        return $query->result();    
    }
    
    /**
     * Get all the applications submitted by a user with a given status
     * @param string $status The new application status: one of 
     * 'pending', 'approved', 'rejected'
     */
    function get_applications_for_user($user_id, $status) {
        // Check status is one of 'pending', 'approved', 'rejected'
        $user_id = (int) $user_id;
        $this->db->where('badge_application.user_id', $user_id);
        $this->db->where('status', $status);
        $this->db->join('badge', 
                        'badge.badge_id = badge_application.badge_id');
        $query = $this->db->get('badge_application');
        return $query->result();
    }   

    /** 
     * Get all badge applications (for all users) with a given status
     * @param string $status The new application status: one of 
     * 'pending', 'approved', 'rejected'
     */     
    function get_all_applications($status) {
        $this->db->where('status', $status);
        $this->db->select('*, badge_application.created AS created');
        $this->db->join('user_profile' , 
                        'badge_application.user_id = user_profile.id');        
        $this->db->join('badge', 
                        'badge.badge_id = badge_application.badge_id');
        $query = $this->db->get('badge_application');
        return $query->result();    
    }
    
    /**
     * Get the details of a badge application
     * @param integer $application_id The ID of the application
     */
    function get_application($application_id) {
        $application_id = (int)$application_id;
        $query = $this->db->query("SELECT 
                                   ba.application_id AS application_id,
                                   ba.evidence_URL AS evidence_URL,
                                   ba.badge_id AS badge_id,
                                   ba.status AS status,
                                   b.name AS name,
                                   ba.issued AS issued,
                                   b.description AS description,
                                   b.criteria AS criteria,
                                   ba.user_id AS user_id, 
                                   u.email AS email,
                                   up.fullname AS fullname                                   
                                   FROM badge_application ba
                                   INNER JOIN badge b ON b.badge_id = ba.badge_id
                                   INNER JOIN user u ON ba.user_id = u.id
                                   INNER JOIN user_profile up ON ba.user_id = up.id
                                   WHERE application_id = $application_id");
        return $query->row();
    }    
    
    /**
     * Insert a badge application
     * @param integer $badge_id The ID of the badge
     * @param integer $user_id The ID of the user who submitted the application
     * @param string $evidence_url The URL supplied by the user as evidence
     * that they meet the badge criteria in their application
     */     
    function insert_application($badge_id, $user_id, $evidence_url) {
        $valid = TRUE;
        
        if (!is_numeric($badge_id) || !is_numeric($user_id)) {
            $valid = FALSE;
        }
        
        if ($valid && $this->can_apply($user_id, $badge_id)) { 
            $application->created = time();
            $application->badge_id = $badge_id;
            $application->user_id = $user_id;
            $application->evidence_url = $evidence_url;
            $this->db->insert('badge_application', $application);
            $application_id =  $this->db->insert_id();             
        } else {
            $application_id = FALSE;
        }
        return $application_id; 
    }
    
    /**
     * Delete a badge application
     * @param integer $application_id The ID of the application to delete
     */
    function delete_application($application_id) {
        $this->db->delete('badge_application', 
                          array('application_id' => $application_id)); 
        $this->db->delete('badge_decision', 
                         array('application_id' => $application_id)); 
    } 

    /**
     * Update the status of an application
     * @param integer $application_id The ID of the application to delete
     * @param string $status The new application status: one of 
     * 'pending', 'approved', 'rejected'
     */     
    function update_application_status($application_id, $status) {
            $this->db->where('application_id', $application_id);
            $this->db->update('badge_application', array('status'=>$status));    
    }
    
    /**
     * Get the owner of an application i.e. the person who submitted the 
     * application
     * @param integer $application_id The ID of the application 
     */     
    function get_application_owner($application_id) {
        $this->db->where('application_id', $application_id);
        $query = $this->db->get('badge_application');
        if ($query->num_rows() > 0) {
            $application = $query->row();
            $owner_user_id = $application->user_id;
        }

        return $owner_user_id;
    }       
    
    /**
     * Determine if a user has permission to edit a badge application i.e. 
     * whether they submitted the application or are an admin
     * @param integer $user_id The ID of the user      
     * @param integer $application_id The ID of the application 
     */     
    function has_application_edit_permission($user_id, $application_id) {
        $permission = FALSE;
        if ($user_id) {
            $owner_user_id = $this->get_application_owner($badge_id);
            if ($user_id == $owner_user_id || $this->auth_lib->is_admin()) {
                $permission = TRUE;
            }
        }
        
        return $permission;
    }    
    
    /**
     * Check if a user has edit permission for an application
     * @param integer $user_id The ID of the user      
     * @param integer $application_id The ID of the application      
     */
    function check_application_edit_permission($user_id, $application__id) {
        if (!$this->has_application_edit_permission($user_id, 
                                                    $application__id)) {
            show_error(t("You do not have permission to view this application."));   
        } 
    }      
    
    /**
     * Determine if a user can apply for a badge (i.e. that they don't already
     * have a pending or approved application for that badge)
     * @param integer $user_id The ID of the user  
     * @param integer $badge_id The ID of the badge
     */     
    function can_apply($user_id, $badge_id) {
        $can_apply = TRUE;
        $badge_id = (int) $badge_id;
        $user_id = (int) $user_id;
        $query = $this->db->query("SELECT * FROM badge_application
                                   WHERE user_id = $user_id
                                   AND badge_id = $badge_id
                                   AND (status = 'pending' 
                                   OR status = 'approved')"); 
        if ($query->num_rows() > 0) {
            $can_apply = FALSE;
        }  

        if ($user_id == 0) {
            $can_apply = FALSE;
        }
        
        return $can_apply;
    }

    /** 
     * Get all the decisions for a specified badge application
     * @param integer $application_id The ID of the application 
     */     
    function get_decisions($application_id) {
        $this->db->where('application_id', $application_id);
        $this->db->join('user_profile' , 
                        'badge_decision.user_id = user_profile.id');
        $query = $this->db->get('badge_decision');
        return $query->result();
    }
    
    /**  
     * Add a decision about a badge application
     * @param integer $application_id The ID of the application 
     * @param integer $user_id The ID of the user
     * @param string $decision_made The decision, one of 'approved' and 
     * 'rejected'     
     * @param string $feedback Any feedback given with the decision 
     */
    function add_decision($application_id, $user_id, $decision_made, 
                          $feedback) {
        $badge_awarded = FALSE;
        $decision->application_id = $application_id;
        $decision->user_id = $user_id;
        $decision->decision = $decision_made;
        $decision->feedback = $feedback;
        $decision->timestamp = time();
        
        $this->db->insert('badge_decision', $decision);
 
        // Update badge status 
        $this->db->where('application_id', $application_id);
        $this->db->join('badge_application', 
                        'badge_application.badge_id = badge.badge_id');
        $query = $this->db->get('badge');
        $badge = $query->row();

        if ($badge->type == 'verifier') {
            $this->update_application_status($application_id, $decision_made);
            $this->db->where('application_id', $application_id);
            $this->db->update('badge_application', array('issued'=>time()));  
            if ($decision_made == 'approved') {
                $badge_awarded = TRUE;
            }
        }
        
        if ($badge->type == 'crowdsource') {
            $num_approves = $this->get_num_approves($application_id);
            if ($num_approves >= $badge->num_approves) {
                $this->update_application_status($application_id, 'approved');
                $this->db->where('application_id', $application_id);
                $this->db->update('badge_application', 
                                  array('issued'=>time()));
                $badge_awarded = TRUE;
            }
        }

        return $badge_awarded;
    }
    
    /** 
     * Determine if a user has made a decision on an application
     * @param integer $application_id The ID of the application 
     * @param integer $user_id The ID of the user
     */
    function has_made_decision($application_id, $user_id) {
        $has_made_decision = FALSE;
        $this->db->where('user_id', $user_id);
        $this->db->where('application_id', $application_id);
        $query = $this->db->get('badge_decision');
        if ($query->num_rows() > 0) {
            $has_made_decision  = TRUE;
        }
        
        return $has_made_decision ;
    }
	    
    /** 
     * Get the number of 'approved' decisions for a badge application
     * @param integer $application_id The ID of the application 
     */     
    function get_num_approves($application_id) {
        $this->db->where('application_id', $application_id);
        $this->db->where('decision', 'approved');
        $query = $this->db->get('badge_decision');
        return $query->num_rows();
    }
   
    /** 
     * Get all the badges that a specified user has been awarded
     * @param integer $badge_id The ID of a badge
     */
    function get_badges_for_user($user_id) {
        $user_id = (int) $user_id;
        $query = $this->db->query("SELECT * FROM 
                                  badge b INNER JOIN badge_application ba
                                  ON b.badge_id = ba.badge_id
                                  WHERE ba.user_id = $user_id
                                  AND ba.status = 'approved'");
        return $query->result();
    } 
	
	/**
	 * Get the list of users awarded a specific badge
	 * @param integer $badge_id The ID of the badge
	 * @return array The list of users 
	 */
	function get_users_awarded_badge($badge_id) {
		$this->db->where('badge_id', $badge_id);
		$this->db->where('status', 'approved');    
        $this->db->join('user_picture', 'badge_application.user_id = user_picture.user_id', 
                        'left');
        $this->db->join('user_profile', 'user_profile.id = badge_application.user_id');  		
		$query = $this->db->get('badge_application');
		return $query->result();
	}
}