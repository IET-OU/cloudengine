<?php 
/**
 *  Model file for functions related to events i.e. cloudscapes with a date associated
 * with them
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Events
 */
class Events_model extends Model {
    
    function Events_model() {
        parent::Model();
    }

    /**
     * Get all the events in a specifed month
     *
     * @param integer $month The month as a number
     * @param integer $year The year
     * @return array Array of cloudscapes that are events for the month
     */
    function get_events_for_month($month, $year) {
        $this->load->helper('date');
        if ($month > 12) {
            // Fix a seasonal month-wrap bug [BB #106].
            $month -= 12;
            $year++;
        }
        // strtotime uses US date format with month before day of month
        $month_start_date = strtotime("$month/1/$year");
        $last_day_of_month = days_in_month($month, $year);
        $month_end_date   = strtotime("$month/$last_day_of_month/$year");
        $query = $this->db->query("SELECT * FROM cloudscape 
        WHERE (start_date >= $month_start_date AND start_date < $month_end_date) OR 
        (end_date >= $month_start_date AND end_date <= $month_end_date) ORDER BY start_date");
        
        return $query->result();
    }
    
    /**
     * Get all the clouds that are deadlines in a specifed month
     *
     * @param integer $month The month as a number
     * @param integer $year The year
     * @return array Array of clouds that are deadlines for the month
     */    
    function get_calls_for_month($month, $year) {
        $this->load->helper('date');
        // strtotime uses US date format with month before day of month
        $month_start_date = strtotime("$month/1/$year");
        $last_day_of_month = days_in_month($month, $year);
        $month_end_date   = strtotime("$month/$last_day_of_month/$year");
        $query = $this->db->query("SELECT * FROM cloud 
        						    WHERE (call_deadline >= $month_start_date 
        						    AND call_deadline <= $month_end_date) 
                                    ORDER BY call_deadline");
        
        return $query->result();
    }
    
            
    /**
     * Add a user to the attenders of the cloudscape
     *
     * @param integer $cloudscape_id
     * @param integer $user_id
     */
    function attend($cloudscape_id, $user_id) {
        if (!$this->is_attending($cloudscape_id, $user_id)) {
            $this->db->set('cloudscape_id', $cloudscape_id);
            $this->db->set('user_id', $user_id);
            $this->db->set('timestamp', time());
            $this->db->insert('cloudscape_attended');
        } 
    }
    
    /**
     * Determine if a user is attending a cloudscape
     *
     * @param integer $cloudscape_id
     * @param integer $user_id
     * @return boolean
     */
    function is_attending($cloudscape_id, $user_id) {        
        $following = false;
        if ($user_id) {
            $this->db->where('cloudscape_id', $cloudscape_id);
            $this->db->where('user_id', $user_id);
            $query = $this->db->get('cloudscape_attended');
            
            if ($query->num_rows() > 0) {
                $following = TRUE;
            }
        }
        return $following;
    }
    
    /**
     * Remove a user from the attenders of a cloudscape
     *
     * @param integer $cloudscape_id
     * @param integer $user_id
     */
    function unattend($cloudscape_id, $user_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('user_id', $user_id);
        $this->db->delete('cloudscape_attended'); 
    }
    
    /**
     * Get the events that a specified user has marked as attending that are in the past
     *
     * @param integer $user_id
     * @return array of the events
     */
    function get_past_events_attended($user_id) {
       
         $this->db->order_by('cloudscape.start_date', 'desc');
         $this->db->where('cloudscape_attended.user_id', $user_id);
         $this->db->where('(start_date < '.time().' OR end_date < '.time().')');
         $this->db->join('cloudscape', 
                         'cloudscape.cloudscape_id = cloudscape_attended.cloudscape_id');
         $query = $this->db->get('cloudscape_attended');
         return $query->result();
    }
    
    /**
     * Get the events that a specified user has marked as attending that are currently happening or in the future
     *
     * @param integer $user_id
     * @return array of the events
     */
    function get_current_events_attended($user_id) {
        $this->db->order_by('cloudscape.start_date', 'asc');
         $this->db->where('cloudscape_attended.user_id', $user_id);
         $this->db->where('start_date > '.time());
         $this->db->join('cloudscape', 
                         'cloudscape.cloudscape_id = cloudscape_attended.cloudscape_id');
         $query = $this->db->get('cloudscape_attended');
         
         return $query->result(); 
    }
    
    /**
     * Get the users who are attending or have attended a specific event
     *
     * @param integer $cloudscape_id
     * @return array of the users
     */
    function get_attendees($cloudscape_id) {
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->join('user_picture', 'cloudscape_attended.user_id = user_picture.user_id',                         'left');
        $this->db->join('user_profile', 'user_profile.id = cloudscape_attended.user_id',                        'left');
        $query = $this->db->get('cloudscape_attended');
        return $query->result();
    }
    
    /**
     * Gets the e-mail addresses of users who are attending an event and have specified that they 
     * are happy to receive e-mails
     *
     * @param integer $cloudscape_id The id of the cloudscape for the event
     * @return array of the e-mail addresses 
     */
    function get_attendees_email($cloudscape_id) {
        $this->db->where('cloudscape_attended.cloudscape_id', $cloudscape_id);
        $this->db->where('user_profile.email_events_attending', '1');
        $this->db->join('user_profile', 'user_profile.id = user.id');
        $this->db->join('cloudscape_attended', 'cloudscape_attended.user_id = user.id');
        $this->db->select('email');
        $query = $this->db->get('user');
        return $query->result();
    }
   
    /**
     * Stores the details of an e-mail sent to attendees in the database 
     *
     * @param integer $cloudscape_id The id of the cloudscape for the event
     * @param integer $user_id The id of the user who has requested the e-mail be sent
     * @param string $subject The subject of the e-mail
     * @param string $body The body/message of the e-mail
     */
    function insert_event_email($cloudscape_id, $user_id, $subject, $body) {
        $email->cloudscape_id = $cloudscape_id;
        $email->user_id       = $user_id;
        $email->subject       = $subject;
        $email->body          = $body;
        $email->timestamp     = time();
        $this->db->insert('cloudscape_email', $email);
    }
    
    /**
     * Determines if a cloudscape represents an event (based on whether a start date has been specified)
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return boolean True if it is an event, false otherwise 
     */
    function is_event($cloudscape_id) {
        $is_event = false;
        $this->db->where('cloudscape_id', $cloudscape_id);
        $query = $this->db->get('cloudscape');
        if ($query->num_rows() > 0) {
            $cloudscape = $query->row();
            if ($cloudscape->start_date) {
                $is_event = true;
            }
        }
        
        return $is_event;
    }
    
    /**
     * Returns the number of attendees of a particular event
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return integer The number of people who have indicated that they are attending
     */
    function get_total_attendees($cloudscape_id) {
        $attendees = $this->get_attendees($cloudscape_id);
        return count($attendees); 
    }
    
    /**
     * Determine if the user has exceeded the limit for number of e-mails per hour for a specific event
     *
     * @param integer $user_id The ID of the user
     * @param integer $cloudscape_id The ID of the cloudscape
     * @return boolean True if the limit has been exceeded, false otherwise      * 
     */
    function check_email_limit_exceeded($user_id, $cloudscape_id) {
        $limit_exceeded = false;
        
        $this->db->where('user_id', $user_id);
        $this->db->where('cloudscape_id', $cloudscape_id);
        $this->db->where('timestamp > ', time() - 60*60);
        $query = $this->db->get('cloudscape_email');
        
        if ($query->num_rows() >= $this->config->item(email_event_attending_limit_per_hour)) {
            $limit_exceeded = true;
        }
        
        return $limit_exceeded;
    }  
}  