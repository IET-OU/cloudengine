<?php
/**
 * The API Controller for URLs under api/clouds/, api/cloudscapes/, api/users/, api/tags/
 * and api/search/
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package API
 */

// Don't change please!
define('_API_VERSION', '0.5');
// The URL segment following an ID in eg. clouds/{ID}/followers
define('_API_RELATED_SEGMENT', 4); #3 or 4.
define('_API_USERNAME_PREFIX', ''); //Hmm, was '~'

class Api extends MY_Controller {

    protected $api_key  = NULL;
    protected $item_type= NULL; //The item type, eg. 'cloud', 'user'.
    protected $format   = NULL; //Requested output format, 'json'.
    protected $json_callback = FALSE; //JSON-P callback function.
    protected $query    = NULL; //Search query or tag-name. 
    protected $count    = NULL; //Requested number of results/items.

    /** Constructor, for now load all models. Calls '_pre_process'.
     */
    public function Api() {
        parent::MY_Controller();

        // Switch the API on or off.
        if (!$this->config->item('x_api')) {
            show_404('/api');
        }
        

        $this->load->model('cloud_model');
        $this->load->model('cloudscape_model');
        $this->load->model('user_model');

        $this->load->model('tag_model');
        $this->load->model('favourite_model');
        $this->load->model('event_model'); #Cloudstream.

        $this->load->model('content_model');
        $this->load->model('embed_model');
        $this->load->model('link_model');
        $this->load->model('comment_model');

        $this->load->library('Api_lib', NULL, 'apilib');
        return $this->_pre_process();
    }

/**
 * Helper functions - mostly move to a separate library?
 */

    /** Get the requested format.
     * @return string
     */
    public function _api_format() { return $this->format; }
    /** Get the requested callback parameter, or FALSE.
     * @return string
     */
    public function _json_callback(){ return $this->json_callback; }

    /** Get the item type, eg. 'cloud', cloudscape, user.
     * @return string
     */
    public function _item_type() { return $this->item_type; }

    /** Initialize the controller class - parse 'api_key', 'callback' parameters etc.
     *  Currently called from constructor.
     * @return
     */
    protected function _pre_process() {
        // Check for authorised access.
        $this->api_key = $this->input->get('api_key', $xss_clean=TRUE);
        $api_user = $this->apilib->api_key_valid($this->api_key);

        // Security. Only allow eg. 'Object.func_CB_1234'
        $this->json_callback = $this->input->get('callback', $xss_clean);
        if ($this->json_callback && !preg_match('/^[a-zA-Z][\w_\.]*$/', $this->json_callback)) {
            $this->_api_error("400.6", "Error, 'callback' must start with a letter, and contain only letters, numbers, underscore and '.'");
        }

        $supported_formats = $this->config->item('x_api_formats');
        if (is_string($supported_formats)) {
            $supported_formats = explode('|', $supported_formats);
        }
        $this->format = $this->uri->file_extension() ? $this->uri->file_extension() : 'json';

        //-------------------------------------------------------------------------------------------------
        // RichLove 10/11/2016 - Fix - Start (to get appropriate file format with a query string in the URL)
        //-------------------------------------------------------------------------------------------------
        // Construct the full path and query string
        $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];     
        // Get the components of the full path as an array
        $new_url = parse_url($url);
        // Get the path without query string
        $path = $new_url['path'];
        // Get detailed info of path components as array
        $pathinfo = pathinfo($path);
        // Get the file format from the path info array (e.g. json)
        $this->format = $pathinfo['extension'];
        //---------------------------------------------------------------------------------------------------
        // RichLove 10/11/2016 - Fix - Finish (to get appropriate file format with a query string in the URL)
        //----------------------------------------------------------------------------------------------------

        if (!$supported_formats || !in_array($this->format, $supported_formats)) {
            $bad_format = $this->format;
            $this->format = 'json';
            $this->_api_error("400.5", "Error, the output format '$bad_format' is not recognised.");
        }

        $this->count = $this->input->get('count', $xss_clean);
        if (!is_numeric($this->count) || $this->count > $this->config->item('x_api_max_results')) {
            $this->count = $this->config->item('x_api_stream_default');
        }

