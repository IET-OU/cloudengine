<?php 

/**
 * Controller for functionality related to events on the site. 
 * Cloudscapes can be given start and end dates. If a cloudscape is given a start date
 * then it is considered an 'event' and so displays in the events calendar etc. 
 * @copyright 2009, 2010, 2012 The Open University. See CREDITS.txt
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
    function view($view = 'cloudscapes') {
        $current_month = date('m');
        $current_year  = date('Y');
        
        // Get the events for the next twelve months a month at a time;
        $month = $current_month;
        $year  = $current_year;
        
        for ($i = 0; $i < 12; $i++) {
           switch ($view) {
                case 'cloudscapes':
                    $events[$month][$year] = 
                      $this->events_model->get_events_for_month($month, $year);
                    break;
                case 'clouds':
                    $events[$month][$year] = 
                $this->events_model->get_cloud_events_for_month($month, $year);
                    break;
                case 'calls':
                    $events[$month][$year] = 
                       $this->events_model->get_calls_for_month($month, $year);
           
           }
           $month++;
           if ($month == 13) { // At the end of the year, 
               $month = 1;
               $year++;
           }
        }
        
        $data['events']      = $events;
        $data['view']        = $view;
        $data['first_month'] = $current_month;
        $data['first_year']  = $current_year;
        $data['navigation']  = 'events';
        $data['title']       = t('Events');
        $data['archive']     = FALSE;
          
        $this->layout->view('events/view', $data);
    }

    /**
     * Display a list of past events
     *
     */
    function archive($view = 'cloudscapes') {        
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
            switch ($view) {
                case 'cloudscapes':
                    $events[$month][$year] = 
                      $this->events_model->get_events_for_month($month, $year);
                    break;
                case 'clouds':
                    $events[$month][$year] = 
                $this->events_model->get_cloud_events_for_month($month, $year);
                    break;
                case 'calls':
                    $events[$month][$year] = 
                       $this->events_model->get_calls_for_month($month, $year);
           
           }
           $month--;
           if ($month == 0) { // At the end of the year, 
               $month = 12;
               $year--;
           }
        }
        
        $data['events']      = $events;
        $data['view']        = $view;
        $data['first_month'] = $first_month;
        $data['first_year']  = $first_year;
        $data['navigation']  = 'events';
        $data['title']       = t('Past events');
        $data['archive']     = TRUE;
        $this->layout->view('events/archive', $data);
    }

    /**
     * Display future events as an icalendar file
     */
    function ical($view = 'cloudscapes', $mode = 'standard', $debug = false) {
        $this->load->helper('api_helper');

        switch ($view) {
            case 'cloudscapes':
                $data['events']= $this->events_model->get_future_events();
                break;
            case 'clouds':
                $data['events']= 
                            $this->events_model->get_future_cloud_events();
                break;
            case 'calls':
                $data['events']= $this->events_model->get_future_calls();
                break;
            default:
                show_error("Error, unknown view '$view'.");
        }    

		$data['debug'] = $debug;
		$data['view'] = $view;
		$data['extended'] = 'ex'==$mode;
        $this->load->view('events/ical', $data);
    }

    /**
     * Display future events as an RSS feed
     */
    function rss($view = 'cloudscapes') {
        $this->load->helper('api_helper');

        switch ($view) {
            case 'cloudscapes':
                $data['events']= $this->events_model->get_future_events();
                $data['feed_name']        = t('Conferences');
                $data['feed_url']         = base_url().'events/rss/';
                $data['page_description'] = t('Conferences');                
                break;
            case 'clouds':
                $data['events']= 
                            $this->events_model->get_future_cloud_events();
                $data['feed_name']        = t('Workshops, seminars and talks');
                $data['feed_url']         = base_url().'events/rss/clouds';
                $data['page_description'] = t('Workshops, seminars and talks'); 
                break;
            case 'calls':
                $data['events']= $this->events_model->get_future_calls();
                $data['feed_name']        = t('Deadlines');
                $data['feed_url']         = base_url().'events/rss/calls';
                $data['page_description'] = t('Deadlines'); 
        }   
        $data['encoding']         = $this->config->item('charset');
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email'); 

        $this->load->view('events/rss', $data);    
    }    
}