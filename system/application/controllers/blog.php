<?php
/**
 * Controller for functionality related to the site blog
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Blog
 */
class Blog extends MY_Controller {

	function Blog () {
		parent::MY_Controller();
		$this->load->model('blog_model');
		$this->load->model('event_model');
		$this->load->model('user_model');
		$this->load->library('layout', 'layout_main');
	}

	/**
	 * View a blog post
	 *
	 * @param integer $post_id The ID of the blog post
	 */
	function view($post_id = 0) {	
	    $news = $this->blog_model->get_blog_post($post_id);
	    
	    // Form validation for comments
	    $this->load->library('form_validation');
        $this->form_validation->set_rules('body', t("Comment"), 'required');
	    
        // Process the comment form 
	    if ($this->input->post('submit')) {
	        $user_id  = $this->db_session->userdata('id');
	        $this->auth_lib->check_logged_in();
            $post_id = $this->input->post('post_id');

            if (!is_numeric($post_id)) {
                show_error(t("An error occurred when viewing this blog post"));
            }
            
            if ($this->form_validation->run()) {
                $body = $this->input->post('body');

                $moderate = $this->_moderate_comment($body, $user_id);  // Moderate for spam  

                $comment_id = $this->blog_model->insert_comment($post_id, $user_id, $body, 
                                                                $moderate);
                if (config_item('x_moderation') && $moderate) {
                    $data['item'] = 'comment';
                    $data['continuelink'] = '/blog/view/'.$post_id;
                    $this->layout->view('moderate', $data);
                    return;                
                }  
                redirect('/blog/view/'.$post_id); // Return to the main cloud view page 
            }
	    }

	    $data['comments']  = $this->blog_model->get_comments($post_id);
	    $data['news'] = $news;
	    $data['title'] = $news->title;
	    $data['admin'] = $this->auth_lib->is_admin();
	    $this->layout->view('blog/view', $data);
	}
	
	/**
	 * Display the archive of blog posts
	 *
	 */
	function archive() {
	   $data['news_items'] = $this->blog_model->get_blog_posts();
       $data['title']      = $this->config->item('site_name').' '.t("Blog Archive");
	   $data['rss']        = site_url('blog/rss');
       $data['admin'] = $this->auth_lib->is_admin();
	   $this->layout->view('blog/archive', $data);
	}
  	
	/**
	 * Add a new blog post - only admins are allowed to do this
	 */
	function add() {
	    $this->auth_lib->check_is_admin(); 
        $user_id  = $this->db_session->userdata('id');        
        
        // Set up form validation 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        $this->form_validation->set_rules('body', t("Description"), 'required');
  
        // If add news form has been submitted then process it
        if ($this->input->post('submit')) {
            // Get the information from the form and add the news
            $news->title   = $this->input->post('title');
            $news->body    = $this->input->post('body');
            $news->user_id = $user_id;
            if ($this->form_validation->run()) {
                $post_id = $this->blog_model->insert_blog_post($news, $tags);

                if ($post_id) {
                    redirect('/blog/view/'.$post_id );
                } else {
                    show_error(("An error occurred adding the new blog post.")); 
                }
            } else {
                $data['news'] = $news;
            }
        }

        $data['new']   = true;
        $data['title'] = t("Create news");

        // Display the form for creating a news
        $this->layout->view('blog/edit', $data);

    }
    
    /**
     * Edit an existing blog post - only admins are allowed to do this
     *
     * @param integer $post_id The ID of the blog post
     */
    function edit($post_id = 0) {
        $this->auth_lib->check_is_admin(); 
        
        // Check permissions
        $user_id  = $this->db_session->userdata('id');
        
        // Set up form validation rules (empty rules needed for set_value() 
        $this->load->library('form_validation');
        $this->form_validation->set_rules('title', t("Title"), 'required');
        $this->form_validation->set_rules('body', t("Description"), 'required');

        if ($this->input->post('submit')) {
            // Get the form data 
            $news->post_id = $post_id;
            $news->title    = $this->input->post('title');
            $news->body     = $this->input->post('body');
            $news->user_id  = $user_id;

            // Validate the data, if fine, update the news and redirect to the news page,
            // otherwise keep the submitted ata to repopulate the form
            if ($this->form_validation->run()) {
                $this->blog_model->update_blog_post($news);
                redirect('/blog/view/'.$post_id);
            } else {
                $data['news'] = $news;
            }
        } 
        
        // If no data already set from invalid form submission, get the data for the news
        if (!isset($data['news'])) {
            $data['news'] = $this->blog_model->get_blog_post($post_id);
        }
        $data['new']   = false; 
        $data['title'] = t("Edit news");
        
        // Display the edit form 
        $this->layout->view('blog/edit', $data); 
    }

