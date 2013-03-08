<?php 
/**
 * Generic functions for items in the site 
 * The current item types are cloud, cloudscape and user 
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Item 
 */
class Item_model extends Model {

    protected $ci;

    function Item_model() {
		$this->ci = & get_instance();
        parent::Model();
    }
    
    /**
     * Determine the URL for viewing an item on the site
     *
     * @param string $item_type The type of item e.g. 'cloud', 'cloudscape' or 'user'
     * @param integer $item_id The ID of the item
     * @return string The URL for viewing the item
     */
    function view($item_type, $item_id) {
       $url = FALSE;
       switch ($item_type) {
           case 'cloud': 
               $url = 'cloud/view/'.$item_id; 
               break;
           case 'cloudscape': 
               $url = 'cloudscape/view/'.$item_id; 
               break;
           case 'user': 
               $url = 'user/view/'.$item_id; 
               break;
		   case 'cloud_comment':
			  $this->ci->load->model('comment_model');
			  $comment = $this->ci->comment_model = get_comment($item_id);
			  $url = 'cloud/view/'.$comment->cloud_id.'/comments#cloud_comment-'.$item_id; 
			  break;
		   case 'embed':
			  $this->ci->load->model('embed_model');
			  $embed = $this->ci->embed_model->get_embed($item_id);
			  $url = 'cloud/view/'.$embed->cloud_id.'#embedt-'.$item_id; 
			  break;
		   case 'link':
		      $this->ci->load->model('link_model');
			  $link = $this->ci->link_model->get_embed($link_id);
			  $url = 'cloud/view/'.$link->cloud_id.'/links#link-'.$item_id; 
			  break;		   
		   case 'content':
		      $this->ci->load->model('content_model');
		   	  $content = $this->ci->content_model->get_content($item_id);
			  $url = 'cloud/view/'.$content->cloud_id.'#content-'.$item_id; 
			  break;
       } 
       return $url; 
    }
	    
    /**
     * Determine if a user has edit permission for an item and if not redirect to
     * an error page. 
     *
     * @param integer $user_id The ID of the user
     * @param string $item_type The type of item e.g. 'cloud', 'cloudscape' or 'user'
     * @param integer $item_id The ID of the item
     */
    function check_edit_permission($user_id, $item_type, $item_id) {
       switch ($item_type) {
           case 'cloud' : 
                $this->ci->load->model('cloud_model');
                $this->ci->cloud_model->check_edit_permission($user_id, $item_id);
                break;
           case 'cloudscape' : 
                $this->ci->load->model('cloudscape_model');
                $this->ci->cloudscape_model->check_admin_permission($cloudscape_id, $user_id);
                break;
           case 'user': 
               if ($user_id != $item_id) {
                   show_error('You do not have permission to edit the profile of this person.');
               }
        }
    
    }
    
    /**
     * Get the title of an item
     *
     * @param string $item_type The type of item e.g. 'cloud', 'cloudscape' or 'user'
     * @param integer $item_id The ID of the item
     * @return string The item title
     */
    function get_title($item_type, $item_id) {
       $title = FALSE;
       switch ($item_type) {
           case 'cloud' :
               $this->ci->load->model('cloud_model');
               $cloud = $this->ci->cloud_model->get_cloud($item_id);
               $title = $cloud->title;
               break;
           case 'cloudscape' :
               $this->ci->load->model('cloudscape_model');
               $cloudscape = $this->ci->cloudscape_model->get_cloudscape($item_id);
               $title = $cloudscape->title;
               break;
           case 'user':
                $this->ci->load->model('user_model');
                $profile = $this->ci->user_model->get_user($item_id);
                $title = $profile->fullname;
        }

        return $title;
    }

}