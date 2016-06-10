<?php

/**
 * Model functionality relating to messages
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Comment
 */

class Message_model extends Model {

	function __construct()
	{
		parent::Model();
	}

  /**
   * Get a list messages for a user 
   *
   * @param integer $user_id
   * 
   * @return query object of user threads
   */
  function get_user_threads($user_id) {
      $user_id = (int) $user_id;
      $query = $this->db->query("
      SELECT mt.thread_id, 
      	Min(mt.subject) AS subject, 
      	Min(m.created) AS created_date, 
      	Min(u1.user_name) AS creator, 
      	Max(m.created) AS last_message_date, 
        (select content from message inner join user on user.id = message.author_user_id and message.created = (select max(created) from message where thread_id = mt.thread_id)) as content,
      	Count(m.message_id) AS total_messages, 
      	Sum(Abs(is_new)) AS new_messages,
      	(select group_concat(DISTINCT recipient_user_id SEPARATOR ', ') FROM message_recipient WHERE message_id in (select message_id from message where thread_id = mt.thread_id)) as participant_ids,
        (select author_user_id from message inner join user on user.id = message.author_user_id and message.created = (select max(created) from message where thread_id = mt.thread_id)) as last_message_author_id
      FROM user u
      INNER JOIN message_recipient mr
      	ON u.id=mr.recipient_user_id
      INNER JOIN message m 
      	ON m.message_id = mr.message_id
      INNER JOIN message_thread mt
      	ON mt.thread_id = m.thread_id
      INNER JOIN message_thread_participant mtp
      	ON mt.thread_id=mtp.thread_id
      INNER JOIN user u1 
      	ON mt.author_user_id=u1.id
      INNER JOIN user u2 
      	ON m.author_user_id=u2.id
      WHERE 	mr.is_deleted=0 
      AND 		u.id = $user_id
      AND 		mtp.participant_user_id = $user_id 
      AND 		mtp.is_deleted=0
      AND 		mtp.is_archived=0
      GROUP BY mt.thread_id
      ORDER BY last_message_date desc;
      ");
   
    return $query->result();
    
  }

  /**
   * get a thread
   *
   * @param integer $thread_id
   * 
   * @return query object of a thread
   */
  function get_thread($thread_id = 0) {
      $thread_id = (int) $thread_id;
        $query = $this->db->query("SELECT *
            FROM message_thread mt        
            WHERE mt.thread_id = $thread_id
      ");
    return $query->row();
  }

  /**
   * Get a thread for a user (each user has their own copy of a thread)
   *
   * @param integer $user_id
   * @param integer $thread_id
   * 
   * @return query object of a thread
   */
  function get_user_thread($user_id, $thread_id = 0) {  
    $result = 0;
    $user_id = (int) $user_id;
    $thread_id = (int) $thread_id;
    $query = $this->db->query("
      SELECT 
      t.subject,
      m.message_id,
      m.author_user_id,
      is_new,
      is_spam,
      mr.is_deleted,
      user_name,
      content,
      m.created
      FROM message_recipient mr
      INNER JOIN message m
      	ON m.message_id = mr.message_id
      INNER JOIN message_thread mt
      	ON mt.thread_id = m.thread_id
      INNER JOIN user u1
      	on u1.id = m.author_user_id
      INNER JOIN message_thread t
        on t.thread_id = mt.thread_id        
      WHERE recipient_user_id = $user_id
      AND mt.thread_id = $thread_id
      AND mr.is_deleted = 0
      AND mr.is_spam = 0
      ORDER BY m.created
      ");
      
    if ($query->num_rows()) {  
      $result = $query->result();
    }
    
    return $result;
    
  }

  /**
   * Get participants of a thread
   *
   * @param integer $thread_id
   * @param integer $user_id (if this is supplied, it will exclude the user_id from the result set)
   * 
   * @return array of participant data
   */
  function get_thread_participants($thread_id = 0, $user_id = 0) {
      $thread_id = (int) $thread_id;
      $user_id   = (int) $user_id;
    $query = $this->db->query("
      SELECT participant_user_id as user_id, fullname as name 
      FROM message_thread_participant mtp
      INNER JOIN user_profile up on up.id = mtp.participant_user_id
      WHERE thread_id = $thread_id
      AND up.id != $user_id
      ");
    $participant_ids = $query->result(); 
    $participants = array();
    foreach ($participant_ids as $id) {
      $participants[] = $id->participant_user_id;
    }
    return $participant_ids;
  }

  /**
   * Create a thread 
   *
   * @param  object thread
   * @return integer new thread id
   */
  function create_thread($thread) {
        $thread->created = time();
        $this->db->insert('message_thread', $thread);
        $thread_id =  $this->db->insert_id(); 
        return $thread_id;
  }

  /**
   * Create thread participants 
   *
   * @param object thread particpants
   */
  function create_thread_participant($thread_participant) { 
        $this->db->insert('message_thread_participant', $thread_participant);
  }

  /**
   * Send a message 
   *
   * @param object $messge object
   * 
   * @return new message id
   */
  function create_message($message) {
        $message->created = time();
        $this->db->insert('message', $message);
        $message_id =  $this->db->insert_id(); 
        return $message_id;
  } 
  
  /**
   * Send a message 
   *
   * @param object message recipient
   * 
   */
  function create_message_recipient($message_recipient) {
        $this->db->insert('message_recipient', $message_recipient);
  }   
  
  /**
   * Flag a message 
   *  - read
   *  - unread
   *  - deleted
   *  - spam
   *
   * @param integer $message_id
   * @param integer $recipient_user_id 
   * @param string $action_field
   * @param string $action_value
   */
  function flag_message($message_id,$recipient_user_id,$action_field,$action_value) {
    $this->db->set($action_field, $action_value);
    $this->db->where('message_id', $message_id);
    $this->db->where('recipient_user_id', $recipient_user_id);    
    $this->db->update('message_recipient'); 
  }   

  /**
   * Flag a thread 
   *  - deleted
   *  - archived
   *
   * @param integer $thread_id
   * @param integer $participant_user_id
   * @param string $action_field
   * @param string $action_value
   */
  function flag_thread($thread_id,$participant_user_id,$action_field,$action_value) {   
    $this->db->set($action_field, $action_value);
    $this->db->where('thread_id', $thread_id);
    $this->db->where('participant_user_id', $participant_user_id);    
    $this->db->update('message_thread_participant'); 
  }   


  /**
   * Get the most recent 'undeleted' message in a thread for a user
   *  
   * @param integer $thread_id
   * @param integer $user_id
   * 
   * @return message_id integer
   */
  function get_most_recent_thread_message_id($thread_id,$user_id) {
      $user_id = (int) $user_id;
      $thread_id = (int) $thread_id;
      $query = $this->db->query("
      SELECT max(mr.message_id) as message_id
      FROM message m
      INNER JOIN message_recipient mr
      	ON m.message_id = mr.message_id
      WHERE m.thread_id = $thread_id
      AND mr.recipient_user_id = $user_id
      AND is_deleted = 0");
    return $query->row()->message_id;      
  } 
    
  /**
   * Get message_ids in a thread for a user
   *  
   * @param integer $thread_id
   * @param integer $user_id
   * 
   * @return array message_ids integer
   */
  function get_thread_message_ids($thread_id, $user_id) {   
      $thread_id = (int) $thread_id;
      $user_id = (int) $user_id;
      $query = $this->db->query("
      SELECT mr.message_id
      FROM message m
      INNER JOIN message_recipient mr
      	ON m.message_id = mr.message_id
      WHERE m.thread_id = $thread_id
      AND mr.recipient_user_id = $user_id
      AND is_deleted = 0");

      foreach ($query->result_array() as $message_id) {
        $message_ids[] = $message_id['message_id']; 
      }      
      
      return $message_ids;      
  } 
 
  /**
   * Get receipients for the compose 'To' dropdown jQuery/Ajax call
   *  
   * @param integer $thread_id
   * @param integer $user_id
   * 
   * @return message_id integer
   */
  function get_recipients($term) {  
      $term = $this->db->escape_str($term);
      $query = $this->db->query("
      SELECT u.id,user_name, fullname
      FROM user u
      INNER JOIN user_profile up
      	ON up.id = u.id
      	ON up.id = u.id
      WHERE fullname like '%$term%'
      ORDER BY fullname
      LIMIT 0,100");  
      
    return $query->result_array();    
      
  }  
  
  
    /**
   * Get count of unread messages for a user
   * 
   * @param integer $user_id
   * 
   * @return message count integer
   */
  function get_user_new_message_count($user_id) { 
      $user_id = (int) $user_id;
      $query = $this->db->query("
            SELECT count(m.message_id) as unread_count
    FROM message_recipient mr
    INNER JOIN message m 
    	ON m.message_id = mr.message_id
    INNER JOIN
    	message_thread_participant mtp on mtp.thread_id = m.thread_id
    WHERE is_new = 1
    AND recipient_user_id = $user_id
    AND participant_user_id = $user_id
    AND mr.is_deleted = 0
		AND mtp.is_deleted = 0    
    AND is_spam = 0");
        
    return $query->row()->unread_count;    
      
  } 
    
}
