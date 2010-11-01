<?php 

/**
 * Controller for functionality related to events on the site. 
 * Cloudscapes can be given start and end dates. If a cloudscape is given a start date
 * then it is considered an 'event' and so displays in the events calendar etc. 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Events
 */

class Events extends Controller {

	function Events ()
	{
		parent::Controller();	
		$this->load->library('layout', 'layout_main'); 
		$this->load->model('user_model');
		$this->load->model('events_model');
	}
	
	/**
	 * Display a list of events for this month and upcoming months
	 *
	 */
	function events_list() {
	    
	    $current_month = date('m');
	    $current_year  = date('Y');
	    
	    // Get the events for the next twelve months a month at a time;
	    $month = $current_month;
	    $year  = $current_year;
	    
	    for ($i = 0; $i < 12; $i++) {
	       $events[$month][$year] = $this->events_model->get_events_for_month($month, $year);
           $month++;
           if ($month == 13) { // At the end of the year, 
               $month = 1;
               $year++;
           }
	    }
        
	    $data['events']        = $events;
	    $data['current_month'] = $current_month;
	    $data['current_year']  = $current_year;
	    $data['navigation']    = 'events';
	    $data['title']         = 'Current and upcoming events';
	                
	    $this->layout->view('events/list', $data);
	}

	/**
	 * Display a list of past events
	 *
	 */
	function events_list_past() {
	    
	    $current_month = date('m');
	    $current_year  = date('Y');
	    
	    // Get the events for the last twelve months a month at a time;
	    $month = $current_month;
	    $year  = $current_year;
	    $month--;
	    if ($month == 0) {
	        $month = 12;
	        $year--;
	    }
	    
	    $first_month = $month;
	    $first_year  = $year;

	    
	    for ($i = 0; $i < 80; $i++) {
	       $events[$month][$year] = $this->events_model->get_events_for_month($month, $year);
           $month--;
           if ($month == 0) { // At the end of the year, 
               $month = 12;
               $year--;
           }
	    }
        
	    $data['events'] = $events;
	    $data['first_month'] = $first_month;
	    $data['first_year']  = $first_year;
	    $data['navigation']    = 'events';
	    $data['title']         = 'Past events';
	    $this->layout->view('events/list_past', $data);
	}	
	
	
	/**
	 * Display a list of calls (for papers etc.) for this month and upcoming months
	 *
	 */
	function calls() {
	    $current_month = date('m');
	    $current_year  = date('Y');
	    
	    // Get the events for the next twelve months a month at a time;
	    $month = $current_month;
	    $year  = $current_year;
	    
	    for ($i = 0; $i < 12; $i++) {
	       $events[$month][$year] = $this->events_model->get_calls_for_month($month, $year);
           $month++;
           if ($month == 13) { // At the end of the year, 
               $month = 1;
               $year++;
           }
	    }
        
	    $data['events'] = $events;
	    $data['current_month'] = $current_month;
	    $data['current_year']  = $current_year;
	    $data['navigation']    = 'events';
	    $data['title']         = 'Deadlines';
	    $this->layout->view('events/calls', $data);   
	}
	
	/**
	 * Display a list of old calls (for papers etc.)
	 *
	 */
	function calls_archive() {
	    $current_month = date('m');
	    $current_year  = date('Y');
	    
	    // Get the events for the last twelve months a month at a time;
	    $month = $current_month;
	    $year  = $current_year;
	    $month--;
	    if ($month == 0) {
	        $month = 12;
	        $year--;
	    }
	    
	    $first_month = $month;
	    $first_year  = $year;

	    
	    for ($i = 0; $i < 80; $i++) {
	       $events[$month][$year] = $this->events_model->get_calls_for_month($month, $year);
           $month--;
           if ($month == 0) { // At the end of the year, 
               $month = 12;
               $year--;
           }
	    }
        
	    $data['events']      = $events;
	    $data['first_month'] = $first_month;
	    $data['first_year']  = $first_year;
	    $data['navigation']  = 'events';
	    $data['title']       = 'Deadline archive';
	    $this->layout->view('events/calls_archive', $data); 
	}
}