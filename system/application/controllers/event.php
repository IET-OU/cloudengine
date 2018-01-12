<?php

/**
 * Controller for functionality related to the cloudstreams on the site and events
 * contained in them
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Cloudstream
 */

class Event extends MY_Controller {

	function Event ()
	{
		parent::MY_Controller();

		$this->load->model('event_model');
		$this->load->library('layout', 'layout_main');
		$this->load->model('user_model');
	}


	/**
	 * Display the activity stream for the whole site
	 *
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
	 */
    function site($type = '') {
        if (!$type) {
            $events = $this->event_model->get_all(100);
        } else {
            $events = $this->event_model->get_all_type($type, 100);
            $simple = false;
            if ($type) {
                $simple = true;
            }
        }
        $data['events']   = $this->event_model->display_format($events, $simple);
        $data['title']    = t("Public Cloudstream");
        $data['rss']      = base_url().'event/site_rss/'.$type;
        $data['basepath'] = base_url().'event/site';
        $data['type']     = $type;
        $this->layout->view('event/site', $data);
    }

	/**
	 * Display the RSS feed for the activity stream for the whole site
	 *
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
	 */
    function site_rss($type = '') {
        $this->load->helper('xml');

        if (!$type) {
            $events = $this->event_model->get_all(100);
        } else {
            $events = $this->event_model->get_all_type($type, 100);
        }
        $data['events']            = $this->event_model->display_rss_format($events);
        $data['encoding']          = $this->config->item('charset');
        $data['feed_name']        = $this->config->item('site_name').':'.t('Public Cloudstream');
        $data['feed_url']         = base_url().'event/site_rss';
        $data['page_description'] =  $this->config->item('site_name').':'.t('Public Cloudstream');
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');

        header("Content-Type: application/rss+xml");
        $this->load->view('event/rss', $data);
    }

	/**
	 * Display the activity stream for all items followed by the currently logged in user
	 *
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
	 */
    function following($type = '') {
        $user_id  = $this->db_session->userdata('id');
        if ($user_id) {
            $user = $this->user_model->get_user($user_id);
            $events = $this->event_model->get_events_for_following($user_id, 50, $type);
            $simple = false;
            if ($type) {
                $simple = true;
            }
            $data['events']   = $this->event_model->display_format($events, $simple);
            $data['type']     = $type;
            $data['title']    = t("Your Cloudstream");
            $data['basepath'] = '/event/following';
            $this->layout->view('event/following', $data);
        } else {
            show_error(t("You need to be logged in to view this page."));
        }
    }

    /**
	 * Display the activity stream for all items created by a specified user
     *
     * @param integer type $user_id The id of the user
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
     */
    function user($user_id = 0, $type = '') {

        if ($user_id) {
            $events = $this->event_model->get_events_for_user($user_id, 100, $type);
        }

        $data['type']   = $type;

        $user = $this->user_model->get_user($user_id);
        $simple = FALSE;
        $data['user'] = $user;
            if ($type) {
                $simple = true;
            }
        $data['events'] = $this->event_model->display_format($events, $simple);
        $data['basepath']= $this->config->site_url('event/user/'.$user_id);
        $data['rss']     = $this->config->site_url('event/user_rss/'.$user_id.'/'.$type);
        $data['title'] = t("!person's cloudstream", array('!person'=>$user->fullname));
        $this->layout->view('event/user', $data);
    }

    /**
	 * Display the RSS feed for the activity stream for all items created by a specified user
     *
     * @param integer type $user_id The id of the user
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
     */
    function user_rss($user_id = 0, $type = '') {
        $this->load->helper('xml');
        $this->load->model('cloudscape_model');
        $user                     = $this->user_model->get_user($user_id);
        $events                   = $this->event_model->get_events_for_user($user_id, 100,
                                                                            $type);
        $data['events']           = $this->event_model->display_rss_format($events);
        $data['encoding']         = $this->config->item('charset');
        $data['feed_name']        = t('Cloudstream for !person',
                                      array('!person' => $user->fullname));
        $data['feed_url']         = base_url().'event/user_rss/'.$user_id;
        $data['page_description'] = 'Cloudstream for '.$user->fullname;;
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');
        header("Content-Type: application/rss+xml");
        $this->load->view('event/rss', $data);
    }

    /**
     *  Display the activity stream for all items in a specified cloudscape
     *
     * @param integer $cloudscape_id The id of the cloudscape
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
     */
    function cloudscape($cloudscape_id = 0, $type = '') {
        $this->load->model('cloudscape_model');
        $cloudscape               = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        $events                   = $this->event_model->get_events_for_cloudscape(
                                                                   $cloudscape_id, 100, $type);
        $data['cloudscape']       = $cloudscape;
        $data['events']           = $this->event_model->display_format($events);
        $data['rss']              = $this->config->site_url('event/cloudscape_rss/'.$cloudscape_id.'/'.$type);
        $data['type']             = $type;
        $data['basepath']         = $this->config->site_url('event/cloudscape/'.$cloudscape_id);
        $data['omit_cloudscapes'] = true;
        $data['title']            = t("Cloudstream for the cloudscape !title",
                                      array('!title'=>"'$cloudscape->title'"));
        $this->layout->view('event/cloudscape', $data);
    }

    /**
     *  Display the RSS feed for the activity stream for all items in a specified cloudscape
     *
     * @param integer $cloudscape_id The id of the cloudscape
	 * @param string $type The type of events to display in the activity stream. Empty string
	 * denotes events of all types
     */
    function cloudscape_rss($cloudscape_id = 0, $type = '') {
        $this->load->helper('xml');

        $this->load->model('cloudscape_model');
        $cloudscape               = $this->cloudscape_model->get_cloudscape($cloudscape_id);
        $events                   = $this->event_model->get_events_for_cloudscape(
        													      $cloudscape_id, 100, $type);
        $data['events']           = $this->event_model->display_rss_format($events);
        $data['encoding']         = $this->config->item('charset');
        $data['feed_name']        = t('Cloudstream for !title',
                                      array('!title'=>$cloudscape->title));
        $data['feed_url']         = base_url().'event/cloudscape_rss/'.$cloudscape_id;
        $data['page_description'] = 'Cloudstream for '.$cloudscape->title;
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');
        header("Content-Type: application/rss+xml");
        $this->load->view('event/rss', $data);
    }

    /**
     * Display the admin activity stream
     */
    function admin() {
        $this->auth_lib->check_is_admin();
        $events_raw = $this->event_model->get_events_for_admin(500);
        $data['events'] = $this->event_model->display_format($events_raw);
        $data['title'] = t("Admin cloudstream");
        $this->layout->view('event/admin', $data);
    }

		public function admin_ban() {
				$this->auth_lib->check_is_admin();
				$events_raw = $this->event_model->get_events_for_admin(500);
				$events_user_ids = [];
				$this->event_model->display_format($events_raw, false, $events_user_ids);
				$data['events'] = $events_user_ids;
				$data['events_raw'] = $events_raw;
				$data['title'] = t("Admin cloudstream (banning)");
				$this->layout->view('event/admin_ban', $data);
		}
}
