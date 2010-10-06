<?php 
    if ($this->auth_lib->is_logged_in()) {
        $user_id = $this->db_session->userdata('id');
        $this->CI = & get_instance();
        $this->CI->load->model('user_model'); 
        $data['loggedinprofile'] = $this->CI->user_model->get_user($user_id);
        $this->load->view('auth/logged_in_block', $data); 
    } else { 
        $this->load->view('auth/login_block'); 
    } 
?>