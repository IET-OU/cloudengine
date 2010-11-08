<?php

/**
 * Controller for functionality related to comments on clouds
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Comment
 */

class Message extends Controller {

	function Message ()
	{
		parent::Controller();
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
                $thread_id=$_POST['thread_id'];
                foreach ($thread_id as $id)
                {
                  $this->flag_thread($id,$action);
                }
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
        if (strlen($data['threads'][$i]->content) > 40) {
          $data['threads'][$i]->content_preview = substr($data['threads'][$i]->content, 0 , 40) .'...';
        }       
        else {
          $data['threads'][$i]->content_preview = substr($data['threads'][$i]->content, 0 , 40);
        }                
      }
            
      //process
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
      
      if (!intval($thread_id)) {
        //TODO: set warning message
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
          redirect('message');  
        }
        
        else {          
          $this->load->library('form_validation');
          $this->form_validation->set_rules('content', t("Content"), 'required');
          if ($this->form_validation->run()) {   
            $message->content   = $this->input->post('content');
            $message->thread_id = $thread_id;
            $this->add_message_to_thread($message);
          }
          
        }
      }
      
      
      //data
      $data['title']                    = 'Messages - Conversation';
      $data['navigation']               = 'message_thread';
      $data['thread_id']                = $thread_id;
      $data['thread']                   = $this->message_model->get_thread($data['user_id'], $thread_id);    
      $data['participants']             = $this->message_model->get_thread_participants($thread_id,$data['user_id']);
      for($i=0; $i < count($data['thread']); $i++) {
        $this->flag_message($data['thread'][$i]->message_id, 'set_read');
        $data['thread'][$i]->author_name     = $this->user_model->get_user_full_name($data['thread'][$i]->author_user_id);
        $data['thread'][$i]->picture         = $this->user_model->get_picture($data['thread'][$i]->author_user_id);          
        if (!array_search($data['thread'][$i]->author_user_id,$data['participants'])
            && $data['thread'][$i]->author_user_id != $data['user_id']) {
          //$data['participants'][$i]->user_id    = $data['thread'][$i]->author_user_id;
          //$data['participants'][$i]->name       = $data['thread'][$i]->author_name;
        }    
      }      
      //process
      //output
      $this->layout->view('message/thread', $data);
  }

  /**
   * Send a message 
   *
   * 
   */
  function compose() {

      //initialise
      $user_id            = $this->db_session->userdata('id');
     
      //data
      $data['title']            = 'Messages - Compose';
      $data['navigation']       = 'message_compose';      
      //ajax recipients

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
        
        
      
        //create the thread reply       
        if ($this->form_validation->run()) {
          //var_dump($_POST);exit;
          $thread->subject                 = $this->input->post('subject');
          $thread->participant_usernames   = explode(",",$this->input->post('recipients'));
          array_pop($thread->participant_usernames);
          foreach ($thread->participant_usernames as $username) {
            $user = $this->auth_model->get_user_by_username(trim($username));
            $thread->participants[]   = $user->id;
          }
          $thread->participants[]     = $user_id;
          $thread->participants       = array_unique($thread->participants);
           
          $message->thread_id         = $this->create_thread($thread);       
          $message->content           = $this->input->post('content');
          
          $this->add_message_to_thread($message);
          
          redirect('/message/thread/'.$message->thread_id);
        }
      }
     
      //process

    
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
        $thread->thread_id              = $this->message_model->create_thread($thread);        
        
        foreach ($thread->participants as $participant) {
          $thread_participant->thread_id             = $thread->thread_id;
          $thread_participant->participant_user_id    = $participant;  
          $this->message_model->create_thread_participant($thread_participant);
        }     
        
      //********  process **********  
      
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
        $message->message_id      = $this->message_model->create_message($message);

        //message recipients
        foreach ($message->recipients as $message_recipient) {
          $message_recipient_data->message_id             = $message->message_id;
          $message_recipient_data->recipient_user_id      = $message_recipient->user_id;  
          $this->message_model->create_message_recipient($message_recipient_data);
        }  
             
      //process
      
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

     // var_dump($action);exit;

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
      redirect('message');
        
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
      
      /*$video_activity_text  = $this->input->post('video_activity_text');
      $video_id             = $this->input->post('video_id'); 
      $user_id              = $this->input->post('user_id');
      
      // if there is already text for this video and user then update the entry
      if ($this->user_video_text_model->get_item($user_id,$video_id)) {
        $result_text = $this->user_video_text_model->edit($user_id, $video_id, $video_activity_text);
      } 
      // if there is no text for this video and user then insert the entry
      else {
        $result_text = $this->user_video_text_model->add($user_id, $video_id, $video_activity_text);
      }*/
      
      //format the results as JSON and return to the 'AJAX_record.php' page       
      //$results_json_encode = $this->json->encode(array(array('value' => 'richlove','label' => 'Richard Lovelock'),array('value' => 'bacon','label' => 'Richard Bacon')));
      //$results_json = '[' .$results_json_encode .']';
      /*$results_json = '[
			{
				"value": "juliette_culver",
				"label": "Juliette Culver"
			},
			{
				"value": "richlove",
				"label": "Richard Lovelock"
			},
			{
				"value": "richlove2",
				"label": "Rich Lovelock"
			},
			{
				"value": "grainne_conole",
				"label": "Grainne Conole"
			},
			{
				"value": "tony_hirst",
				"label": "Tony Hirst"
			}     
		]';*/
      
      //var_dump($results_json);exit;
      //print $results_json_encode;
      //print $_GET['term'];
      //$ajax_result  = array('ajax_result' => $results_json); 
      //$this->load->view('AJAX_record',$ajax_result);        
        
    }   
    
}
