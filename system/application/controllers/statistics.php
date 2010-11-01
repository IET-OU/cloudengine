<?php
/**
 * Controller for functionality related to site statistics 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Statistics
 */
class Statistics extends Controller {

	function Statistics() {
		parent::Controller();
        $this->load->model('statistics_model');		
		$this->load->library('layout', 'layout_main'); 
		$this->load->model('cloud_model');
		$this->load->model('comment_model');
		$this->load->model('cloudscape_model');
		$this->load->model('blog_model');
		$this->load->model('content_model');
		$this->load->model('event_model');
		$this->load->model('tag_model');
		$this->load->model('link_model');
		$this->load->model('embed_model');
		
        $this->auth_lib->check_is_admin(); 		
	}

	/**
	 * Display the site statistics
	 *
	 */
    function stats() {
        $data['user_total']            = $this->statistics_model->get_total_users();  
        $data['cloud_total']           = $this->statistics_model->get_total_clouds();
        $data['cloudscape_total']      = $this->statistics_model->get_total_cloudscapes();
        $data['tag_total']             = $this->statistics_model->get_total_tags();     
        $data['comment_total']         = $this->statistics_model->get_total_comments();
        $data['link_total']            = $this->statistics_model->get_total_links();
        $data['content_total']         = $this->statistics_model->get_total_content();
        $data['embed_total']           = $this->statistics_model->get_total_embeds();
         
        $data['cloud_team_total']      = $this->statistics_model->get_total_clouds(true); 
        $data['cloudscape_team_total'] = $this->statistics_model->get_total_cloudscapes(true);        
        $data['tag_team_total']        = $this->statistics_model->get_total_tags(true);         
        $data['comment_team_total']    = $this->statistics_model->get_total_comments(true);                
        $data['link_team_total']       = $this->statistics_model->get_total_links(true);
        $data['content_team_total']    = $this->statistics_model->get_total_content(true); 
        $data['embed_team_total']      = $this->statistics_model->get_total_embeds(true);        

        $data['title'] = 'Statistics';
	    $this->layout->view('statistics/stats', $data);	    
	}

	/**
	 * Display site statistics between given dates 
	 *
	 */
	function stats_dates() {
        $data['title'] = 'Statistics';
	    if ($this->input->post('submit')) {
	         $start = $this->input->post('start');
	         $end   = $this->input->post('end');
	         
	         $start_timestamp = strtotime($start);
	         $end_timestamp   = strtotime($end);
	         
             $data['user_total'] = $this->statistics_model->get_total_users($start_timestamp, 
                                                                            $end_timestamp);
             $data['active_total'] = 
             	$this->statistics_model->get_active_users($start_timestamp, $end_timestamp);
	         $data['cloud_total']  = $this->statistics_model->get_total_clouds(false, 
	                                                        $start_timestamp, $end_timestamp);
             $data['cloudscape_total'] = $this->statistics_model->get_total_cloudscapes(false,
                                                            $start_timestamp, $end_timestamp);
             $data['comment_total']    = $this->statistics_model->get_total_comments(false, 
                                                            $start_timestamp, $end_timestamp);
             $data['link_total']       = $this->statistics_model->get_total_links(false, 
                                                            $start_timestamp, $end_timestamp);
             $data['content_total']    = $this->statistics_model->get_total_content(false, 
                                                            $start_timestamp, $end_timestamp);
             $data['embed_total']      = $this->statistics_model->get_total_embeds(false, 
                                                            $start_timestamp, $end_timestamp);
             $data['cloud_team_total']      = $this->statistics_model->get_total_clouds(true, 
                                                            $start_timestamp, $end_timestamp);
             $data['cloudscape_team_total'] = 
             	$this->statistics_model->get_total_cloudscapes(true, $start_timestamp, 
             	                                               $end_timestamp);
             $data['comment_team_total'] = $this->statistics_model->get_total_comments(true, 
                                                            $start_timestamp, $end_timestamp);
             $data['link_team_total'] = $this->statistics_model->get_total_links(true, 
                                                            $start_timestamp, $end_timestamp);
             $data['content_team_total']    = $this->statistics_model->get_total_content(true,                                                            $start_timestamp, $end_timestamp);
             $data['embed_team_total']      = $this->statistics_model->get_total_embeds(true, 
                                                            $start_timestamp, $end_timestamp);                
	         $data['startdate'] = $start;
	         $data['enddate'] = $end;
	         $this->layout->view('statistics/stats_dates_results', $data); 
	    } else {
	         $this->layout->view('statistics/stats_dates_form', $data);
	    }
	}

    /**
     * Show stats for a specified cloudscape
     * 
     */
    function cloudscape() {
        $data['title'] = 'Cloudscape Statistics';
	    if ($this->input->post('submit')) {
	        $cloudscape_id = $this->input->post('cloudscape_id');
	        $data['cloudscape'] = 
	        	$this->statistics_model->get_cloudscape_stats($cloudscape_id);
	        $this->layout->view('statistics/cloudscape_stats.php', $data);
	        
	    } else {
            // Get all cloudscapes
            $cloudscapes = $this->cloudscape_model->get_cloudscapes(); 
            // Put the cloudscapes into a suitable form for a select
            foreach ($cloudscapes as $cloudscape) {
                $options[$cloudscape->cloudscape_id] = $cloudscape->title;
            }
            
            $data['cloudscapes'] = $options;
            $this->layout->view('statistics/cloudscape_form.php', $data); 
	    }
    }	
	
    /**
     * Show stats for a specified cloudscape between specified dates 
     *
     * @param integer $cloudscape_id The ID of the cloudscape
     */
    function cloudscape_date() {
        $data['title'] = 'Cloudscape Statistics';
	    
        if ($this->input->post('submit')) {
	        $cloudscape_id = $this->input->post('cloudscape_id');
            
            $start_date_str = trim($this->input->post('start_date'));            
            $end_date_str   = trim($this->input->post('end_date'));  
            $starttime       = strtotime($start_date_str);
            $endtime         = strtotime($end_date_str);
            
            $data['visitors_logged_in'] =  
                $this->statistics_model->get_cloudscape_cloud_visitors_logged_in(
                                                        $cloudscape_id, $starttime, $endtime);
            $data['visitors_guest'] =  
                $this->statistics_model->get_cloudscape_cloud_visitors_guest($cloudscape_id, 
                                                                        $starttime, $endtime);	   
	        $data['cloudscape'] = $this->statistics_model->get_cloudscape_stats(
	                                                                          $cloudscape_id);
	        $data['starttime']  = $starttime;
	        $data['endtime']    = $endtime; 
	        
	        $this->layout->view('statistics/cloudscape_stats_dates.php', $data);
	    } else {
            // Get all cloudscapes
            $cloudscapes = $this->cloudscape_model->get_cloudscapes(); 
            // Put the cloudscapes into a suitable form for a select          
            foreach ($cloudscapes as $cloudscape) {
                $options[$cloudscape->cloudscape_id] = $cloudscape->title;
            }
            
            $data['cloudscapes'] = $options;
            $this->layout->view('statistics/cloudscape_form_dates.php', $data); 
	    }
    }
}