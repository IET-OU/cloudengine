<?php

/**
 * Controller for functionality related to comments on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Comment
 */

class Message extends MY_Controller {

	function Message ()
	{
		parent::MY_Controller();
		$this->load->model('message_model');
    $this->load->model('user_model');
		$this->load->library('layout', 'layout_main'); 	
	}

  /**
   * Show thread list
   *
   * @param
   */
  function index() {
            
      //initialise
      $data['user_id']          = $this->db_session->userdata('id');
      if (!intval($data['user_id'])) {
        redirect(base_url());
      }
      
      //form processing
      if ($this->input->post('submit')) {
        
        if ($this->input->post('delete_thread')) {
          $this->flag_thread($this->input->post('delete_thread'),'set_deleted'); 
          $data['message_display_content'] = 'Message successfully deleted';
          $data['message_display_type'] = 'success';          
        }
        
        elseif ($this->input->post('location')) {
          redirect($this->input->post('location'));  
        }
        
        elseif ($this->input->post('thread-action')) {    
          
          $action = $this->input->post('thread-action');                  
          
          switch ($action) {               
          
            case 'set_read':
            case 'set_unread':
            case 'set_deleted':
              
              if (isset($_POST['thread_id'])) {
                //set confirmation messages
                switch ($action) {  
                  case 'set_read':
                    $data['message_display_content']  = 'Conversations set to read';
                    $data['message_display_type']     = 'success';
                    break; 
                  case 'set_unread':
                    $data['message_display_content']  = 'Conversations set to unread';
                    $data['message_display_type']     = 'success';    
                    break;               
                  case 'set_deleted':
                    $data['message_display_content']  = 'Conversations deleted';
                    $data['message_display_type']     = 'success';
                    break;                   
                }

                $thread_id=$_POST['thread_id'];
                foreach ($thread_id as $id)
                {
                  $this->flag_thread($id,$action);
                }
              }    
              else {
                    $data['message_display_content']  = 'No conversations selected';
                    $data['message_display_type']     = 'error';
                    break;                  
              }          
              break;          
            default:
              break;                  
          }    
        }     
      }
           
      //data
      $data['title']            = 'Messages - Inbox';
      $data['navigation']       = 'message_inbox';      
      $data['threads']    = $this->message_model->get_user_threads($data['user_id']);
      for($i=0; $i < count($data['threads']); $i++) {
        $data['threads'][$i]->picture                     = $this->user_model->get_picture($data['threads'][$i]->last_message_author_id);
        $data['threads'][$i]->last_message_author_name    = $this->user_model->get_user_full_name($data['threads'][$i]->last_message_author_id);
        $data['threads'][$i]->all_participants            = $this->message_model->get_thread_participants($data['threads'][$i]->thread_id); 
        $data['threads'][$i]->other_participants          = $this->message_model->get_thread_participants($data['threads'][$i]->thread_id,$data['user_id']);         
        $data['threads'][$i]->other_participant_count     = count($data['threads'][$i]->other_participants);       
        $data['threads'][$i]->content_preview = substr($data['threads'][$i]->content, 0 , 39);
        if (strlen($data['threads'][$i]->content) > 39) {
          $data['threads'][$i]->content_preview .= '...';
        }       
      }
      
      $user_message_count   = $this->message_model->get_user_new_message_count($data['user_id']);
      $this->db_session->set_userdata('user_message_count', $user_message_count);
      
      if ($this->db_session->flashdata('message_display_content')) {
        $data['message_display_content']  = $this->db_session->flashdata('message_display_content');
        $data['message_display_type']     = $this->db_session->flashdata('message_display_type');
      }
      
      //output
      $this->layout->view('message/list', $data);
  }


