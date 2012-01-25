<?php 

/**
 * Controller for functionality related to events on the site. 
 * Cloudscapes can be given start and end dates. If a cloudscape is given a start date
 * then it is considered an 'event' and so displays in the events calendar etc. 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Events
 */

class Events extends MY_Controller {

	function Events ()
	{
		parent::MY_Controller();	
		$this->load->library('layout', 'layout_main'); 
		$this->load->model('user_model');
		$this->load->model('events_model');
        $this->load->helper('format_helper');
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
     * Display future events as an icalendar file
     */
    function ical() {
        $data['events']= $this->events_model->get_future_events();
        header("Content-Type: text/Calendar");
        header("Content-Disposition: inline; filename=calendar.ics");
        $this->load->view('events/ical', $data);
    }
    
    /**
     * Display future events as an RSS feed
     */
    function rss() {
        $data['events']         = $this->events_model->get_future_events();
        
        $data['encoding']         = $this->config->item('charset');
        $data['feed_name']        = t('Future Events');
        $data['feed_url']         = base_url().'events/events_list/';
        $data['page_description'] = t('Future Events');
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');        

        header("Content-Type: application/rss+xml");
        $this->load->view('events/rss', $data);    
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
     * Display future calls as an icalendar file
     */
    function calls_ical() {
        $data['events']= $this->events_model->get_future_calls();
        header("Content-Type: text/Calendar");
        header("Content-Disposition: inline; filename=calendar.ics");
        $this->load->view('events/ical', $data);
    }

    /**
     * Display future events as an RSS feed
     */
    function calls_rss() {
        $data['events']         = $this->events_model->get_future_calls();
        
        $data['encoding']         = $this->config->item('charset');
        $data['feed_name']        = t('Future Calls');
        $data['feed_url']         = base_url().'events/events_list/';
        $data['page_description'] = t('Future Calls');
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');        

        header("Content-Type: application/rss+xml");
        $this->load->view('events/rss', $data);    
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