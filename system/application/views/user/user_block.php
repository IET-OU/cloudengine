<?php 
    if ($this->auth_lib->is_logged_in()) {
        $user_id = $this->db_session->userdata('id');
         $data['loggedinprofile'] = $this->auth_lib->get_profile($user_id);
        $this->load->view('auth/logged_in_block', $data); 
    } else { 
        $this->load->view('auth/login_block'); 
    } 
?>