        // Production or debug, start off with 'text/javascript'.
        // Prevent most browsers prompting to save file!
        @header("Content-Type: text/javascript; charset=UTF-8");
    }


    /** Return a required URL segment.
     * @param string  $name The 'name' of the segment, eg. 'cloud_id'.
     * @param boolean $is_integer Flag whether to check for an integer/numeric value.
     * @return mixed The value.
     */
    protected function _required_segment($name, $is_integer=TRUE) {
        return $this->apilib->required_get($name, $is_integer);
    }

    /** Output an error message, in the requested output 'format'.
     * @param string $code Error code/number with format 'HTTP_ERROR.OPTIONAL_SUB_ERROR', eg. '400.3'.
     * @param string $message Explanation of the error.
     * @return
     */
    protected function _api_error($code, $message = NULL) {
        return $this->apilib->_api_error($code, $message);
    }

    /** Return a date, formatted for API.
     * @param integer $timestamp Unix time stamp
     * @return string ISO formatted date, eg. 2010-06-07T00:00:00:Z..
     */
    protected function _date($timestamp) {
        return $this->apilib->_date($timestamp);
    }

    /** Process the raw object or array from the model functions into output suitable for the API.
     * Could these funcs. be moved to CI view? - Need to return array.
     * @param mixed $data Associative array or object.
     * @return array Array almost ready for API output.
     */
    protected function _api_process($data, $array_type, $has_tag=FALSE) {
        $this->apilib->log('debug', "ok");

        $response = null;
        if (is_object($data)) {
          // Copy elements common to clouds and cloudscapes (but not users), eg. api/clouds/{ID}/view
          if ('cloud'==$this->item_type || 'cloudscape'==$this->item_type) { //isset($data->title)) {
            $response = array(
              'title'   => $data->title,
              'summary' => $data->summary,
              'body'    => $data->body,
              'created' => $this->_date($data->created),
              'modified'=> $this->_date($data->modified),
              'tags'    => $this->apilib->_tags_process($data->tags, $this->item_type.'s'),
              'self_url'=> base_url().$this->uri->uri_string(),
            );
          }

          if ('cloud'==$this->item_type) { #Cloud.
              $response = $this->apilib->_cloud_process($data, $response);
          }
          if ('cloudscape'==$this->item_type) {
              $response = $this->apilib->_cloudscape_process($data, $response);
          }

          // Add a nested rich 'author' object to eg. clouds/{ID}/view
          if (('cloud'==$this->item_type || 'cloudscape'==$this->item_type) && isset($data->fullname)) {
            $response['author'] = $this->apilib->_user_process($data, $rich=TRUE);
          }
          // Proccess users/{ID}/view - don't nest.
          if ('user'==$this->item_type && isset($data->fullname)) {
            $response = (object)$this->apilib->_user_process($data, $rich=TRUE);
          }


        } elseif (is_array($data)) {
            $response = $this->apilib->_array_process($data, $array_type, $has_tag);
        } else {
            // Error!
			var_dump($data);
        }

        return $response;
    }

