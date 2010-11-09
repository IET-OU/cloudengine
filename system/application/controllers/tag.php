<?php
/**
 * Controller for  functionality related to tags
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Tag
 */
class Tag extends MY_Controller {

	function Tag()
	{
		parent::MY_Controller();
		$this->load->model('tag_model');
		$this->load->library('layout', 'layout_main'); 
	}

	/**
	 * View a tag cloud of tags for the site
	 */
	function index() {
	    $data['tags']       = $this->tag_model->get_all_tags(50);   
	    $data['toptags']    = $this->tag_model->get_all_tags(10); 
	    $data['title']      = t("Tags");
	    $data['navigation'] = 'tags';
        $this->layout->view('tag/tag_cloud', $data);
	}	
	
	/**
	 * View items for a specified tag 
	 *
	 * @param string $tag The tag (URL-encoded)
	 */
	function view($tag = '') {
	    $tag = urldecode($tag);
	    $data['tag'] = $tag;
	    $data['clouds']      = $this->tag_model->get_clouds($tag);
	    $data['cloudscapes'] = $this->tag_model->get_cloudscapes($tag);
	    $data['users']       = $this->tag_model->get_users($tag); 
	    $data['title']       = t("Items tagged !tag", array('!tag'=>$tag));
	    $data['navigation']  = 'tags';
	    $data['rss']         = '/tag/rss/'.$tag;
	    $this->layout->view('tag/view.php', $data);
	}
	
	/**
	 * View the rss feed for a tag
	 *
	 * @param string $tag The tag (URL-encoded)
	 */
    function rss($tag = '') {
        $tag = urldecode($tag);
        
        $this->load->helper('xml');
	    $data['tag']              = $tag;
	    $data['clouds']           = $this->tag_model->get_clouds($tag);
        $data['encoding']         = $this->config->item('charset');
        $data['feed_name']        = $this->config->item('site_name').': '.$tag;
        $data['feed_url']         = base_url().'tag/rss.'.$tag;
        $data['page_description'] = $this->config->item('site_name').' clouds tagged '.$tag;
        $data['page_language']    = 'en';
        $data['creator_email']    = $this->config->item('site_email');   
        header("Content-Type: application/rss+xml");       
        $this->load->view('rss/rss', $data);
    }   
    
    /**
     * Delete a tag
     *
     * @param integer $tag_id The ID of the tag
     */
    function delete($tag_id = 0)  {
       $user_id  = $this->db_session->userdata('id'); 
       $this->load->model('item_model');
        
       $tag = $this->tag_model->get_tag($tag_id);
       $item_type = $tag->item_type;
       $item_id   = $tag->item_id;
       
       // Check the user has permission to delete the tag, then delete it
       $this->item_model->check_edit_permission($user_id, $item_type, $item_id); 
       $this->tag_model->delete_tag($tag_id);
       
       // Redirect to the item that the tag  belonged to
       $url = $this->item_model->view($item_type, $item_id);
       redirect($url);    
    }
    
    /**
     * Add tags to an item
     *
     * @param string $item_type The item type e.g. 'cloud', 'cloudscape', 'user'
     * @param integer $item_id The ID of the item
     */
    function add_tags($item_type, $item_id) {
        $this->load->model('item_model');
        $this->auth_lib->check_logged_in();
        $user_id  = $this->db_session->userdata('id');
        
        $url = $this->item_model->view($item_type, $item_id);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('tags', t("Tags"), 'required');
        
        if ($this->input->post('submit')) {
            if ($this->form_validation->run()) {
                // Get the form data 
                $tags = $this->input->post('tags');
                $this->tag_model->add_tags($item_type, $item_id, $tags, $user_id);
               
                redirect($url); // Return to the main cloud view page 
               
            }
        }       
        
        // Display the add link form
        $data['url']        = $url;
        $data['item_title'] = $this->item_model->get_title($item_type, $item_id); 
        $data['title']      = t("Add Tags");
        $this->layout->view('tag/add', $data);         
    }  
}