<?php 
/**
 *  Model file for functions related to links on clouds
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Links
 */
class Link_model extends Model {

    function Link_model() {
        parent::Model();
    }
    
    /**
     * Add a new link to a cloud 
     *
     * @param integer $cloud_id THE ID of the cloud
     * @param string $url The URL of the link
     * @param string $title The link title
     * @param integer $user_id The ID of the user adding the link
     * @param boolean $moderate TRUE if the link needs moderation, FALSE otherwise
     * @return integer The ID of the new link
     */
    function add_link($cloud_id, $url, $title, $user_id, $moderate) {
        $link->cloud_id = $cloud_id;
        $link->url = $url;
        $link->title = $title;
        $link->user_id = $user_id;
        $link->timestamp = time();
        $link->moderate = $moderate;
        $link->type = $this->get_link_type($url);
        $this->db->insert('cloud_link', $link); 
        $link_id = $this->db->insert_id();
        
        if (!$moderate) {
            $this->approve_link($link_id);
        }
        
        $this->CI=& get_instance();
        $this->CI->load->model('cloud_model');
        $this->CI->cloud_model->update_in_search_index($cloud_id);
        return $link_id;   
    }
    
    /**
     * Determine if a URL has already been added as a link for a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $url The link URL
     * @return boolean TRUE if duplicate, FALSE otherwise
     */
    function is_duplicate($cloud_id, $url) {
        $duplicate = FALSE;
        
        $this->db->where('url', $url);
        $this->db->where('cloud_id', $cloud_id);
        $query = $this->db->get('cloud_link');
        
        if ($query->num_rows() > 0) {
            $duplicate = TRUE;
        }
        return $duplicate;
    }
    
    /**
     * Update a link
     *
     * @param integer $link_id The ID of the link
     * @param string $url The URL of the link
     * @param string $title The link title
     */
    function update_link($link_id, $url, $title) {
        $link->url = $url;
        $link->title = $title;
        $link->type = $this->get_link_type($url);
        $this->db->update('cloud_link', $link, array('link_id'=>$link_id)); 
    }
    
    /**
     * Determine if a specified user has permission to edit a specified link
     *
     * @param integer $user_id The ID of the user
     * @param integer $link_id The ID of the link
     * @return boolean TRUE if they have permission, FALSE otherwise
     */
    function has_edit_permission($user_id, $link_id) {
        $permission = FALSE;
        if ($user_id) {
           
            $link = $this->get_link($link_id);
            if ($user_id == $link->user_id || $this->auth_lib->is_admin()) {
                $permission = TRUE;
            }
        }
        return $permission;
    }

    /**
     * Display an error page if specified user does not have permission to edit the 
     * specified link
     *
     * @param integer $user_id The ID of the user
     * @param integer $link_id The ID of the link
     */
    function check_edit_permission($user_id, $link_id) {
        if (!$this->has_edit_permission($user_id, $link_id)) {
            show_error(t("You do not have edit permission for that link."));
        } 
    }
    
    /**
     * Determine the type of link (e.g. if it is a cloud, cloudscape or 'external' (i.e. 
     * neither cloud nor cloudscape) from the URL
     *
     * @param string $url The link URL
     * @return string The type of link - either 'external', 'cloud' or 'cloudscape'
     */
    function get_link_type($url) {
        $type = 'external';
        
        if (strpos($url, base_url().'cloud/view') === 0) {
            $type = 'cloud';
        }
        
        if (strpos($url, base_url().'cloudscape/view') === 0) {
            $type = 'cloudscape';
        }

        return $type;       
    }
    
    /**
     * Remove a link
     *
     * @param integer $link_id The ID of the link
     */
    function delete_link($link_id) {
        $this->db->delete('cloud_link', array('link_id' => $link_id));
        $this->load->model('event_model');
        $event_model = new event_model();  
        $event_model->delete_events('link', $link_id);     
    }    
    
    /**
     * Approve a link
     *
     * @param integer $link_id The ID of the link
     */
    function approve_link($link_id) {
        $this->db->where('link_id', $link_id);
        $this->db->update('cloud_link', array('moderate'=>0)); 
        $link = $this->get_link($link_id);
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->add_event('cloud', $link->cloud_id, 'link', $link_id);  
        $event_model->add_event('user', $link->user_id, 'link', $link_id);  
    }

   /**
     * Get the links for a specific cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return array Array of links
     */
    function get_links($cloud_id) {        
        $cloud_id = (int) $cloud_id;
        if (is_int($cloud_id)) {
            $query = $this->db->query("SELECT l.url, l.title, count(f.item_id)  AS total, 
                                       l.link_id, l.type, up.id AS user_id, up.fullname, 
                                       l.timestamp FROM cloud_link l 
                                       LEFT OUTER JOIN favourite f ON f.item_id = l.link_id 
                                       AND f.item_type = 'link'
                                       INNER JOIN user_profile up ON up.id = l.user_id
                                       INNER JOIN user u on u.id = up.id
                                       WHERE l.cloud_id = $cloud_id
                                       AND u.banned = 0
                                       GROUP BY l.link_id ORDER BY total DESC, timestamp ASC");
            
            return $query->result(); 
        }
    }  

    /**
     * Get a link
     *
     * @param integer $link_id The ID of the link
     * @return object The details of the link
     */
    function get_link($link_id) {
        $link = FALSE;
        $this->db->where('link_id', $link_id);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'user.id = cloud_link.user_id');        
        $this->db->join('user_profile', 'user_profile.id = cloud_link.user_id');                      
        $query = $this->db->get('cloud_link');
        
        if ($query->num_rows() !=  0 ) {
            $link = $query->row();
        }
        return $link;
    }
      
    /**
     * Get links requiring moderation
     *
     * @return array Array of links
     */
    function get_links_for_moderation() {
        $this->db->where('cloud_link.moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = cloud_link.user_id');        
        $this->db->order_by('timestamp', 'asc');
        $query = $this->db->get('cloud_link');
        return $query->result();   
    } 
    
    /**
     * Make a link the primary link for the cloud 
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $link_id The ID of the link
     */
    function make_link_primary($cloud_id, $link_id) {
        $link = $this->get_link($link_id);
        $this->db->where('cloud_id', $cloud_id);
        $this->db->update('cloud', array('primary_url'=>$link->url));
        $this->delete_link($link_id);
    }
    
    /**
     * Determine if a user is the owner of a link
     *
     * @param integer $link_id The ID of the link
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the user is the owner, FALSE otherwise
     */
    function is_owner($link_id, $user_id) {
        $owner = FALSE;
        $this->db->where('link_id', $link_id);
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('cloud_link');
        if ($query->num_rows() > 0) {
            $owner = TRUE;
        }
        
        return $owner;
    }    
}