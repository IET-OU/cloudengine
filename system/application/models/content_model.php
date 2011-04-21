<?php 
/**
 * Model file for functions related to extra content items on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Content
 */
class Content_model extends Model {
    
    function Content_model() {
        parent::Model();
    }
    
    /**
     * Add a new extra content item to a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param string $body
     * @param integer $user_id The ID of the user
     * @param boolean $moderate TRUE if the item needs to be moderated, FALSE otherwise
     * @param integer $timestamp The time the item was added as a Unix time stampe
     * @param boolean $modified Whether the item has been editted (this should always
     * be false for a new comment, so not sure what it is doing here!)
     * @return integer The ID of the new content item
     */
    function insert_content($cloud_id, $body, $user_id, $moderate, $created = null, 
                            $modified = null) {
        $content->cloud_id = $cloud_id;
        $content->body     = $body;
        $content->user_id  = $user_id;  
        $content->created  = time();
        if ($created) {
            $content->created = $created;
        }
        
        $content->modified = null;
        if ($modified) {
            $content->modified = $modified;
        }
        $content->moderate  = $moderate;
        $this->db->insert('cloud_content', $content); 
        $content_id = $this->db->insert_id();
        
        if (!$moderate) {
            $this->approve_content($content_id);
        }
        
        $this->CI=& get_instance();
        $this->CI->load->model('cloud_model');
        $this->CI->cloud_model->update_in_search_index($cloud_id);
        
        return $content_id;     
    }
    
    /**
     * Get all the extra content items on a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return array Array of extra content items
     */
    function get_content($cloud_id) {
        $this->db->select('cloud_content.*, user_picture.*, user_profile.*');
        $this->db->from('cloud_content');      
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('moderate', 0);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'cloud_content.user_id = user.id');        
        $this->db->join('user_picture', 'cloud_content.user_id = user_picture.user_id', 
                        'left');        
        $this->db->join('user_profile', 'user_profile.id = cloud_content.user_id');        
        $this->db->order_by('cloud_content.created', 'asc');
        $query = $this->db->get();
        return $query->result();   
    }
    
    /**
     * Get a specified extra content item
     *
     * @param integer $content_id The ID of the extra content item
     * @return object The details of the extra content item
     */
    function get_content_item($content_id) {
        $this->db->join('user_profile', 'user_profile.id = cloud_content.user_id');            
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'user.id = cloud_content.user_id');            
        $this->db->where('content_id', $content_id);
        $query = $this->db->get('cloud_content');
        
        if ($query->num_rows() !=  0 ) {
            $content = $query->row();
        }
        
        return $content; 
    }
    
    /**
     * Approve an extra comment item so that it is not moderated 
     *
     * @param integer $content_id The ID of the extra content item
     */
    function approve_content($content_id) {
        if ($content_id) {
            $content = $this->get_content_item($content_id);
            $this->db->where('content_id', $content_id);
            $this->db->update('cloud_content', array('moderate'=>0)); 
            $this->load->model('event_model');
            $event_model = new event_model();
            $event_model->add_event('cloud', $content->cloud_id, 'content', $content_id, 
                                    $content->created);
            $event_model->add_event('user', $content->user_id, 'content', $content_id, 
                                    $content->created);
    
        }     
    }
    
    /**
     * Delete an extra content item
     *
     * @param integer $content_id The ID of the extra content item
     */
    function delete_content($content_id) {
        $this->db->delete('cloud_content', array('content_id' => $content_id));
        $this->load->model('event_model');
        $event_model = new event_model();  
        $event_model->delete_events('content', $content_id);      
    }
    
    /**
     * Update an existing comment item
     *
     * @param object $comment The new comment details
     */
    function update_content($content) {
        $content_id       = $content->content_id;
        $content->modified = time();
        $this->db->update('cloud_content', $content, array('content_id'=>$content_id));      
    }

   /**
    * Get all extra content items under moderation
    *
    * @return array Array of extra content items
    */
   function get_content_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = cloud_content.user_id');        
        $query = $this->db->get('cloud_content');
        return $query->result();   
    } 
    
    /**
     * Determine if a specified user has permission to edit a specified content
     *
     * @param integer $user_id The ID of the user
     * @param integer $content_id The ID of the extra content item
     * @return boolean TRUE if they have permission, FALSE otherwise
     */
    function has_edit_permission($user_id, $content_id) {
        $permission = false;
        if ($user_id) {
            $content = $this->get_content_item($content_id);
            if ($user_id == $content->id || $this->auth_lib->is_admin()) {
                $permission = true;
            }
        }
        return $permission;
    }

    /**
     * Display an error if specified user does not have permission to edit the 
     * specified content
     *
     * @param integer $user_id The ID of the user
     * @param integer $content_id The ID of the extra content item
     */
    function check_edit_permission($user_id, $content_id) {
        if (!$this->has_edit_permission($user_id, $content_id)) {
            show_error('You do not have edit permission for that content.');   
        } 
    }
}