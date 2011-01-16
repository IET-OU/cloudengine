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
        $this->ci = & get_instance();
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
       $this->ci = & get_instance();
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