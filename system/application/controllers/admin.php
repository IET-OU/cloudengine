<?php

/**
 * Controller for admin interface for the site
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Admin
 */

class Admin extends MY_Controller {

	function Admin() {
		parent::MY_Controller();
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
	 * Display the admin panel
	 *
	 */
	function panel() {
        ///Translators: ** Start of the CONTROLLERS/MODELS section - page titles/error messages. **
        $data['title'] = t('Admin panel');
	    $this->layout->view('admin/index', $data);	    
	}

	/**
	 * Display the moderation queue page 
	 * 
	 */
	function moderate() {
		if (!config_item('x_moderation')) {
			show_404();
		}
		
	     if ($this->input->post('submit')) {
	         $action = $this->input->post('action');
	         $type   = $this->input->post('type');
	         $id     = $this->input->post('id');
	         $this->load->library('mollom');
            
	         switch($type) {
	             case "cloud":
	                 if ($action == 'approve') {
	                     $this->cloud_model->approve_cloud($id);
	                 } elseif ($action == 'spam') {
	                     $this->cloud_model->delete_cloud($id);
	                 }
	                 break;
	             case "comment":
	                 if ($action == 'approve') {
	                     $this->comment_model->approve_comment($id);
	                 } elseif ($action == 'spam') {
	                     $this->comment_model->delete_comment($id);
	                 }
	                 break;
	             case "cloudscape":       
	                 if ($action == 'approve') {
	                     $this->cloudscape_model->approve_cloudscape($id);
	                 } elseif ($action == 'spam') {
	                     $this->cloudscape_model->delete_cloudscape($id);
	                 }
	                 break;	        
	             case "news_comment":
	                 if ($action == 'approve') {
	                     $this->blog_model->approve_comment($id);
	                 } elseif ($action == 'spam') {
	                     $this->blog_model->delete_comment($id);
	                 }
	                 break;
	             case 'link':
	                 if ($action == 'approve') {
	                     $this->link_model->approve_link($id);
	                 } elseif ($action == 'spam') {
	                     $this->link_model->delete_link($id);
	                 }
	                break;
	             case 'reference':
	                 if ($action == 'approve') {
	                     $this->cloud_model->approve_reference($id);
	                 } elseif ($action == 'spam') {
	                     $this->cloud_model->delete_reference($id);
	                 }
	                break;	 
	             case 'content':
	                 if ($action == 'approve') {
	                     $this->content_model->approve_content($id);
	                 } elseif ($action == 'spam') {
	                     $this->cloud_model->delete_content($id);
	                 }
	                break;		
	             case 'embed':
	                 if ($action == 'approve') {
	                     $this->embed_model->approve_embed($id);
	                 } elseif ($action == 'spam') {
	                     $this->embed_model->delete_embed($id);
	                 }
	                break;		                                               
	         }
	     }

	     // Get all the items to be moderated 
    	 $data['clouds']        = $this->cloud_model->get_clouds_for_moderation();
    	 $data['comments']      = $this->comment_model->get_comments_for_moderation();
    	 $data['cloudscapes']   = $this->cloudscape_model->get_cloudscapes_for_moderation();
    	 $data['news_comments'] = $this->blog_model->get_comments_for_moderation();
    	 $data['links']         = $this->link_model->get_links_for_moderation();
    	 $data['references']    = $this->cloud_model->get_references_for_moderation();
    	 $data['contents']      = $this->content_model->get_content_for_moderation();
         $data['embeds']        = $this->embed_model->get_embeds_for_moderation();  

        $data['title'] = t('Moderate');
        $this->layout->view('admin/moderate', $data);

	}
	
	/**
	 * Edit the featured cloudscapes
	 */
	function featured_cloudscapes() {

	   if ($this->input->post('preview')) {   
	      $cloudscapes[0] = $this->cloudscape_model->get_cloudscape($this->input->post('cloudscape0'));
	      $cloudscapes[1] = $this->cloudscape_model->get_cloudscape($this->input->post('cloudscape1'));
 
	      $cloudscapes[2] = $this->cloudscape_model->get_cloudscape($this->input->post('cloudscape2'));
	      $cloudscapes[3] = $this->cloudscape_model->get_cloudscape($this->input->post('cloudscape3'));
	      $cloudscapes[4] = $this->cloudscape_model->get_cloudscape($this->input->post('cloudscape4'));

	      $data['featured_cloudscapes'] = $cloudscapes;
	      
	      $this->layout->view('admin/featured_cloudscapes_preview', $data);
	      return;
	   }
	   
	   if ($this->input->post('submit')) {
            $cloudscapes[0] = $this->input->post('cloudscape0');
            $cloudscapes[1] = $this->input->post('cloudscape1');
            $cloudscapes[2] = $this->input->post('cloudscape2');
            $cloudscapes[3] = $this->input->post('cloudscape3');
            $cloudscapes[4] = $this->input->post('cloudscape4');
            $this->cloudscape_model->update_featured_cloudscapes($cloudscapes);
            redirect(base_url());

	   }

        $data['title'] = t('Manage featured cloudscapes');	   
       $data['cloudscapes'] =  $this->cloudscape_model->get_featured_cloudscapes(5);
	   $this->layout->view('admin/featured_cloudscapes', $data);
	}


    /**
     * Output a configuration/ phpinfo page. (was 'phpinfo')
     */
    function phpinfo() {
        $this->load->helper('xml_helper');

        require_once(APPPATH.'/libraries/install/install_lib'.EXT);
        $this->load->library('Hglib');
        $hg_revision = $this->hglib->read_revision();

        $this->layout->view('admin/phpinfo', array('title'=> 'Admin - Configuration', 'hg'=>$hg_revision));
    }
    
    /**
     * Recalculate the cached list of popular clouds and cloudscapes used for the 
     * popular section on the home page
     */
    function recalculate_popular() {
        $this->cloud_model->repopulate_popular_clouds();
        $this->cloudscape_model->repopulate_popular_cloudscapes();
        redirect('admin/panel');
    }


    /**
     * Update the site news block on the home page
     *
     */
    function update_site_news() {
        $this->load->model('site_news_model');

        $data['site_news'] = $this->site_news_model->get_latest_site_news();
                
         if ($this->input->post('submit')) {
             $body = $this->input->post('body');
             $user_id = $this->db_session->userdata('id');
             $this->site_news_model->insert_site_news($body, $user_id);
             $data['title'] = t("Site news updated");
             $this->layout->view('admin/site_news_success', $data);
         } else {
            $data['title']     = t("Update Site News");
	        $this->layout->view('admin/site_news_form', $data); 
         }       
    }

    /**
     * Panel for managing support/about pages on the site
     *
     */
    function manage_pages() {
        $this->load->model('page_model'); 
        $data['support_pages'] = $this->page_model->get_pages('support');
        $data['about_pages'] = $this->page_model->get_pages('about');
        $data['title']         = t("Manage Pages");
        $this->layout->view('admin/pages/index', $data); 
    }

    /**
     * Edit a support/about page
     *
     * @param string $section The section i.e. 'support' or 'about'
     * @param string $name The page name
     * @param string $lang The language string for the language that the content of the page 
     * is written in. 
     */
    function edit_page($section ='', $name ='', $lang = '') {
        $this->load->model('page_model'); 
        
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', t("Page name"), 'required');
        $this->form_validation->set_rules('title', t("Page Title"), 'required');
        $this->form_validation->set_rules('lang', t("Language"), 'required');
        $this->form_validation->set_rules('body', t("Page Body"), 'required');  
        
        if ($this->input->post('submit')  && $this->form_validation->run()) {

            $page->section = $section;
            $page->name    = $this->input->post('name');
            $page->lang    = $this->input->post('lang');
            $page->title   = $this->input->post('title');
            $page->body    = $this->input->post('body');
            $this->page_model->update_page($page);
            
            $data['page'] = $this->page_model->get_page($section, $name, $lang);
            $data["title"] = t("Page Saved");
            $this->layout->view('admin/pages/edit_success', $data);
        } else {   
            $data['page'] = $this->page_model->get_page($section, $name, $lang);
            $data["title"] = t("Edit Page");
            $this->layout->view('admin/pages/edit', $data);
        }
    }

    /**
     * Add a new support/about page. 
     *
     */
    function add_page() {
        $this->load->model('page_model');
        // Set up form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('section', t("Section"), 'required');
        $this->form_validation->set_rules('name', t("Page name"), 'required');
        $this->form_validation->set_rules('title', t("Page Title"), 'required');
        $this->form_validation->set_rules('lang', t("Language"), 'required');
        $this->form_validation->set_rules('body', t("Page Body"), 'required');        
        
        if ($this->input->post('submit') && $this->form_validation->run()) {
            $page->section = $this->input->post('section');
            $page->name    = $this->input->post('name');
            $page->lang    = $this->input->post('lang');
            $page->title   = $this->input->post('title');
            $page->body    = $this->input->post('body');
            $this->page_model->insert_page($page);
            
            $data['page'] = $this->page_model->get_page($section, $name, $lang);
            $data["title"] = t("Page Saved");
            $this->layout->view('admin/pages/add_success', $data);
        } else {   
            $data["title"] = t("Add Page");
            $this->layout->view('admin/pages/add', $data);
        }
    }


    /**
     * Delete a support/about page
     *
     * @param string $section The section i.e. 'support' or 'about'
     * @param string $name The page name
     * @param string $lang The language string for the language that the content of the page 
     * is written in. 
     */
    function delete_page($section ='', $name ='', $lang = '') {
        $this->load->model('page_model'); 
        $data['title']= t("Delete Page");
        $data['page'] = $this->page_model->get_page($section, $name, $lang);
        
        if ($this->input->post('submit')) {
            $this->page_model->delete_page($section, $name, $lang);
            $this->layout->view('admin/pages/delete_success', $data);

        } else {
            $this->layout->view('admin/pages/delete_confirm', $data);
        }

    }


	/**
	 * Edit the site settings cloudscapes
	 */
	function site_settings() {
	   
    $this->load->model('settings_model');
    
    //process form submission     
    if ($this->input->post('save')) {
      foreach($_POST as $name => $value ) {
        if (substr($name,0,3) == 'db_') {
          $this->settings_model->replace_setting(substr($name,3),$this->input->post($name));
        }
      }    
      redirect('admin/site_settings');
    }

    $settings   = $this->settings_model->get_all();
    
    foreach ($settings as $setting) {
      $data[$setting->name] = $setting;
    }    

    $data['title']      = t('Site settings');
    $this->layout->view('admin/site_settings', $data);
	}

    /** Generate a CSV file of random users, filtering on existing CSV file(s).
    */
    public function random_users($target=300) {
        $this->load->model('user_model');
        $random_users = $this->user_model->get_random_users($target);

        @header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: inline; filename=random-users.csv');
        echo "Email,Fullname,Is-OU".PHP_EOL;
        foreach ($random_users as $user) {
            #$len = fputcsv(STDOUT, $random_users);
            $ou_flag = FALSE===strpos($user->email, 'open.ac.uk') ? '': '1';
            //Escaping?
            echo "$user->email,$user->fullname,$ou_flag". PHP_EOL;
        }
    }
}