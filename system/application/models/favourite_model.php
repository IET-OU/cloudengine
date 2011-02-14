<?php 
/**
 *  Model file for functions related to favourites
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Favourites
 */
class Favourite_model extends Model {
    
    function Favourite_model() {
        parent::Model();
    }

    /**
     * Add a favourite for an item by the specified user
     *
     * @param integer $user_id The ID of the user
     * @param integer $item_id The ID of the item
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     */
    function add_favourite($user_id, $item_id, $item_type) {
        if ($user_id && $item_id && $this->can_favourite_item($user_id, $item_id, $item_type)) {
            $this->db->insert('favourite', array('user_id' => $user_id, 
                                                 'item_id' => $item_id, 
                                                 'item_type' => $item_type, 
                                                 'timestamp'=>time()));
        }
    }
    
    /**
     * Remove a favourite for an item and specifed user
     *
     * @param integer $user_id The ID of the user
     * @param integer $item_id The ID of the item
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     */
    function remove_favourite($user_id, $item_id, $item_type) {
        $this->db->delete('favourite', array('user_id' => $user_id, 'item_id' => $item_id, 
                                             'item_type' => $item_type));

    }
    
    /**
     * Determine if a user has favourited a specific item
     *
     * @param integer $user_id The ID of the user
     * @param integer $item_id The ID of the item
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     * @return boolean TRUE if they have favourited it, FALSE otherwise
     */
    function is_favourite($user_id, $item_id, $item_type) {
        $favourite = true;
        $this->db->where('user_id', $user_id);
        $this->db->where('item_id', $item_id);
        $this->db->where('item_type', $item_type);
        $query = $this->db->get('favourite');
        if ($query->num_rows() == 0) {
            $favourite = false;
        }
        return $favourite;
    }
    
