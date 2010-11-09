<?php

class  MY_Controller  extends  Controller  {

  function MY_Controller ()  {
    parent::Controller();
      
      //get message unread count for the user
      if ($this->db_session) {
        $this->load->model('message_model');
        $user_message_count = $this->message_model->get_user_new_message_count($this->db_session->userdata('id'));
        $this->db_session->set_userdata('user_message_count', $user_message_count);
      }
                    
  }
} 