  /**
   * Show thread for a user
   *
   * @param integer $thread_id The ID of the thread
   */
  function thread($thread_id = 0) {

      //initialise      
      $data['user_id']                  = $this->db_session->userdata('id');
      if (!intval($data['user_id'])) {
        redirect(base_url());
      }      
            
      if (!intval($thread_id)) {
        $this->db_session->set_flashdata('message_display_content', 'Conversation does not exist.');    
        $this->db_session->set_flashdata('message_display_type', 'error');   
        redirect('message');
      }
      
      //form handling
       if ($this->input->post('location')) {
          redirect($this->input->post('location'));  
       }
      
      elseif ($this->input->post('submit')) {
        
        if ($this->input->post('thread-action')) {
          $action = $this->input->post('thread-action'); 
          $this->flag_thread($thread_id,$action);
          //set confirmation messages
          switch ($action) {  
            case 'set_unread':
               $this->db_session->set_flashdata('message_display_content', 'Conversation set to unread');    
               $this->db_session->set_flashdata('message_display_type', 'success');    
              break;               
            case 'set_deleted':
              $this->db_session->set_flashdata('message_display_content', 'Conversation deleted');    
              $this->db_session->set_flashdata('message_display_type', 'success');    
              break;                   
          }          
       
          redirect('message');  
        }
        
        else {          
          $this->load->library('form_validation');
          $this->form_validation->set_rules('content', t("Reply"), 'required');
          if ($this->form_validation->run()) {   
            $message->content   = $this->input->post('content');
            $message->thread_id = $thread_id;
            $this->add_message_to_thread($message);
            $data['message_display_content'] = 'Message sent successfully';
            $data['message_display_type'] = 'success';              
          }
          
        }
      }
      
      
      //data
      $data['title']                    = 'Messages - Conversation';
      $data['navigation']               = 'message_thread';
      $data['thread_id']                = $thread_id;
      $data['thread']                   = $this->message_model->get_user_thread($data['user_id'], $thread_id);     
      
      if (!$data['thread']) {
          $this->db_session->set_flashdata('message_display_content', 'Conversation does not exist.');    
          $this->db_session->set_flashdata('message_display_type', 'error');   
          redirect('message');
      }
      
      else {    
        $data['participants']             = $this->message_model->get_thread_participants($thread_id,$data['user_id']);
        for($i=0; $i < count($data['thread']); $i++) {
          $this->flag_message($data['thread'][$i]->message_id, 'set_read');
          $data['thread'][$i]->author_name     = $this->user_model->get_user_full_name($data['thread'][$i]->author_user_id);
          $data['thread'][$i]->picture         = $this->user_model->get_picture($data['thread'][$i]->author_user_id);          
          if (!array_search($data['thread'][$i]->author_user_id,$data['participants'])
              && $data['thread'][$i]->author_user_id != $data['user_id']) {
          }    
        }      
  
        $user_message_count   = $this->message_model->get_user_new_message_count($data['user_id']);
        $this->db_session->set_userdata('user_message_count', $user_message_count);
  
        if ($this->db_session->flashdata('message_display_content')) {
          $data['message_display_content']  = $this->db_session->flashdata('message_display_content');
          $data['message_display_type']     = $this->db_session->flashdata('message_display_type');
        }
  
        //output
        $this->layout->view('message/thread', $data);
      }
  }

