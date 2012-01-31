<?php
/**
 * Functions related to site statistics
 * 
 * @copyright 2009, 2010, 2012 The Open University. See CREDITS.txt
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

   /**
    * Get the total number of contributions that a user has made to the site
    * where a contribution is a cloud, cloudscape, comment, link,
    * reference, extra content or embed
    * @param integer The id of the user 
    * @param integer $start_timestamp Date before which contributions must have
    * been made to be included in the count, as a Unix timestamp (optional, 
    * if not set defaults to current time)
    * @param integer $after_registration Period of time in seconds after the 
    * user has registered on the site for contributions to have been made in in
    * order to  be included in the count. (optional, if not set, no restrictions
    * made based on registration date) If this is set then $end_timestamp is 
    * ignored
    * @param integer The number of contributions 
    */
    function get_number_contributions($user_id, 
                                      $start_timestamp = false, 
                                      $end_timestamp = false, 
                                      $after_registration = false) {
        if (!$start_timestamp) {
            $start_timestamp = 0;
        }
        
        if (!$end_timestamp) {
            $end_timestamp = time();
        }
        
        if ($after_registration) {
            // Get the timestamp for when the user registered and add this to 
            // that
            $this->db->where('id', $user_id);
            $query = $this->db->get('user');
            $row = $query->row_array();
            $start_timestamp = strtotime($row['created']) + $after_registration;
        }
        
        $query = $this->db->query(
        "SELECT
        (
        (SELECT COUNT(*) FROM cloud WHERE user_id = $user_id
           AND created > $start_timestamp AND created < $end_timestamp)
        +
        (SELECT COUNT(*) FROM cloudscape WHERE user_id = $user_id 
        AND created > $start_timestamp AND created < $end_timestamp)
        + 
        (SELECT COUNT(*) FROM cloud_content  WHERE user_id = $user_id 
        AND created > $start_timestamp AND created < $end_timestamp)
        + 
        (SELECT COUNT(*) FROM cloud_link WHERE user_id = $user_id 
        AND timestamp > $start_timestamp AND timestamp< $end_timestamp)
        + 
        (SELECT COUNT(*) FROM cloud_reference WHERE user_id = $user_id 
        AND timestamp> $start_timestamp AND timestamp < $end_timestamp)
        + 
        (SELECT COUNT(*) FROM cloud_embed  WHERE user_id = $user_id 
        AND timestamp > $start_timestamp AND timestamp < $end_timestamp)
        + 
        (SELECT COUNT(*) FROM comment WHERE user_id = $user_id 
        AND timestamp > $start_timestamp AND timestamp < $end_timestamp)
        )
        AS no_contributions"
        );
        
        $row = $query->row_array();
        return $row['no_contributions'];      
    }
    
   /**
    * Get the distribution of user contributions to the site - this is divided into 
    * number of users who have made 0 contributions, 1-5 contributions,
    * 5-9 contributions, 10-49 contribtuions, 50+ contributions 
    * @param integer $start_timestamp Date after which contributions must have
    * been made to be included in the count, as a Unix timestamp (optional,
    * if not set defaults to 0)
    * @param integer $start_timestamp Date before which contributions must have
    * been made to be included in the count, as a Unix timestamp (optional, 
    * if not set defaults to current time)
    * @param integer $after_registration Period of time in seconds after the 
    * user has registered on the site for contributions to have been made in in
    * order to  be included in the count (optional, if not set, no restrictions
    * made based on registration date) If this is set then $end_timestamp is 
    * ignored
    * @param Array. An array containing the number of users with number of
    * contributions in each of the specified ranges
    */
    function get_user_contrib($start_timestamp = false, 
                              $end_timestamp = false,
                              $after_registration = false) {
        $contrib = array();
        $contrib['0']    = 0;
        $contrib['1-5']  = 0;
        $contrib['5-9'] = 0;
        $contrib['10-49'] = 0;
        $contrib['50+']  = 0;
        
        // Get all the users and loop through them, getting the number of 
        // contributions for each and then incrementing the appropriate 
        // variable
        $query = $this->db->get('user');       
        foreach ($query->result() as $row) {
            $num_contrib = $this->get_number_contributions($row->id, 
                                                           $start_timestamp, 
                                                           $end_timestamp,
                                                           $after_registration);
            if ($num_contrib == 0) {
                $contrib['0']++;
            } elseif ($num_contrib < 6) {
                $contrib['1-5']++;
            } elseif ($num_contrib < 10) {
                $contrib['5-9']++;  
            } elseif ($num_contrib < 50) {
                $contrib['10-49']++;
            } else {
                $contrib['50+']++;
            }
        }
        return $contrib;
    }
}