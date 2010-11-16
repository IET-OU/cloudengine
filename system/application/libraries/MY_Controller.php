<?php

class  MY_Controller  extends  Controller  {

  function MY_Controller ()  {
    parent::Controller();
      
      //get message unread count for the user, this is called on nearly every page and is used to
      //update the message count in the primary navigation for a user 
      //(most controllers extend MY_Controller)
      if ($this->db_session) { 
        if (is_numeric($this->db_session->userdata('id'))) {
          $this->load->model('message_model');
          $user_message_count = $this->message_model->get_user_new_message_count($this->db_session->userdata('id'));
          $this->db_session->set_userdata('user_message_count', $user_message_count);
        }
      }
                    
  }
} 