// ====================================================================
/**
 * The controller methods.
 */

    /**
     * The controller for eg. /api/clouds/{cloud_id}/{related}{.format}
     * and also for /api/clouds/active
     */
    public function clouds() {
        $this->item_type = 'cloud';
        // 'clouds/active' is a special case.
        if ('active' == $this->input->get('orderby')
            || 'active' == $this->uri->segment(3)  ) {
            $cloud_id= NULL;
            $related = '__active';
        } else {
            $cloud_id = $this->_required_segment('cloud_id');
            $related = $this->uri->segment(_API_RELATED_SEGMENT);
        }

        $html_url= site_url("cloud/view/$cloud_id");
        $api_url = $this->apilib->url($this->item_type, $cloud_id, $related); 
        $total = NULL;

        // Will the response contain an 'items' array? 'cloud', cloudscape..?
        $array_type = FALSE;

        switch ($related) {
            case FALSE:  #Drop-through for aliases.
            case 'view':
            case 'item':
                $data = $this->cloud_model->get_cloud($cloud_id);
                if ($data && isset($data->title)) {
                    // Add rich content.
                    $data->tags   = $this->tag_model->get_tags('cloud', $cloud_id);
                    $data->extra_content= $this->content_model->get_content($cloud_id);
                    $data->embeds = $this->embed_model->get_embeds($cloud_id);
                    $data->links  = $this->link_model->get_links($cloud_id);
                    $data->references= $this->cloud_model->get_references($cloud_id);

                    $data->totals->views = $this->cloud_model->get_total_views($cloud_id);
                    $data->totals->followers = $this->cloud_model->get_total_followers($cloud_id);
                    $data->totals->comments = $this->comment_model->get_total_comments($cloud_id);
                }
                else {
                  $this->_api_error(404, "A Cloud with the ID '$cloud_id' was not found.");
                }
                break;

            case 'comments': // Some clouds have lots of comments, eg. 2978.
                $array_type = 'comment';
                $html_url  .= "#commments";
                $data = $this->comment_model->get_comments($cloud_id);
                break;

            case 'followers': #Check.
                $array_type = 'user';
                $data = $this->cloud_model->get_followers($cloud_id);
                $total= $this->cloud_model->get_total_followers($cloud_id);
                break;

            case 'favourited':
                $array_type = 'user';
                $data = $this->favourite_model->get_users_favourited($cloud_id, $this->item_type);
                break;

            case 'cloudscapes':
                $array_type = 'cloudscape';
                $data = $this->cloud_model->get_cloudscapes($cloud_id);
                break;

            case '__active':
                $array_type = 'cloud';

                $data    = $this->cloud_model->get_active_clouds($this->count);
	            $total   = $this->cloud_model->get_total_clouds();
                $html_url= site_url('#active-clouds');
                $api_url = $this->apilib->url($this->item_type, 'active');
                break;

            default:
                $this->_api_error('400.4', "Error in the request URL following '$this->item_type/' - the related item '$related' (segment "._API_RELATED_SEGMENT.") is not recognised.");
                break;
        }

        $data = $this->_api_process($data, $array_type);
        if ($array_type) {
            $data = array(
                'cloud_id' => $cloud_id,
                'self_url'=> $api_url,
                'html_url'=> $html_url,
                'total_results' => $total ? $total : count($data),
                'items'   => $data,
            );
        }
        echo $this->apilib->render($data);
    }

    /**
     * The controller for /api/cloudscapes/{cloudscape_id}/{related}{.format}
     */
    public function cloudscapes() {
        $this->item_type = 'cloudscape';
        $cloudscape_id = $this->_required_segment('cloudscape_id');
        $related = $this->uri->segment(_API_RELATED_SEGMENT);

        $html_url  = site_url("cloudscape/$related/$cloudscape_id");
        if (!$related) {
            $html_url = site_url("cloudscape/view/$cloudscape_id");
        }

        // Will the response contain an 'items' array? 'cloud', cloudscape..?
        $array_type = FALSE;

        switch ($related) {
            case FALSE:
            case 'view':
            case 'item':
                $data = $this->cloudscape_model->get_cloudscape($cloudscape_id);
                if (!$data) {
                    $this->_api_error(404, "A Cloudscape with the ID '$cloudscape_id' was not found");
                }
                //Add richness. Reveal more data?
                $data->tags = $this->tag_model->get_tags('cloudscape', $cloudscape_id);

                $data->totals->views = $this->cloudscape_model->get_total_views($cloudscape_id);
                $data->totals->clouds= $this->cloudscape_model->get_total_clouds($cloudscape_id);
                $data->totals->followers = $this->cloudscape_model->get_total_followers($cloudscape_id);

                $this->load->model('events_model');
                $data->is_event = FALSE;
                if ($this->events_model->is_event($cloudscape_id)) {
                    $data->is_event = TRUE;
                    $data->totals->attendees = $this->events_model->get_total_attendees($cloudscape_id);
                }
                break;

            case 'followers':
                $array_type = 'user';
                $data = $this->cloudscape_model->get_followers($cloudscape_id); 
                break;

            case 'favourited':
                $array_type = 'user';
                $data = $this->favourite_model->get_users_favourited($cloudscape_id, $this->item_type);
                break;

            case 'clouds':
                $array_type = 'cloud';
                $html_url = site_url("cloudscape/view/$cloudscape_id#clouds-in-cloudscape");
                $data = $this->cloudscape_model->get_clouds($cloudscape_id);
                break;

            case 'attendees':
                $array_type = 'user';

                $this->load->model('events_model');

                if (! $this->events_model->is_event($cloudscape_id)) {
                    $this->_api_error('400', "Error, this cloudscape is not for an event.");
                }
                $data = $this->events_model->get_attendees($cloudscape_id);
                break;

            default:
                $this->_api_error('400.4', "Error in request URL following '$this->item_type/' - the related item '$related' (segment "._API_RELATED_SEGMENT.") is not recognised.");
                break;
        }

        $data = $this->_api_process($data, $array_type);
        if ($array_type) { #$array_resp) {
            $data = array(
                'cloudscape_id' => $cloudscape_id,
                'self_url'=> $this->apilib->url($this->item_type, $cloudscape_id, $related),
                'html_url'=> $html_url,
                'total_results' => count($data),
                'items'   => $data,
            );
        }
        echo $this->apilib->render($data);
    }

    /**
     * Controller for /api/users/{user_id}/{related}{.format}
     */
    public function users() {
        $this->item_type = 'user';
        $user_id = $this->_required_segment('user_id', FALSE); //Allow non-numeric (starting '~'. Eg. users/~nick.)
        $related = $this->uri->segment(_API_RELATED_SEGMENT);

		$data = $this->user_model->get_user($user_id);
        if (!$data || !($data->user_id || $data->id)) {
            $this->_api_error(404, "A user with the ID '$user_id' was not found.");
        }
        $user_id  = $data->user_id = $data->id;
        $user_name= $data->user_name;

        $html_url = site_url("user/$related/$user_id");
        if (!$related) {
            $html_url = site_url("user/view/$user_id");
        }

        // Will the response contain an 'items' array? 'cloud', cloudscape..?
        $array_type = FALSE;

        switch ($related) {
            case FALSE:
            case 'view':
            case 'item':
                $data->picture = $this->user_model->get_picture($user_id);

                //  Add richness. (get_tags_for_item removed, iet-bug#1034)
                if (!isset($data->tags)) {
                    $data->tags = $this->tag_model->get_tags($this->item_type, $user_id);
                }
                $data->totals->clouds = $this->user_model->get_cloud_total($user_id);
                $data->totals->cloudscapes= $this->user_model->get_cloudscape_total($user_id);
                $data->reputation     = $this->favourite_model->get_reputation($user_id);
            	break;

            case 'followers':
                $array_type = 'user';
                $data = $this->user_model->get_followers($user_id); 
            	break;

            case 'favourites':
                $array_type = 'mixed';  //Clouds and cloudscapes.
                $data = $this->favourite_model->get_favourites($user_id, null);
            break;

            case 'cloudscapes':
                $array_type = 'cloudscape';
                $data = $this->cloudscape_model->get_cloudscapes_owner($user_id);
                break;

            case 'clouds':
                $array_type = 'cloud';
                $data = $this->user_model->get_clouds($user_id, $this->count);
                break;

            case 'stream':
                $array_type = 'stream';
                $html_url = site_url("event/user/$user_id#cloudstream");

                // Undocumented '.js' format, for 'users/ID/stream'.
                if ('js'==$this->format) {
                    $title = $this->input->get('title', $xss_clean=TRUE);
                    if (!$title) {
                        $title = 'My Cloudstream';
                    }
                    $view_data = array(
                      'api_key'=> $this->api_key,
                      'item_type'=> $this->item_type,
                      'item_id'=> $user_id,
                      'related'=> $related,
                      'html_url'=>$html_url,
                      'count'  => $this->count,
                      'title'  => $title,
                    );
                    $this->load->view('api/js_api', $view_data);
                }
                else { #'json'.
                    $items = $this->event_model->get_events_for_user($user_id, $this->count, $type='');
                    $data  = $this->event_model->display_rss_format($items, $api=TRUE);
                }
                break;

            default:
                $this->_api_error('400.4', "Error in the request URL following '$this->item_type/' - the related item '$related' (segment "._API_RELATED_SEGMENT.") is not recognised.");
                break;
        }
        // Continue for JSON format.
        if ('js'!=$this->format) {
            $data = $this->_api_process($data, $array_type);
            if ($array_type) {
                $data = array(
                    'user_id' => $user_id,
                    'self_url'=> $this->apilib->url($this->item_type, $user_id, $related),
                    'html_url'=> $html_url,
                    'total_results' => count($data),
                    'user'    => $this->apilib->_user_process((object) array('user_id'=>$user_id, 'user_name'=> $user_name)),
                    'items'   => $data,
                );
            }
            echo $this->apilib->render($data);
        }
    }

    /**
     * Controller for /api/tags/{tag}/{item_type}
     */
    public function tags() {
        #$this->item_type = 'tag';
        $this->query = $this->_required_segment('tag', $is_numeric=FALSE);
        $this->query = urldecode($this->query);

        if (!$this->uri->segment(_API_RELATED_SEGMENT)) {
            $this->_api_error('400.3', "Error, the URL segment following 'tag/' is required - it should be an item type.");
        }
        $this->item_type = $related = $this->uri->segment(_API_RELATED_SEGMENT);
        $html_url = site_url("tag/view/".urlencode($this->query)."#$related");

        if ('js'==$this->format) {
            $title = $this->input->get('title', TRUE);
            if (!$title) {
                $title = "$related tagged '$this->query'";
            }
            $view_data = array(
                'api_key'=> $this->api_key,
                'item_type'=> 'tag',
                'item_id'=> $this->query,
                'related'=> $related,
                'html_url'=>$html_url,
                'count'  => $this->count,
                'title'  => $title,
            );
            $this->load->view('api/js_api', $view_data);
        } else {

        // Will the response contain an 'items' array? 'cloud', cloudscape..?
        $array_type = 'user';

        switch ($this->item_type) {
            case 'clouds':
                $array_type = 'cloud';
                $items = $this->tag_model->get_clouds($this->query, $this->count, $offset=0); #num, offset.
            break;

            case 'cloudscapes':
                $array_type = 'cloudscape';
                $items = $this->tag_model->get_cloudscapes($this->query, $this->count);
            break;

            case 'users':
                $array_type = 'user';
                $items = $this->tag_model->get_users($this->query, $this->count);
            break;

            default:
                $this->_api_error('400.2', "Error in URL segment "._API_RELATED_SEGMENT." - item type not recognised.");
            break;
        }

        $items = $this->_api_process($items, $array_type, $has_tag=TRUE);
        $data  = array(
          'query' => $this->query,
          'total_results' => count($items),
          'item_type'     => $array_type,
          'self_url'      => $this->apilib->url('tag', $this->query, $related),
          'html_url'      => $html_url,
          'items'         => $items ? $items : null,
        );
        echo $this->apilib->render($data);
        }
    }

    /**
     * Controller for /api/search/{clouds|cloudscapes}?q={query}
     */
    public function search() {
    	if (!config_item('x_search')) {
        	show_404('/api');
        }
    	
        if (! $this->input->get('q')) {
            $this->_api_error('400', "Error, the query parameter '?q=' is required");
        }
        $this->query = $this->input->get('q');

        if (!$this->uri->segment(3)) {
            $this->_api_error('400.3', "Error, the URL segment following 'search/' is required - it should be an item type.");
        }
        $this->item_type = $related = $this->uri->segment(3);

        // Load the model late.
        $this->load->model('search_model');

		// Increase the memory limit as Zend Lucene sometimes struggles (from search controller).
	    ini_set('memory_limit','128M');

        switch ($this->item_type) {
            case 'clouds':
                $items = $this->search_model->search_for_item_type($this->query, 'cloud'); #num?
            break;

            case 'cloudscapes':
                $items = $this->search_model->search_for_item_type($this->query, 'cloudscape');
            break;

            case 'users':
                $items = $this->search_model->search_for_item_type($this->query, 'user');
            break;

            default:
                $this->_api_error('400.2', "Error in URL segment ". 3 ." - item type not not valid for search.");
            break;
        }

        // Process the search output to a 'results' array.
        $results = $this->apilib->_search_process($items, $this->item_type);

        // Nest the results in the meta-data object.
        $data  = (object) array(
          'query' => $this->query,
          'total_results' => count($items),
          'start_index'   => 0,
          'items_per_page'=> null,
          'self_url'      => $this->apilib->url('search', $this->query, $related),
          'html_url'      => site_url("search/result?q=$this->query#$related"),
          'items'         => $results ? $results : null,
        );
        $this->apilib->log('debug', "ok, ".$data->total_results);
        echo $this->apilib->render($data);
    }

    /** Controller for /api/suggest/institutions?lang={language}&q={query}
     *
     * Note, purposefully no logging to database, except on error - OK?
     */
    public function suggest() {
        if (!$this->config->item('x_api_suggest')) {
            show_404('/api/suggest');
        }
        $this->load->library('language');
        $this->lang->initialize();

        if (!$this->uri->segment(3)) {
            $this->_api_error('400.3', "Error, the URL segment following 'suggest/' is required - it should be an item type.");
        }
        $what = $related = $this->uri->segment(3);

        $term = $this->input->get('q');
        $term = trim($term);

        //No term, may not be an error - die quietly!
        if (!$term) die();

        switch ($what) {
        case 'institutions':
            $_options = $this->user_model->suggest_institutions($term, 4);
            $label = t("Suggested institutions");
        break;

        default:
            $this->_api_error('400.4', "Error, 'suggest' doesn't support the item type '$what'");
        break;
        }

        $view_data = array(
            'query'=>$term,
            'label'   =>$label,
            'pattern'=>_("!count user"),
            'pattern_plural'=>_("!count users"),
            'html_id' => '_suggestions',
            'datalist'=> $this->input->get('datalist'), //HTML5.
            'items'   => $_options,
        );
        // JSON: do a test render then output json.

        $response = $this->load->view('api/suggest_response', $view_data, $return=TRUE);
        if ($this->uri->file_extension()) { //JSON or whatever.
            $view_data['__html'] = $response;
            echo $this->apilib->render($view_data);
        }
        else { //HTML.
            echo $response;
        }
    }

}
