<?php 
/**
 * Model file for functions related to comments on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Comment
 */
class Comment_model extends Model {
    
    function Comment_model() {
        parent::Model();
    }
    
    /**
     * Get all the comments on the site ordered by most recent first
     *
     * @param integer $num Limit on number of comments
     * @param integer $offset Offset where to start getting comments
     * @return array Array of comments 
     */
    function get_comment_list($num = false, $offset = 0) {
        $this->db->order_by('timestamp', 'desc');
        $this->db->where('moderate', 0);
        $query = $this->db->get('comment', $num, $offset);
        return $query->result();
    }
    
    /**
     * Get a specific comment 
     *
     * @param integer $comment_id The ID of the comment
     * @return object The details of the comment
     */
    function get_comment($comment_id) {
        $comment = false;
        $this->db->select('comment_id, comment.body AS body, comment.user_id AS user_id, 
                           user_profile.fullname AS fullname, cloud.title AS cloud_title, 
                           comment.cloud_id AS cloud_id, comment.modified AS modified,
                           comment.timestamp AS timestamp');
        $this->db->join('cloud', 'cloud.cloud_id = comment.cloud_id');
        $this->db->join('user_profile', 'user_profile.id = comment.user_id');
        $this->db->where('comment_id', $comment_id);
        $query = $this->db->get('comment');
        if ($query->num_rows() !=  0 ) {
            $comment = $query->row();
        }
        return $comment;
    }  
       
    /**
     * Get all comments that need moderating
     *
     * @return array Array of comments
     */
    function get_comments_for_moderation() {
        $this->db->where('moderate', 1);
        $this->db->join('user_profile', 'user_profile.id = comment.user_id');
        $query = $this->db->get('comment');
        return $query->result();
    }
    
    /**
     * Approve a comment that has been flagged for moderation 
     *
     * @param integer $comment_id The ID of the comment
     */
    function approve_comment($comment_id) {
        $this->db->where('comment_id', $comment_id);
        $this->db->update('comment', array('moderate'=>0)); 
        $comment = $this->get_comment($comment_id);
        $this->load->model('event_model');
        $event_model = new event_model();
        $event_model->add_event('user', $comment->user_id, 'comment', $comment_id, 
                                $comment->timestamp);
        $event_model->add_event('cloud', $comment->cloud_id, 'comment', $comment_id, 
                                $comment->timestamp); 
        
        $this->_send_comment_email($comment);       
    }    
    
    /**
     * Send comment notification e-mails to the appropriate people i.e. the cloud owners
     * and anybody else who has commented on the cloud, apart from anybody who has said 
     * in their preferences that they do not want follow up comments. 
     *
     * @param object $comment The details of the comment
     */
    private function _send_comment_email($comment) {
        $this->CI = & get_instance();
        $this->CI->load->plugin('phpmailer');
        $this->CI->load->model('cloud_model');
        $this->CI->load->model('user_model');
        // Get the comment and the cloud that the comment is on and the commenter
        $cloud     = $this->CI->cloud_model->get_cloud($comment->cloud_id);
        $commenter = $this->CI->user_model->get_user($comment->user_id);
        
        // Add the cloud owner to the list of emails to send to if their preferences say so
        $email_list = array();
        if ($this->CI->user_model->email_comment($cloud->id) && $cloud->id != $commenter->id) {
            $email_list[] = $this->CI->user_model->get_user($cloud->id); 
        }
        
        // Add anybody who wants followup comments to the list of emails 
        $comments  = $this->CI->comment_model->get_comments($cloud->cloud_id);
        if ($comments) {
            foreach ($comments as $comment) { 
                $commenter = $this->CI->user_model->get_user($comment->user_id);
                if ($commenter->id != $commenter->id  // Don't send to the new commenter 
                    && !in_array($commenter,  $email_list) // Don't send to somebody twice
                    && $commenter->email_comment_followup) {      // Check person wants email
                        $email_list[] = $commenter;           // Add to the list of emails
                }
            }        
        }
        
        $data['cloud'] = $cloud;
        $data['commenter'] = $commenter;
        $data['comment']   = $comment;
        // Now send message for each recipient in turn. 
        if ($email_list) {
            foreach ($email_list as $recipient) {
                $data['recipient'] = $recipient;
                $message = $this->CI->load->view('email/comment', $data, true);
                $subject= 'New comment on \''.$cloud->title.'\' on '.
                           $this->CI->config->item('site_name');
                // only send the e-mail if it's the live site otherwise send it to the 
                // site e-mail address for debug purposes 
                $to = $this->config->item('x_live')? $recipient->email : 
                                                    $this->config->item('site_email');                
             } 
                
             send_email($to,  $this->CI->config->item('site_email'), $subject, $message);
        }
    } 
    
    /**
     * Get the user id of the person who made a comment
     *
     * @param integer $comment_id The ID of the comment
     * @return integer The ID of the user 
     */
    function get_commenter($comment_id) {
        $user_id = false;
        $this->db->where('comment_id', $comment_id);
        $query = $this->db->get('comment');
        if ($query->num_rows() !=  0 ) {
            $comment = $query->row();
        }
        if ($comment) {
            $user_id = $comment->user_id;
        }
        return $user_id;        
    }
    
    /**
     * Insert a new comment on a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @param integer $user_id The ID of the user making the comment
     * @param string $body The body of the comment
     * @param boolean $moderate TRUE if the comment needs to be moderated, FALSE otherwise
     * @param integer $timestamp The time the comment was added as a Unix time stampe
     * @param boolean $modified Whether the comment has been editted (this should always
     * be false for a new comment, so not sure what it is doing here!)
     * @return integer The ID of the new comment
     */
    function insert_comment($cloud_id, $user_id, $body, $moderate, $timestamp = null, 
                            $modified = null) {
        if (!$moderate) {
            $moderate = 0;
        }
        $comment->cloud_id = $cloud_id;
        $comment->body     = $body;
        $comment->user_id  = $user_id;
        if (!$moderate) {
            $moderate = 0;
        }
        $comment->moderate = $moderate;
        $comment->timestamp = time();
        if ($timestamp && $timestamp != 0) {
            $comment->timestamp = $timestamp;
        }
        
        $comment->modified = null;
        if ($modified) {
            $comment->modified = $modified;
        }
        $this->db->insert('comment', $comment);
        $comment_id = $this->db->insert_id();
        if (!$comment->moderate) {
            $this->approve_comment($comment_id);
        }
        
        $this->CI=& get_instance();
        $this->CI->load->model('cloud_model');
        $this->CI->cloud_model->update_in_search_index($cloud_id);
                
        return $comment_id;
    }
    
    /**
     * Update an existing comment
     *
     * @param object $comment The details of the comment to update
     */
    function update_comment($comment) {
        $comment_id       = $comment->comment_id;
        $comment->modified = time();
        $this->db->update('comment', $comment, array('comment_id'=>$comment_id));      
    }

    /**
     * Delete and existing comment item
     *
     * @param integer $comment_id The ID of the comment
     */
    function delete_comment($comment_id) {
        $this->db->delete('comment', array('comment_id' => $comment_id)); 
        $this->load->model('event_model');
        $event_model = new event_model();           
        $event_model->delete_events('comment', $comment_id);        
    } 

    /**
     * Get the latest comments on clouds on the site
     * 
     * @param integer $num Limit on the number of comments to get, FALSE if no limit
     * @param integer $offset The offset where to start getting the comments 
     * @return array Array of comments
     */
    function get_latest_comments($num = false, $offset = 0) {
        $this->db->where('comment.moderate', 0);
        $this->db->select('comment.body as body, comment.timestamp as timestamp, 
                           comment.cloud_id as cloud_id, cloud.title as title');
        $this->db->join('cloud', 'comment.cloud_id = cloud.cloud_id');
        $this->db->order_by('comment.timestamp', 'desc');
        $query = $this->db->get('comment', $num, $offset);
        return $query->result();
    }  

    /**
     * Get the comments on a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return array Array of comments
     */
    function get_comments($cloud_id) {
        $this->db->select('comment_id, comment.user_id as user_id, picture, fullname, 
                           body, email_comment, email_comment_followup, timestamp, modified');
        $this->db->from('comment');
        $this->db->where('comment.moderate', 0);
        $this->db->where('comment.cloud_id', $cloud_id);
        $this->db->order_by('timestamp', 'asc');
        $this->db->join('user_profile', 'user_profile.id = comment.user_id');      
        $this->db->join('user_picture', 'user_profile.id = user_picture.user_id', 'left');

        $query = $this->db->get();
        return $query->result();
    }
        
    /**
     * Get total number of comments on a cloud
     *
     * @param integer $cloud_id The ID of the cloud
     * @return integer The number of comments on the cloud
     */
    function get_total_comments($cloud_id = null) {
        if ($cloud_id) {
            $this->db->where('cloud_id', $cloud_id);
        }
        $this->db->where('moderate', 0);
        $query = $this->db->get('comment'); 
        return $query->num_rows();       
    }   
    
    /**
     * Determine if a specified user has permission to edit a specified comment
     *
     * @param integer $user_id The ID of the user
     * @param integer $comment_id The ID of the comment
     * @return boolean TRUE if the user has permission, FALSE otherwise
     */
    function has_edit_permission($user_id, $comment_id) {
        $permission = false;
        if ($user_id) {
           
            $comment = $this->get_comment($comment_id);
            if ($user_id == $comment->user_id || $this->auth_lib->is_admin()) {
                $permission = true;
            }
        }
        return $permission;
    }

    /**
     * Display an error if specified user does not have permission to edit the 
     * specified comment
     *
     * @param integer $user_id The ID of the user
     * @param integer $comment_id The ID of the comment
     */
    function check_edit_permission($user_id, $comment_id) {
        if (!$this->has_edit_permission($user_id, $comment_id)) {
            show_error(t("You do not have edit permission for that comment."));
        } 
    }    
}