  /**
   * Send a message 
   *
   * 
   */
  function compose($recipient_id = 0) {

      //initialise
      $user_id                      = $this->db_session->userdata('id');
      if (!intval($user_id)) {
        redirect(base_url());
      }        
      
      $data['valid_recipients']     = array();
      $data['recipients']           = '';
      $data['subject']              = '';
      $data['content']              = '';
      $data['title']                = 'Messages - Compose';
      $data['navigation']           = 'message_compose';
      
      //if the user has clicked 'Send message' from another user's profile, get recipient's user name
      if ($recipient_id) {
        $recipient = $this->user_model->get_user($recipient_id);
        $data['valid_recipients'][] = $recipient->user_name .', ';
      }
      
      //form handling
      $this->load->library('form_validation');
      $this->form_validation->set_rules('recipients', t("To"), 'required');
      $this->form_validation->set_rules('subject', t("Subject"), 'required');
      $this->form_validation->set_rules('content', t("Message"), 'required');
      
      if ($this->input->post('cancel')) {
        redirect('message');
      }
      elseif ($this->input->post('location')) {
          redirect($this->input->post('location'));  
       }
      elseif ($this->input->post('submit')) {
        
        $data['subject']              = $this->input->post('subject');
        $data['content']              = $this->input->post('content');
      
        //remove white space from recipient list
        $recipients                      = preg_replace( '/\s*/m', '', $this->input->post('recipients'));
        //remove trailing comma from recipient list if it has one
        if (substr($recipients, -1) == ',') {
          $recipients      = substr($recipients, 0, strlen($recipients) - 1 );
        }          
        
        $thread->participant_usernames = explode(',',$recipients);
        foreach ($thread->participant_usernames as $username) {
          $username = (trim($username));
          $user = $this->auth_model->get_user_by_username($username);
          
          if (!$user) {
            $invalid_recipients[]         = $username;
          }
          else
          {
            $data['valid_recipients'][]   = $username;
            $thread->participants[]       = $user->id;
          }            
        }      
      
        if ($invalid_recipients and strlen($recipients)) { 
          $data['message_display_content']  = 'Invalid usernames: ' .implode(', ', $invalid_recipients);
          $data['message_display_type']     = 'error';            
        }
        else {      
          
          //create the thread reply       
          if ($this->form_validation->run()) {
  
            $thread->subject   = $this->input->post('subject');
          
            $thread->participants[]     = $user_id;
            $thread->participants       = array_unique($thread->participants);
                        
            $message->thread_id         = $this->create_thread($thread);       
            $message->content           = $this->input->post('content');
            
            $this->add_message_to_thread($message);
            
            $this->db_session->set_flashdata('message_display_content', 'Message sent successfully.');    
            $this->db_session->set_flashdata('message_display_type', 'success');              
            
            redirect('/message/thread/'.$message->thread_id);
          }
        }
      }
     
      //output
      $this->layout->view('message/compose', $data);
  
  } 

  /**
   * Create a message and thread
   *
   * 
   */
  function create_thread($thread) { 
    
      //******** initialise ********
      $user_id            = $this->db_session->userdata('id');
    
      
      //********  data    **********
        $thread->author_user_id = $user_id;
        $thread->author         = $this->user_model->get_user($user_id);
        $thread->thread_id      = $this->message_model->create_thread($thread);        
        
        foreach ($thread->participants as $participant) {
          
          //create thread for each participant
          $thread_participant->thread_id              = $thread->thread_id;
          $thread_participant->participant_user_id    = $participant;  
          $this->message_model->create_thread_participant($thread_participant);
          
          //send email to each participant except the user
          if ($participant != $user_id) {              
            $email->subject     = 'New message on '.$this->config->item('site_name');     
            $email->recipient   = $this->user_model->get_user($participant); 
            $email->to          = $this->config->item('x_live') ? $email->recipient->email : 
                                             $this->config->item('site_email');
            $email->thread      = $thread;
            $email->Content     = $this->load->view('email/message_new_notification', $email, true);   
            send_email($email->to, $this->config->item('site_email'), $email->subject, $email->content); 
          }
          
        }     

      //********  output  **********  
      return $thread->thread_id;
  } 

  /**
   * Adds a sent a message - to an existing thread
   *
   * 
   */
  function add_message_to_thread($message) {
     
      //initialise
        $user_id      = $this->db_session->userdata('id');      
      
      //data 
      
        $message->recipients      = $this->message_model->get_thread_participants($message->thread_id);    
        $message->author_user_id  = $user_id; 
        $message->author          = $this->user_model->get_user($user_id);        
        $message->message_id      = $this->message_model->create_message($message);
        $message->thread_subject  = $this->message_model->get_thread($message->thread_id)->subject;         

        //message recipients
        foreach ($message->recipients as $message_recipient) {
          
          $message_recipient_data->message_id             = $message->message_id;
          $message_recipient_data->recipient_user_id      = $message_recipient->user_id;  
          $this->message_model->create_message_recipient($message_recipient_data);   
          
          //send email to each participant except the user
          if ($message_recipient->user_id != $user_id) {     
            $email->subject     = 'New reply on '.$this->config->item('site_name');     
            $email->recipient   = $this->user_model->get_user($message_recipient->user_id); 
            $email->to          = $this->config->item('x_live') ? $email->recipient->email : 
                                             $this->config->item('site_email');
            $email->message     = $message;
            $email->content     = $this->load->view('email/message_reply_notification', $email, true); 
              
            send_email($email->to, $this->config->item('site_email'), $email->subject, $email->content);
          }          
                 
        }  
      
      //output
      //$this->layout->view('message/thread', $data);
      return $message;
  } 
  
