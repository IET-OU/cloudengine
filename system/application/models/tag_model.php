<?php 
/**
 * Functions related to tags
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Tags
 */
class Tag_model extends Model {
    
    function Tag_model() {
        parent::Model();
    }

    /**
     * Get all tags
     *
     * @param integer $num Limit to the number of tags to get
     * @return array Array of tags
     */
    function get_all_tags($num) {
        $query = $this->db->query('SELECT tag, COUNT(*) AS tag_total FROM tag GROUP BY tag 
                                   ORDER BY  tag_total DESC LIMIT '.$num);
        return $query->result();
    }
    
    /**
     * Get the total number of tags used on the site
     *
     * @return integer The number of tags
     */
    function get_total_tags() {
        $query = $this->db->get('tag');
        return $query->num_rows();
    }
    
    /**
     * Get the tags for a specific item
     *
     * @param string $item_type The type of item e.g. 'cloud', 'cloudscape' or 'user'
     * @param integer $item_id The ID of the item
     * @return array Array of tags
     */
    function get_tags($item_type, $item_id) {
        $this->db->where('item_id', $item_id);
        $this->db->where('item_type', $item_type);
        $this->db->order_by('tag', 'asc');
        $query = $this->db->get('tag');
        return $query->result();
    }	
      
    /**
     * Determine if an item is already tagged with a particular term i.e. if a tag is a
     * duplicate of an existing tag
     *
     * @param string $item_type The type of item e.g. 'cloud', 'cloudscape' or 'user'
     * @param integer $item_id The ID of the item
     * @param string $tag The tag to check
     * @return boolean TRUE if the tag is a duplicate, FALSE otherwise
     */
    function is_duplicate($item_type, $item_id, $tag) {
        $duplicate = false;
        
        $this->db->where('item_type', $item_type);
        $this->db->where('item_id', $item_id);
        $this->db->where('tag', $tag);
        $query = $this->db->get('tag');
        
        if ($query->num_rows() > 0) {
            $duplicate = true;
        }
        return $duplicate;
    }
    
    /**
     * Delete all the tags for an item.
     *
     * @param string $item_type The item type e.g. 'cloud', 'cloudscape', 'user'
     * @param integer $item_id The id of the item e.g. the cloud_id, cloudscape_id or user_id
     */
    function delete_tags($item_type, $item_id) {
        $this->db->where('item_id', $item_id);
        $this->db->where('item_type', $item_type);
        $this->db->delete('tag');        
    }

    /**
     * Add a set of tags to an item 
     *
     *
     * @param string $item_type The item type e.g. 'cloud', 'cloudscape', 'user'
     * @param integer $item_id The id of the item e.g. the cloud_id, cloudscape_id or user_id
     * @param string $tags comma-separated string of tags 
     */
    function add_tags($item_type, $item_id, $tags, $user_id) {
        $tags = split(",", $tags);
        if ($tags) {
            foreach ($tags as $tag) {
                $this->add_tag($item_type, $item_id, $tag, $user_id);
            }
        }        
    }      
    
    /**
     * Add a tag to an item
     *
     *
     * @param string $item_type The item type e.g. 'cloud', 'cloudscape', 'user'
     * @param integer $item_id The id of the item e.g. the cloud_id, cloudscape_id or user_id
     * @param string $tag The tag to add
     */
    function add_tag($item_type, $item_id, $tag, $user_id)  {
        $tag = trim($tag);
        if ($tag && !$this->tag_model->is_duplicate($item_type, $item_id, $tag)) {
            $tag_to_insert->item_id   = $item_id;
            $tag_to_insert->item_type = $item_type;
            $tag_to_insert->tag       = $tag;
            $tag_to_insert->user_id   = $user_id;
            $tag_to_insert->timestamp = time();
            $this->db->insert('tag', $tag_to_insert);      
        }
    }
    
    /**
     * Delete a specified tag 
     * @param integer $tag_id The id of the tag to delete
     */
    function delete_tag($tag_id) {
        $this->db->where('tag_id', $tag_id);
        $this->db->delete('tag');          
    }
    
    /**
     * Get a tag specified by the id for a tag 
     *
     * @param integer $tag_id The id of the tag
     * @return object
     */
    function get_tag($tag_id) {
        $tag = FALSE;
        $this->db->where('tag_id', $tag_id);
        $query = $this->db->get('tag');
        if ($query->num_rows() > 0) {
            $tag = $query->row();
        }
        
        return $tag;
    }
    
    /**
     * Get all clouds with a specific tag
     *
     * @param string $tag
     * @param integer $num Number of clouds to display
     * @param integer $offset Item number to start results from (for paging)
     * @return array Array of clouds
     */
    function get_clouds($tag, $num = false, $offset = 0) {
        $this->db->order_by('cloud.created', 'desc');
	    $this->db->join('cloud', 'tag.item_id = cloud.cloud_id');
	    $this->db->where('item_type', 'cloud');
	    $this->db->where('moderate', 0);
	    $this->db->where('tag', $tag);
	    $query = $this->db->get('tag', $num, $offset); 
	    return $query->result();
	}

	/**
	 * Get all cloudscapes with a specific tag
	 *
	 * @param string $tag The tag
	 * @param integer $num Limit to number of cloudscapes to get
	 * @param integer $offset The offset from which to start getting the tags
	 * @return array Array of cloudscapes
	 */
	function get_cloudscapes($tag, $num = false, $offset = 0) {
	    $this->db->join('cloudscape', 'tag.item_id = cloudscape.cloudscape_id');
	    $this->db->where('item_type', 'cloudscape');
	    $this->db->where('moderate', 0);
	    $this->db->where('tag', $tag);
	    $query = $this->db->get('tag', $num, $offset); 
	    return $query->result();
	}
	
	/**
	 * Get all users with a specific tag
	 *
	 * @param string $tag The tag
	 * @param integer $num Limit to number of users to get
	 * @param integer $offset The offset from which to start getting the tags
	 * @return array Array of users
	 */
	function get_users($tag, $num = false, $offset = 0) {
	    $this->db->join('user', 'tag.item_id = user.id');
	    $this->db->join('user_profile', 'user.id=user_profile.id');
	    $this->db->where('item_type', 'user');
	    $this->db->where('tag', $tag);
	    $query = $this->db->get('tag', $num, $offset); 
	    return $query->result();
	}
}