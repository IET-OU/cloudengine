<?php 
    if ($this->auth_lib->is_logged_in()) {
            $user_id = $this->db_session->userdata('id');
            $loggedinprofile = $this->profile_model->get_user($user_id);
           
    } 
?>