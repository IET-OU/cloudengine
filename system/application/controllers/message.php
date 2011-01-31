<?php
/**
 * Controller for functionality related to the message system
 * 
 * NOTE: the term 'thread' is synonomous with the term 'conversation'
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Comment
 */

class Message extends MY_Controller {

	function Message ()
	{
		parent::MY_Controller();

		// Switch the API on or off.
    if (!$this->config->item('x_message')) {
        show_404('/message');
    }

		$this->load->model('message_model');
    $this->load->model('user_model');
		$this->load->library('layout', 'layout_main'); 	
    $this->load->helper('format');    
	}

  /**
   * Show thread list (inbox)
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
        $this->load->library('form_validation');
      
        if ($this->input->post('submit')) {
                    
          //delete single thread from inbox view
          if ($this->input->post('delete_thread')) {
            $thread_id = $this->form_validation->ie7_button_fix($this->input->post('delete_thread'));  
            $this->flag_thread($thread_id,'set_deleted'); 
            $data['message_display_content'] = t("Message successfully deleted");
            $data['message_display_type'] = 'success';          
          }
          
          //redirect
          elseif ($this->input->post('location')) {
            $location = $this->form_validation->ie7_button_fix($this->input->post('location'));
            redirect($location);  
          }
          
          //actions for multiple select checkboxes
          elseif ($this->input->post('thread-action')) {    
            
            $action = $this->form_validation->ie7_button_fix($this->input->post('thread-action'));  
                 
            switch ($action) {               
            
              case 'set_read':
              case 'set_unread':
              case 'set_deleted':
                
                if (isset($_POST['thread_id'])) {
                  //set confirmation messages
                  switch ($action) {  
                    case 'set_read':
                      $data['message_display_content']  = t("Conversations set to read");
                      $data['message_display_type']     = 'success';
                      break; 
                    case 'set_unread':
                      $data['message_display_content']  = t("Conversations set to unread");
                      $data['message_display_type']     = 'success';    
                      break;               
                    case 'set_deleted':
                      $data['message_display_content']  = t("Conversations deleted");
                      $data['message_display_type']     = 'success';
                      break;                   
                  }
  
                  $thread_id=$_POST['thread_id'];
                  //flag each thread with appropriate action
                  foreach ($thread_id as $id)
                  {
                    $this->flag_thread($id,$action);
                  }
                }    
                //no conversation selected by user
                else {
                      $data['message_display_content']  = t("No conversations selected");
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
        $data['title']            = t("Messages - Inbox");
        $data['navigation']       = 'message_inbox';      
        //get all conversations (threads) for the user
        $data['threads']          = $this->message_model->get_user_threads($data['user_id']);
        //loop through conversations
        for($i=0; $i < count($data['threads']); $i++) {
          $data['threads'][$i]->picture                     = $this->user_model->get_picture($data['threads'][$i]->last_message_author_id);
          $data['threads'][$i]->last_message_author_name    = $this->user_model->get_user_full_name($data['threads'][$i]->last_message_author_id);
          $data['threads'][$i]->all_participants            = $this->message_model->get_thread_participants($data['threads'][$i]->thread_id); 
          $data['threads'][$i]->other_participants          = $this->message_model->get_thread_participants($data['threads'][$i]->thread_id,$data['user_id']);         
          $data['threads'][$i]->other_participant_count     = count($data['threads'][$i]->other_participants);       
          //truncate the message for preview
          $data['threads'][$i]->content_preview = truncate_content($data['threads'][$i]->content,40);                        
        }
      
        //check user count of new messages in case any actions have been applied to messages (e.g. 'mark read')
        $user_message_count   = $this->message_model->get_user_new_message_count($data['user_id']);
        $this->db_session->set_userdata('user_message_count', $user_message_count);
        
        //set any flash data from previous request in to current message display variable
        if ($this->db_session->flashdata('message_display_content')) {
          $data['message_display_content']  = $this->db_session->flashdata('message_display_content');
          $data['message_display_type']     = $this->db_session->flashdata('message_display_type');
        }
      
      //output
       $this->layout->view('message/list', $data);
  }


  /**
   * Show a conversation (thread) for a user
   *
   * @param integer $thread_id The ID of the thread
   */
  function thread($thread_id = 0) {

      //initialise      
        $data['user_id']                  = $this->db_session->userdata('id');
        if (!intval($data['user_id'])) {
          redirect(base_url());
        }      
      
        //if no thread_id, set an error message and redirect to inbox      
        if (!intval($thread_id)) {
          $this->db_session->set_flashdata('message_display_content', 'Conversation does not exist.');    
          $this->db_session->set_flashdata('message_display_type', 'error');   
          redirect('message');
        }
      
      //form processing      
        $this->load->library('form_validation');
        
        //redirect if appropriate
        if ($this->input->post('location')) {
          $location = $this->form_validation->ie7_button_fix($this->input->post('location'));          
          redirect($location);  
        }
        
        elseif ($this->input->post('submit')) {
        
          //action had been requested on a thread  
          if ($this->input->post('thread-action')) {
            $action = $this->form_validation->ie7_button_fix($this->input->post('thread-action'));  
            //set approriate flag on the thread 
            $this->flag_thread($thread_id,$action);
            //set confirmation messages
            switch ($action) {  
              case 'set_unread':
                 $this->db_session->set_flashdata('message_display_content', t("Conversation set to unread"));    
                 $this->db_session->set_flashdata('message_display_type', 'success');    
                break;               
              case 'set_deleted':
                $this->db_session->set_flashdata('message_display_content', t("Conversation deleted"));    
                $this->db_session->set_flashdata('message_display_type', 'success');    
                break;                   
            }          
            redirect('message');  
          }
          
          //thread reply
          else {          
            $this->form_validation->set_rules('content', t("Reply"), 'required');
            if ($this->form_validation->run()) {   
              $message->content   = $this->input->post('content');
              $message->thread_id = $thread_id;
              //add the message to the conversation
              $this->add_message_to_thread($message);
              $data['message_display_content'] = t("Message sent successfully");
              $data['message_display_type'] = 'success';              
            }
            
          }
        }
      
      
      //data
        $data['title']                    = t("Messages - Conversation");
        $data['navigation']               = 'message_thread';
        $data['thread_id']                = $thread_id;
        //get the details of the user's copy of this conversation
        $data['thread']                   = $this->message_model->get_user_thread($data['user_id'], $thread_id);     
      
        //conversation doesn't exist
        if (!$data['thread']) {
            $this->db_session->set_flashdata('message_display_content', t("Conversation does not exist."));    
            $this->db_session->set_flashdata('message_display_type', 'error');   
            redirect('message');
        }
      
        //process conversation
        else {    
          //get conversation participant details 
          $data['participants']             = $this->message_model->get_thread_participants($thread_id,$data['user_id']);
          //loop through the messages in the conversation
          for($i=0; $i < count($data['thread']); $i++) {
            //mark conversation as read
            $this->flag_message($data['thread'][$i]->message_id, 'set_read');
            //get message author details
            $data['thread'][$i]->author_name     = $this->user_model->get_user_full_name($data['thread'][$i]->author_user_id);
            $data['thread'][$i]->picture         = $this->user_model->get_picture($data['thread'][$i]->author_user_id);            
          }      
    
          //check user count of new messages
          $user_message_count   = $this->message_model->get_user_new_message_count($data['user_id']);
          $this->db_session->set_userdata('user_message_count', $user_message_count);
  
          //set any flash data from previous request in to current message display variable  
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
   * @param integer $recipient_id The ID of the thread
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
        $data['title']                = t("Messages - Compose");
        $data['navigation']           = 'message_compose';
      
      //data
      
        //if the user has clicked 'Send message' from another user's profile a $recipient_id will be present
        //get recipient's user name to use in the 'To' form field
        if ($recipient_id) {
          $recipient = $this->user_model->get_user($recipient_id);
          $data['valid_recipients'][] = $recipient->user_name .', ';
        }
      
      //form processing
        $this->load->library('form_validation');
        $this->form_validation->set_rules('recipients', t("To"), 'required');
        $this->form_validation->set_rules('subject', t("Subject"), 'required');
        $this->form_validation->set_rules('content', t("Message"), 'required');

        //user clicked cancel, take to the inbox
        if ($this->input->post('cancel')) {
          redirect('message');
        }
        //redirect if appropriate    
        elseif ($this->input->post('location')) {
            $location = $this->form_validation->ie7_button_fix($this->input->post('location'));
            redirect($location);  
         }
        //process sending message
        elseif ($this->input->post('submit')) {
          
          $data['subject']              = $this->input->post('subject');
          $data['content']              = $this->input->post('content');
        
          
          
          //remove white space from recipient list
          //$recipients  = preg_replace( '/\s*/m', '', $this->input->post('recipients'));
          $recipients    = trim($this->input->post('recipients'));          

          //remove trailing comma from recipient list if it has one
          if (substr($recipients, -1) == ',') {
            $recipients      = substr($recipients, 0, strlen($recipients) - 1 );
          }                 
           
          //create array of usernames]
          $thread->participant_usernames = explode(',',$recipients);
          
          //loop through users
          foreach ($thread->participant_usernames as $username) {
                        
            $username = (trim($username));
            $user = $this->auth_model->get_user_by_username($username);
            
            //if user doesn't exist, add to array
            if (!$user) {
              $invalid_recipients[]           = $username;
            }
            //valid recipients
            else
            {
              $data['valid_recipients'][]     = $username;
              $thread->participants[]         = $user->id;
            }            
          }      
        
          //if we have invalid recipients, set an error message
          if ($invalid_recipients and strlen($recipients)) { 
            $data['message_display_content']  = t("Invalid usernames:") .implode(', ', $invalid_recipients);
            $data['message_display_type']     = 'error';            
          }
        
          //create the thread reply  
          else {
            if ($this->form_validation->run()) {
              $thread->subject   = $this->input->post('subject');
              //add user to participant list
              $thread->participants[]         = $user_id;
              //remove duplicate participant list
              $thread->participants           = array_unique($thread->participants);
              //create the new thread
              $message->thread_id             = $this->create_thread($thread);       
              $message->content               = $this->input->post('content');
              //add the message to the newly created thread
              $this->add_message_to_thread($message, true);
              //set flash data message and redirect user to the new thread
              $this->db_session->set_flashdata('message_display_content', t("Message sent successfully"));    
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
    
      //initialise
        $user_id                      = $this->db_session->userdata('id');
      
      //data
        $thread->author_user_id       = $user_id;
        $thread->author               = $this->user_model->get_user($user_id);
      
      //process
      
        //create the new thread
        $thread->thread_id            = $this->message_model->create_thread($thread);        
        
        //loop through the participants and create a copy of the thread for each participant
        foreach ($thread->participants as $participant) {
          
          //create thread for each participant
          $thread_participant->thread_id              = $thread->thread_id;
          $thread_participant->participant_user_id    = $participant;  
          $this->message_model->create_thread_participant($thread_participant);
          
          //send email to each participant except the user
          if ($participant != $user_id) {
            $participant_profile = $this->user_model->get_user($participant); 
            if ($participant_profile->email_message_notify) {
              $email->subject     = 'New message on '.$this->config->item('site_name');     
              $email->recipient   = $participant_profile; 
              $email->to          = $this->config->item('x_live') ? $email->recipient->email : 
                                               $this->config->item('site_email');
              $email->thread      = $thread;
              $email->content     = $this->load->view('email/message_new_notification', $email, true);   
              send_email($email->to, $this->config->item('site_email'), $email->subject, $email->content); 
            }
          }
        }     
        
      //output 
      return $thread->thread_id;
  } 

  /**
   * Adds a sent a message - to an existing thread
   *
   * 
   */
  function add_message_to_thread($message, $new_thread = FALSE) {
     
      //initialise
        $user_id      = $this->db_session->userdata('id');      
      
      //data 
        $message->recipients      = $this->message_model->get_thread_participants($message->thread_id);    
        $message->author_user_id  = $user_id; 
        $message->author          = $this->user_model->get_user($user_id);   
        //create the message     
        $message->message_id      = $this->message_model->create_message($message);
        $message->thread_subject  = $this->message_model->get_thread($message->thread_id)->subject;         

        //loop through message recipients
        foreach ($message->recipients as $message_recipient) {
          
          //create a copy of the message for each recipient
          $message_recipient_data->message_id             = $message->message_id;
          $message_recipient_data->recipient_user_id      = $message_recipient->user_id;  
          $this->message_model->create_message_recipient($message_recipient_data);        
          
          //if we are adding messages to an existing conversation then send the recipients and email
          if (!$new_thread) {
            
             //send email to each participant except the user
            if ($message_recipient->user_id != $user_id) {     
             
              $recipient_profile  = $this->user_model->get_user($message_recipient->user_id);        
             
              if ($recipient_profile->email_message_notify) {           
                $email->subject     = 'New reply on '.$this->config->item('site_name');     
                $email->recipient   = $recipient_profile; 
                $email->to          = $this->config->item('x_live') ? $email->recipient->email : 
                                                 $this->config->item('site_email');
                $email->message     = $message;
                $email->content     = $this->load->view('email/message_reply_notification', $email, true); 
                send_email($email->to, $this->config->item('site_email'), $email->subject, $email->content);
              }
            }    
                 
          }
                 
        }  
      
      //output
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
        
        //if no message or action supplied, redirect to inbox
        if (!intval($message_id)) {
          redirect('message');
        }  
        if (!$action) {
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
        
        //set flag against the message for the user
        if (isset($action_field)) {
          $this->message_model->flag_message($message_id,$recipient_user_id,$action_field,$action_value);      
        }
    
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
        
        //if no thread or action supplied, redirect to inbox        
        if (!intval($thread_id)) {
          redirect('message');
        }  
        if (!$action) {
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


  /**
   * Get message recipients
   *  - this is used for the jQuery/Ajax call to get the list of recipients 
   *  - for the dropdown in the 'To' field 
   * 
   * @param string    term 
   */  
  function get_message_recipients() {
    
      $this->load->model('user_model');
      $this->load->library('JSON');
  
      //passed from the jQuery call and will do a search against user full name
      $term = $this->input->get('term');
      
      //get recipient names that contain the search term
      $recipients = $this->message_model->get_recipients($term);
      //put terms in to array to JSON encode
      foreach ($recipients as $recipient) {
        $temp[] = array('value' => $recipient['user_name'], 'label' => $recipient['fullname']);
      }
      //JSON encode results
      $results_json = $this->json->encode($temp);
      //return results
      print $results_json;

    }   

}