    /**
     * Delete a blog post - only admins are allowed to do this
     *
     * @param integer $post_id The ID of the blog post
     */
    function delete($post_id = 0) {
        $this->auth_lib->check_is_admin(); 
        
        // If confirmation form submitted delete the newsscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $data['news'] = $this->blog_model->get_blog_post($post_id);
            $this->blog_model->delete_blog_post($post_id);
            $this->layout->view('blog/delete_success', $data);
        } else {
            $data['title'] = t("Delete news");
            $data['post_id'] = $post_id;
            $data['news'] = $this->blog_model->get_blog_post($post_id);
            $this->layout->view('blog/delete_confirm', $data);
        }
    }
   
	/**
	 * Display the RSS feed for the blog
	 */
    function rss() {
        $this->load->helper('xml');
        $data['news']             = $this->blog_model->get_blog_posts(10); 
        $data['encoding']         = $this->config->item('charset');;
        $data['feed_name']        = $this->config->item('site_name').t(' Blog');
        $data['feed_url']         = site_url('blog/rss');
        $data['page_description'] = $this->config->item('site_name').' blog';
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');   
        header("Content-Type: application/rss+xml");       
        $this->load->view('rss/news_rss', $data);
    }
    
    /**
     * Edit a comment on a blog post - only admins are allowed to do this
     *
     * @param integer $comment_id The ID of the comment
     */
    function comment_edit($comment_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $user_id = $this->db_session->userdata('id');
        
        if ($this->input->post('submit')) {
            // Get the form data 
            $comment->comment_id = $comment_id;
            $comment->body       = $this->input->post('body');

           // Set up form validation rules (empty rules needed for set_value() 
            $this->load->library('form_validation');

           $this->form_validation->set_rules('body', t("Comment"), 'required');
            // Validate the data, if fine, update the news and redirect to the news page,
            // otherwise keep the submitted ata to repopulate the form
            if ($this->form_validation->run()) {
                $this->blog_model->update_comment($comment);
                $comment = $this->blog_model->get_comment($comment_id);
                redirect('/blog/view/'.$comment->post_id);
            } else {
                $data['comment'] = $comment;
            }
        } 
        
        // If no data already set from invalid form submission, get the data for the news
        if (!isset($data['comment'])) {
            $data['comment'] = $this->blog_model->get_comment($comment_id);
        }
        $data['new']   = false; 
        $data['title'] = t("Edit comment");
        
        // Display the edit form 
        $this->layout->view('blog_comment/edit', $data); 
    }

    /**
     * Delete a comment - only admins are allowed to do this
     *
     * @param integer $comment_id The ID of the comment
     */
    function comment_delete($comment_id = 0) {
        $this->auth_lib->check_is_admin(); 
        $data['comment'] = $this->blog_model->get_comment($comment_id);
        
        // If confirmation form submitted delete the newsscape, otherwise display 
        // the confirmation form 
        if ($this->input->post('submit')) {
            $this->blog_model->delete_comment($comment_id);
            $this->layout->view('blog_comment/delete_success', $data);
        } else {
            $data['title'] = t("Delete comment");
            $data['comment_id'] = $comment_id;
            $this->layout->view('blog_comment/delete_confirm', $data);
        }
    }   
    
    /**
     * Check a comment for moderation using Mollom if moderation has been enabled
     *
     * @param string $body The body of the comment
     * @param integer $user_id The ID of the user
     * @return boolean TRUE if the item should be moderated, FALSE otherwise
     */
    function _moderate_comment($body, $user_id) {
    	$moderate = FALSE;
        if (config_item('x_moderation')) {
            $user = $this->user_model->get_user($user_id); 
            if (!$user->whitelist) {
                $this->load->library('mollom');
                try { 
                        $spam_status = $this->mollom->checkContent(null, $body); 	 
                        	 
                        if ($spam_status['quality'] < 0.5) {
                            $moderate = TRUE;
                        }  
                } catch (Exception $e) {
                    
                }
            }
       	}
       	
        return $moderate;                          
    }
}
