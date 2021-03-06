<?php
/**
 * Controller for the home page
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Home
 */
class Home extends MY_Controller {

	function Home() {
	    
		parent::MY_Controller();
		$this->load->library('layout', 'layout_main');	
		$this->load->helper('format');
		if ($this->auth_lib->is_logged_in()) {
            $this->load->model('user_model');
            $this->load->model('session_model');
		}
		
	    $this->load->model('cloud_model');
	    $this->load->model('cloudscape_model');
	    $this->load->model('site_news_model');
	    $this->load->model('event_model');
	    $this->load->model('events_model');    
      
    }

	/**
	 * The site homepage
	 *
	 */
	function index() {
	    
	    $data['home']           = TRUE;

	    $data['active_clouds']        = $this->cloud_model->get_active_clouds(10);
	    $data['popular_clouds']       = $this->cloud_model->get_popular_clouds(15);
	    $data['popular_cloudscapes']  = $this->cloudscape_model->get_popular_cloudscapes(15);
	    $data['total_clouds']         = $this->cloud_model->get_total_clouds();
	    
	    $data['featured_cloudscapes'] = $this->cloudscape_model->get_featured_cloudscapes(5);  
	    $data['default_cloudscape']   = $this->cloudscape_model->get_default_cloudscape();
      $data['title']                = t("Homepage");
      $data['navigation']           = 'home'; 
      $data['rss']                  = base_url().'blog/rss';
      $data['site_news']            = $this->site_news_model->get_latest_site_news();

      if ($this->auth_lib->is_admin()) {
        $this->load->model('blog_model');
        $this->load->model('link_model');
        $this->load->model('content_model');
        $this->load->model('embed_model');
       	$this->load->model('comment_model');              
          $total_items = 0;
          $total_items  += count($this->cloud_model->get_clouds_for_moderation());
          $total_items  += count($this->comment_model->get_comments_for_moderation());
          $total_items  += count($this->cloudscape_model->get_cloudscapes_for_moderation());
          $total_items  += count($this->blog_model->get_comments_for_moderation());
          $total_items  += count($this->link_model->get_links_for_moderation());
          $total_items  += count($this->cloud_model->get_references_for_moderation());
          $total_items  += count($this->content_model->get_content_for_moderation());
          $total_items  += count($this->embed_model->get_embeds_for_moderation());
          $total_items  += count($this->user_model->get_profiles_for_moderation());

          $data['total_items'] = $total_items;

          $data['online_users']= $this->session_model->count_users();
      }

      $data['popular_type'] = $this->uri->segment(2, 'cloud');
      $data['current_month'] = date("n", time());
      $data['month']        = $this->uri->segment(1, $data['current_month'] );
      $current_year = date("Y", time());
      if ($data['month'] < $data['current_month'] ) {
          $year = $current_year + 1;
      } else {
          $year = $current_year;
      }
      $data['events'] = $this->events_model->get_events_for_month($data['month'], $year);

		$this->layout->view('home', $data);
	}

}