  /**
   * Flag a message 
   *  - read
   *  - unread
   *  - deleted
   *  - spam
   *
   * @param integer   $message_id
   * @param string    $action
   */
  function flag_message($message_id = 0, $action = NULL) {

      //initialise
        $recipient_user_id  = $this->db_session->userdata('id');
        
        if (!intval($message_id)) {
          //TODO: set warning message
          redirect('message');
        }  
        if (!$action) {
          //TODO: set warning message
          redirect('message');
        }      
        
      //data
        switch ($action) {
          case 'set_unread':
            $action_field = 'is_new';
            $action_value = 1;    
            break;      
          case 'set_read':
            $action_field = 'is_new';
            $action_value = 0;      
            break;    
          case 'set_spam':
            $action_field = 'is_spam';
            $action_value = 1;
            break;
          case 'unset_spam':
            $action_field = 'is_spam';
            $action_value = 0;
            break;
          case 'set_deleted':
            $action_field = 'is_deleted';
            $action_value = 1;
            break;
          case 'unset_deleted':
            $action_field = 'is_deleted';
            $action_value = 0;        
            break;          
          default:
            break;                  
        }
        
        if (isset($action_field)) {
          $this->message_model->flag_message($message_id,$recipient_user_id,$action_field,$action_value);      
        }
      //process
    
      //output  
      return;
  }   
 
  /**
   * Flag a thread
   *  - deleted
   *  - archived
   * 
   * @param integer   $thread_id
   * @param string    $action
   */
  function flag_thread($thread_id, $action) {

      //initialise
        $participant_user_id  = $this->db_session->userdata('id');
        if (!intval($thread_id)) {
          //TODO: set warning message
          redirect('message');
        }  
        if (!$action) {
          //TODO: set warning message
          redirect('message');
        }      
        
      //data
        switch ($action) {   
          case 'set_archived':
            $action_field = 'is_archived';
            $action_value = 1;
            break;
          case 'unset_archived':
            $action_field = 'is_archived';
            $action_value = 0;
            break;
          case 'set_deleted':
            $action_field = 'is_deleted';
            $action_value = 1;
            break;
          case 'unset_deleted':
            $action_field = 'is_deleted';
            $action_value = 0;        
            break;     
          case 'set_read':
            $message_ids  = $this->message_model->get_thread_message_ids($thread_id,$participant_user_id);   
            foreach($message_ids as $message_id) {
              $this->flag_message($message_id,'set_read');  
            }    
            return;   
          case 'set_unread':
            $message_id   = $this->message_model->get_most_recent_thread_message_id($thread_id,$participant_user_id);   
            $this->flag_message($message_id,'set_unread');     
            break;                  
          default:
            break;                  
        }
        
        if (isset($action_field)) {
          $this->message_model->flag_thread($thread_id,$participant_user_id,$action_field,$action_value);      
        }
      //process
    
      //output  
      //redirect('message');
        
  }    
  
  
  function get_message_recipients() {
    
      $this->load->model('user_model');
      $this->load->library('JSON');
  
      $term = $_GET['term'];
      
      $recipients = $this->message_model->get_recipients($term);
      foreach ($recipients as $recipient) {
        $temp[] = array('value' => $recipient['user_name'], 'label' => $recipient['fullname']);
      }
      $results_json = $this->json->encode($temp);
      
      print $results_json;
        
    }   
    
}