    /**
     * Get the number of favourites for a specified item
     *
     * @param integer $item_id The ID of the item
     * @param integer The number of favourites 
     */
    function get_total_favourites($item_id, $item_type) {
        $this->db->where('item_id', $item_id);
        $this->db->where('item_type', $item_type);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'favourite.user_id = user.id');        
        $query = $this->db->get('favourite'); 
        return $query->num_rows();
    }
    
    /**
     * Get the reputation for a user - this is equal to the total number of favourites on a 
     * user's clouds, cloudscapes and links
     *
     * @param integer $user_id The ID of the user
     * @return integer The total number of votes 
     */
    function get_reputation($user_id) {
        $user_id = (int) $user_id;
        $reputation = 0;
        // Get all the cloud favourites
        $query = $this->db->query("SELECT * FROM cloud c INNER JOIN favourite f 
                                   ON f.item_id = c.cloud_id 
                                   WHERE c.user_id = $user_id AND f.item_type = 'cloud'");
        $reputation += $query->num_rows();
        // Get all the cloudscape favourites
        $query = $this->db->query("SELECT * FROM cloudscape c INNER JOIN favourite f 
                                   ON f.item_id = c.cloudscape_id 
                                   WHERE c.user_id = $user_id 
                                   AND f.item_type = 'cloudscape'");
        $reputation += $query->num_rows();

        // Get all the link favourites
        $query = $this->db->query("SELECT * FROM cloud_link l INNER JOIN favourite f 
                                   ON f.item_id = l.link_id 
                                   WHERE l.user_id = $user_id AND f.item_type = 'link'");
        $reputation += $query->num_rows();      

        return $reputation;    
    }
    
    /**
     * Determine if the user in question can vote i.e. if they are generally allowed to vote
     *
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if they can vote, FALSE otherwise
     */
    function can_favourite($user_id) {
        $can_favourite = false;
        // Only allow users with a certain number of votes to vote themselves
        if ($this->get_reputation($user_id) > -1) {
            $can_favourite = true;
        }
        
        $this->CI = & get_instance();
        if ($this->CI->auth_lib->is_admin()) {
             $can_favourite = true;
        }
        return $can_favourite;
    }
    
    /**
     * Determine if the user in question is allowed to vote on this particular item
     *
     * @param integer $user_id The ID of the user
     * @param integer $item_id The ID of the item
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     */
    function can_favourite_item($user_id, $item_id, $item_type) {
        $can_favourite= true;
        // Check if reputation high enough to add favourites at all
        if (!$this->can_favourite($user_id)) {
            $can_favourite = false;
        }
        
        // If the user has already voted on this item, they can't vote again
        if ($this->is_favourite($user_id, $item_id, $item_type)) {
            $can_favourite = false;
        }
        
        if ($this->is_owner($user_id, $item_id, $item_type)) {
            $can_favourite = false;
        }
        return $can_favourite; 
    }
    
    /**
     * Determine if a user is the owner of a particular item
     *
     * @param integer $user_id The ID of the user
     * @param integer $item_id The ID of the item
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     */
    function is_owner($user_id, $item_id, $item_type) {
        $is_owner = false;
        $this->CI = & get_instance();
        switch ($item_type) {
            case 'cloud':
                $this->CI->load->model('cloud_model');
                if ($this->CI->cloud_model->is_owner($item_id, $user_id)) {
                    $is_owner = true;
                }
                break;
            case 'cloudscape':
                $this->CI->load->model('cloudscape_model');
                if ($this->CI->cloudscape_model->is_owner($item_id, $user_id)) {
                    $is_owner = true;
                }
                break;
            case 'link': 
                $this->CI->load->model('link_model');
                if ($this->CI->link_model->is_owner($item_id, $user_id)) {
                    $is_owner = true;
                }                
                break;
        }   
        return $is_owner;
    }
    
    /**
     * Get the items of a particular type that a user has favourited
     *
     * @param integer $user_id The ID of the user
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     * @return array Array of items
     */
    function get_favourites($user_id, $item_type=NULL) {
        switch ($item_type) {
        case 'cloud': 
            $this->db->order_by('favourite.timestamp', 'desc');
            $this->db->where('favourite.user_id', $user_id);
            $this->db->where('favourite.item_type', 'cloud');
            $this->db->join('cloud', 'cloud.cloud_id = favourite.item_id');
            $query = $this->db->get('favourite');
            $items = $query->result();
            break;

        case 'cloudscape': 
            $this->db->order_by('favourite.timestamp', 'desc');
            $this->db->where('favourite.user_id', $user_id);
            $this->db->where('favourite.item_type', 'cloudscape');
            $this->db->join('cloudscape', 'cloudscape.cloudscape_id = favourite.item_id');
            $query = $this->db->get('favourite');     
            $items = $query->result();    
            break;

        case NULL: //Recommended links are in the 'favourite' table - we don't want them.
            $query = $this->db->query("
              SELECT c.user_id,item_id,item_type,timestamp,created,title,summary
              FROM favourite AS f
              JOIN cloudscape AS c ON f.item_id = c.cloudscape_id
              WHERE f.user_id = $user_id
              AND (f.item_type = 'cloudscape')
            UNION
              SELECT f.user_id,item_id,item_type,timestamp,created,title,summary
              FROM favourite AS f
              JOIN cloud AS c ON f.item_id = c.cloud_id
              WHERE f.user_id = $user_id
              AND (f.item_type = 'cloud')
              ORDER BY timestamp");    
            $items = $query->result();    
            break;
        }
        return $items;
    }
    
    /**
     * Get a list of the users who have favourited a particular item
     *
     * @param integer $user_id The ID of the user
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     * @return array Array of users
     */
    function get_users_favourited($item_id, $item_type) {
        $this->db->order_by('favourite.timestamp', 'desc');
        $this->db->where('item_id', $item_id);
        $this->db->where('item_type', $item_type);
        $this->db->where('user.banned',0);  
        $this->db->join('user', 'favourite.user_id = user.id');        
        $this->db->join('user_picture', 'favourite.user_id = user_picture.user_id', 'left');
        $this->db->join('user_profile', 'user_profile.id = favourite.user_id');
        $query = $this->db->get('favourite');
        return $query->result();
    }
    
    /**
     * Get the most favourited items of a particular type
     *
     * @param string $item_type e.g. 'cloud', 'cloudscape' or 'link'
     * @param integer $num Number of items to get
     * @return array Array of items
     */
    function get_popular($item_type, $num) {
        switch($item_type) {
            case 'cloud': 
                $query = $this->db->query("SELECT c.cloud_id AS item_id, c.title, 
                                           COUNT(*) AS total_favourites 
                                           FROM cloud c INNER JOIN favourite f 
                                           ON f.item_id = c.cloud_id 
                                           WHERE item_type = 'cloud'
                                           GROUP BY c.cloud_id 
                                           ORDER BY total_favourites DESC LIMIT $num"); 
                break;
            case 'cloudscape':
                $query = $this->db->query("SELECT c.cloudscape_id AS item_id, c.title, 
                                          COUNT(*) AS total_favourites 
                                          FROM cloudscape c INNER JOIN favourite f 
                                          ON f.item_id = c.cloudscape_id 
                                          WHERE item_type = 'cloudscape'
                                          GROUP BY c.cloudscape_id 
                                          ORDER BY total_favourites DESC LIMIT $num"); 
        }
  
        return $query->result();
    }
}