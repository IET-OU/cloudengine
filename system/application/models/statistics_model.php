<?php
/**
 * Functions related to site statistics
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Statistics
 */
class Statistics_model extends Model {

    function Statistics_model() {
        parent::Model();
    }
    
    /**
     * Construct the contents for a SQL WHERE clause to filter by team members as specified 
     * in the config file
     *
     * @return string The SQL string to use to filter by the team
     */
    private function _where_team() {
    	$clause = FALSE;
        $this->CI = & get_instance();
        $team = $this->CI->config->item('team');
        if ($team) {
        	$clause = "(user_id IN (".$team ."))";
        	
        }
        return $clause;
    }
    
    /**
     * Get the total number of users registered on the site between two dates
     *
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of users
     */
    function get_total_users($start_timestamp = false, $end_timestamp = false) {
        
        if ($start_timestamp) {
            $this->db->where("created > FROM_UNIXTIME($start_timestamp)");
        }
        
        if ($end_timestamp) {
             $this->db->where("created < FROM_UNIXTIME($end_timestamp)");
        }
        
        $query = $this->db->get('user');
        return $query->num_rows();
    }
    
    /**
     * Get the number of active users i.e. who have logged onto the site, betwee two times
     *
     * @param integer $start_timestamp The start time as a Unix timestampe
     * @param integer $end_timestamp The end time as a Unix timestamp
     * @return integer The number of users
     */
    function get_active_users($start_timestamp, $end_timestamp) {
        $query = $this->db->query("SELECT DISTINCT user_id FROM logs WHERE 
                                   user_id <> 0 AND timestamp >= $start_timestamp AND 
                                   timestamp <= $end_timestamp");
        return $query->num_rows();
    }
    
    /**
     * Get the total number of clouds created between two times
     *
     * @param boolean $team If TRUE, only include clouds created by the team as specified
     *  in the config file
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of clouds
     */
    function get_total_clouds($team = false, $start_timestamp = false, 
                              $end_timestamp = false) {
        if ($team && config_item('team')) {
          $this->db->where($this->_where_team());
        }
        
        if ($start_timestamp) {
            $this->db->where('created >', $start_timestamp);
        }
        
        if ($end_timestamp) {
             $this->db->where('created <', $end_timestamp);
        }
        
        $query = $this->db->get('cloud');
        return $query->num_rows();
    }  
    
    /**
     * Get the total number of cloudscapes created between two times
     *
     * @param boolean $team If TRUE, only include cloudscapes created by the team as 
     * specified in the config file
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of cloudscapes
     */
    function get_total_cloudscapes($team = false, $start_timestamp = false, 
                                   $end_timestamp = false) {
        if ($team && config_item('team')) {
           $this->db->where($this->_where_team());
        }
        if ($start_timestamp) {
            $this->db->where('created >', $start_timestamp);
        }
        
        if ($end_timestamp) {
             $this->db->where('created <', $end_timestamp);
        }
        
        $query = $this->db->get('cloudscape');
        return $query->num_rows();
    }   

    /**
     * Get the total number of distinct tags that have been used on the site
     *
     * @param boolean $team If TRUE, only include tags created by the team as 
     * specified in the config file
     * @return integer The number of tags
     */
    function get_total_tags($team = false) {
        if ($team && config_item('team')) {
          $this->db->where($this->_where_team());
        }        
        $query = $this->db->get('tag');
        return $query->num_rows();
    }   

    /**
     * Get the total number of comments created between two times
     *
     * @param boolean $team If TRUE, only include comments  created by the team as 
     * specified in the config file
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of comments 
     */
    function get_total_comments($team = false, $start_timestamp = false, 
                                $end_timestamp = false) {
        if ($team && config_item('team')) {
           $this->db->where($this->_where_team());
        }
        if ($start_timestamp) {
            $this->db->where('timestamp >', $start_timestamp);
        }
        
        if ($end_timestamp) {
             $this->db->where('timestamp <', $end_timestamp);
        }
        
        $query = $this->db->get('comment');
        return $query->num_rows();
    }   
    
    /**
     * Get the total number of links created between two times
     *
     * @param boolean $team If TRUE, only include links created by the team as 
     * specified in the config file
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of links
     */
    function get_total_links($team = false, $start_timestamp = false, 
                             $end_timestamp = false) {
        if ($team && config_item('team')) {
          $this->db->where($this->_where_team());
        }       
        if ($start_timestamp) {
            $this->db->where('timestamp >', $start_timestamp);
        }
        
        if ($end_timestamp) {
             $this->db->where('timestamp <', $end_timestamp);
        }
        
        $query = $this->db->get('cloud_link');
        return $query->num_rows();
    }      
    
    /**
     * Get the total number of extra content items created between two times
     *
     * @param boolean $team If TRUE, only include  extra content items created by the team as 
     * specified in the config file
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of  extra content items
     */    
    function get_total_content($team = false, $start_timestamp = false, 
                               $end_timestamp = false) {
        if ($team && config_item('team')) {
          $this->db->where($this->_where_team());
        }          
        if ($start_timestamp) {
            $this->db->where('created >', $start_timestamp);
        }
        
        if ($end_timestamp) {
             $this->db->where('created <', $end_timestamp);
        }
        
        $query = $this->db->get('cloud_content');
        return $query->num_rows();
    }      

    /**
     * Get the total number of embeds created between two times
     *
     * @param boolean $team If TRUE, only include cloudscapes created by the team as 
     * specified in the config file
     * @param integer $start_timestamp The start time as a Unix timestamp (or FALSE to not
     * specify a start time)
     * @param integer $end_timestamp The end time as a Unix timestamp (or FALSE to not
     * specify an end time)
     * @return integer The number of embeds 
     */    
    function get_total_embeds($team = false, $start_timestamp = false, 
                              $end_timestamp = false) {
       if ($team && config_item('team')) {
         $this->db->where($this->_where_team());
       }    
        
        if ($start_timestamp) {
            $this->db->where('timestamp >', $start_timestamp);
        }
        
        if ($end_timestamp) {
             $this->db->where('timestamp <', $end_timestamp);
        }
        
        $query = $this->db->get('cloud_embed');
        return $query->num_rows();
    }     
    
	/**
	 * Get the number of guest visitors (i.e. not logged on) who have visited a cloud
	 * in a specified cloudscape
	 * - Visitors with the same IP address count as a single visitor
	 *
	 * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $start_timestamp The start time as a Unix timestamp 
     * @param integer $end_timestamp The end time as a Unix timestamp 
	 * @return integer The number of guest visitors
	 */
    function get_cloudscape_cloud_visitors_guest($cloudscape_id, $starttime, $endtime) {
        $sql = "SELECT * FROM logs l 
                INNER JOIN cloudscape_cloud c ON c.cloud_id = l.item_id
                WHERE l.item_type='cloud' AND l.user_id = 0 
                AND c.cloudscape_id = $cloudscape_id 
                AND l.timestamp > $starttime AND l.timestamp < $endtime
                GROUP BY l.ip";
        
        $query = $this->db->query($sql);
        
        $total_visitors = $query->num_rows();
         
        return $total_visitors;
    }
    
	/**
	 * Get the number of logged in visitors who have visited a cloud
	 * in a specified cloudscape
	 * - Visitors with the same  user ID count as a single visitor
	 *
	 * @param integer $cloudscape_id The ID of the cloudscape
     * @param integer $start_timestamp The start time as a Unix timestamp 
     * @param integer $end_timestamp The end time as a Unix timestamp 
	 * @return integer The number of logged in visitors
	 */
    function get_cloudscape_cloud_visitors_logged_in($cloudscape_id, $starttime, $endtime) {
        $sql = "SELECT * FROM logs l 
                INNER JOIN cloudscape_cloud c ON c.cloud_id = l.item_id
                WHERE l.item_type='cloud' AND l.user_id <> 0 
                AND c.cloudscape_id = $cloudscape_id 
                AND l.timestamp > $starttime AND l.timestamp < $endtime
                GROUP BY l.user_id";
        
        $query = $this->db->query($sql);
        
        $total_visitors = $query->num_rows();
         
        return $total_visitors;
    }
    
    /**
     * Get the statistics for a cloudscape
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return object Details of all the statistics calculated for the cloudscape
     */
    function get_cloudscape_stats($cloudscape_id) {
        $this->CI = &get_instance();
        $this->CI->load->model('cloudscape_model');
        $this->CI->load->model('events_model');
        $cloudscape = $this->CI->cloudscape_model->get_cloudscape($cloudscape_id);
        $cloudscape->total_clouds = 
                               count($this->CI->cloudscape_model->get_clouds($cloudscape_id));
        $cloudscape->total_views =  
                                 $this->CI->cloudscape_model->get_total_views($cloudscape_id);
        $cloudscape->total_followers  = 
                             $this->CI->cloudscape_model->get_total_followers($cloudscape_id);
        $cloudscape->total_attendees  = 
                                count($this->CI->events_model->get_attendees($cloudscape_id));
        $cloudscape->total_comments   = 
                              $this->CI->cloudscape_model->get_total_comments($cloudscape_id);
        $cloudscape->total_links      = 
                                 $this->CI->cloudscape_model->get_total_links($cloudscape_id);
        $cloudscape->total_references = 
                            $this->CI->cloudscape_model->get_total_references($cloudscape_id);
        $cloudscape->total_embeds = 
                                $this->CI->cloudscape_model->get_total_embeds($cloudscape_id);
        $cloudscape->total_content = 
                               $this->CI->cloudscape_model->get_total_content($cloudscape_id);
        $cloudscape->total_commenters = 
                            $this->CI->cloudscape_model->get_total_commenters($cloudscape_id);
        $cloudscape->total_logged_in = 
                    $this->get_cloudscape_cloud_visitors_logged_in($cloudscape_id, 0, time()); 
        $cloudscape->total_guests = 
                        $this->get_cloudscape_cloud_visitors_guest($cloudscape_id, 0, time());
        
        return $cloudscape;
    }  
}