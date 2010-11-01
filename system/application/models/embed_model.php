<?php 
/**
 *  Model file for functions related to embedson clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Embed
 */
class Embed_model extends Model {

    function Embed_model() {
        parent::Model();
    }
    
    /**
     * Add an embed to a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param string $url The URL of the embed
     * @param string $title The title of the embed
     * @param integer $user_id The ID of the user adding the embed
     * @param string $accessible_alternative Text containing an accessible alternative to the
     * embed
     * @param boolean $moderate TRUE if the embed needs moderation, FALSE otherwise
     * @return integer The ID of the new embed
     */
    function add_embed($cloud_id, $url, $title, $user_id, $accessible_alternative, 
                       $moderate) {
        $embed->cloud_id               = $cloud_id;
        $embed->url                    = $url;
        $embed->title                  = $title;
        $embed->user_id                = $user_id;
        $embed->accessible_alternative = $accessible_alternative;
        $embed->timestamp              = time();
        $embed->moderate               = $moderate;
        $this->db->insert('cloud_embed', $embed); 
        $embed_id = $this->db->insert_id();
        
        if (!$moderate) {
            $this->approve_embed($embed_id);
        }
        
        return $embed_id;   
    }
    
    /**
     * Update the information about an embed
     *
     * @param integer $embed_id The ID of the embed
     * @param string $url The URL of the embed
     * @param string $title The title of the embed
     * @param string $accessible_alternative Text containing an accessible alternative to the
     * embed
     */
    function update_embed($embed_id, $url, $title, $accessible_alternative) {
        $embed->url = $url;
        $embed->title = $title;
        $embed->accessible_alternative = $accessible_alternative;
        $this->db->update('cloud_embed', $embed, array('embed_id'=>$embed_id)); 
    }
    
    /**
     * Determine if a specified user has permission to edit a specified embed
     *
     * @param integer $user_id The ID of the user
     * @param integer $embed_id The ID of the embed
     * @return boolean TRUE if the user has permission, FALSE otherwise
     */
    function has_edit_permission($user_id, $embed_id) {
        $permission = false;
        if ($user_id) {
            $embed = $this->get_embed($embed_id);
            if ($user_id == $embed->user_id || $this->auth_lib->is_admin()) {
                $permission = true;
            }
        }
        return $permission;
    }

    /**
     * Display an error if specified user does not have permission to edit the 
     * specified embed
     *
     * @param integer $user_id The ID of the user
     * @param integer $embed_id The ID of the embed
     */
    function check_edit_permission($user_id, $embed_id) {
        if (!$this->has_edit_permission($user_id, $embed_id)) {
            show_error(t("You do not have edit permission for that embed."));   
        } 
    }
    
    /**
     * Delete an embed
     *
     * @param integer $embed_id The ID of the embed
     */
    function delete_embed($embed_id) {
        $this->db->delete('cloud_embed', array('embed_id' => $embed_id));
        $this->load->model('event_model');
        $event_model = new event_model();  
        $event_model->delete_events('embed', $embed_id);     
    }    
    
    /**
     * Approve an embed
     *
     * @param integer $embed_id The ID of the embed
     */
    function approve_embed($embed_id) {
        $this->db->where('embed_id', $embed_id);
        $this->db->update('cloud_embed', array('moderate'=>0)); 
        $embed = $this->get_embed($embed_id);
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->add_event('cloud', $embed->cloud_id, 'embed', $embed_id);  
        $event_model->add_event('user', $embed->user_id, 'embed', $embed_id);            
    }

   /**
     * Get the embeds for a specific cloud
     *
     * @param integer $cloud_id
     * @return array Array of embeds
     */
    function get_embeds($cloud_id) {
        $this->db->where('cloud_id', $cloud_id);
        $this->db->where('moderate', 0);
        $this->db->join('user_profile', 'user_profile.id = cloud_embed.user_id');        
        $this->db->order_by('timestamp', 'asc');
        $query = $this->db->get('cloud_embed');
        return $query->result(); 
    }  

    /**
     * Get an embed
     *
     * @param integer $embed_id The ID of the embed
     * @return object The details of the embed
     */
    function get_embed($embed_id) {
        $this->db->where('embed_id', $embed_id);
        $this->db->join('user_profile', 'user_profile.id = cloud_embed.user_id');                      
        $query = $this->db->get('cloud_embed');
        
        if ($query->num_rows() !=  0 ) {
            $embed = $query->row();
        }
        return $embed;
    }
      
    /**
     * Get all embeds requiring moderation
     *
     * @return array Array of embeds
     */
    function get_embeds_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = cloud_embed.user_id');        
        $this->db->order_by('timestamp', 'asc');
        $query = $this->db->get('cloud_embed');
        return $query->result();   
    } 
         
}