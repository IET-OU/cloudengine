<?php
/**
 * Library for API-related functions
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Format
 */
class Api_lib {

    // The CodeIgniter object.
    protected $CI = NULL;

    // A username derived when we validate the API key.
    protected $api_client = NULL;

    public function Api_lib() {
        $this->CI =& get_instance();
        $this->CI->load->model('api_client_model');
    }

    /** 
     * Return a required GET parameter, or URL segment (when we hadn't finalised URL format) 
     * - rename.
     * @param string  $name The name of the GET parameter
     * @param boolean $is_integer Flag whether to check for integer/numeric value, 
     * default TRUE.
     * @param integer $segment The segment number, default 3.
     * @return mixed The value.
     */
    public function required_get($name, $is_integer = TRUE, $segment = 3) {
        $term = NULL;
        if ($this->CI->uri->segment($segment)) {
            $term = $this->CI->uri->segment($segment);
        } else {
            $this->_api_error("400.3", "Error, the URL segment following '".
                                        str_replace('_id', '', $name)."/' is required.");
        }
        if ($is_integer && !is_numeric($term)) {
            $this->_api_error("400.3", "Error, the URL segment following '".
                                       str_replace('_id', '', $name).
                                       "/' (segment 3) should be a numeric ID.");
        }
        return $term;
    }

    /** 
     * Finally create the JSON (or other format) output.
     * @param mixed $response The array or object to output.
     * @return string A JSON-encoded object.
     */
    public function render($response) {
        // Prepend the API version - is casting/ array_merge costly?
        $response = array_merge(array('api_version'=>_API_VERSION), (array) $response);

        $view_data = array(
          'response'=> $response,
          'format'  => $this->CI->_api_format(),
          'json_callback' => $this->CI->_json_callback(),
        );
        return $this->CI->load->view('api/api_render', $view_data);
    }

    /** 
     * Log an API event, currently using CI functionality.
     */
    public function log($level='error', $message, $php_error = FALSE) {
        $this->CI->load->library('user_agent');
        $ip = $_SERVER['SERVER_ADDR'];
        $ref= $this->CI->agent->referrer();    #['HTTP_REFERER']
        $ua = $this->CI->agent->agent_string();#['HTTP_USER_AGENT']
        $request = $this->CI->uri->uri_string().'?'.$_SERVER['QUERY_STRING'];
        // Play nice if no API clients are in DB (api keys may be configured to not required).
        $user_name = isset($this->api_client->user_name) ? 
                          $this->api_client->user_name : '[no_user]';
        $client_id = isset($this->api_client->client_id) ? 
        					$this->api_client->client_id :NULL;
        log_message($level, "Api: $message, $request -- {$user_name}, $ip, $ref, $ua", 
                    $php_error);
        return $this->CI->api_client_model->log(
            $client_id, $level, $message, $request, $ua, $ref);
    }

    /** 
     * Output an error message, in the requested output 'format'.
     * @param string $code Error code/number with format 'HTTP_ERROR.OPTIONAL_SUB_ERROR', 
     * eg. '400.3'.
     * @param string $message Explanation of the error.
     * @return
     */
    public function _api_error($code, $message = NULL) {
        $data = array(
          'stat' => 'fail',
          'code'   => $code,
          'message'=> $message,
          'request'=> $this->CI->uri->uri_string().'?'.$_SERVER['QUERY_STRING'],
        );

        $this->log('error', "$code, $message"); #__CLASS__

        // For '400.1'/ 503.1 PHP puts '400 Bad Request', etc. - all looking correct!
        header("HTTP/1.0 $code", TRUE, $code);
        @header("Content-Disposition: inline; filename=cloudworks-api-error.txt");
        $format = $this->CI->_api_format();
        if ('js' == $format) {
          $this->CI->load->view('api/js_api', array('error' => $data));
        } else {
          try {
            $this->CI->load->view('api/api_render', array('response'=>$data, 
                                  'format'=>$format));
          } catch(Exception $e){ var_dump($e); }
          exit;
        }
    }

    /** 
     * Return a URL to call the API.
     * @param string $item_type The primary 'object', eg. 'clouds', 'tags', 'search'.
     * @param string $term Either a numeric identifier (eg. cloud ID), a tag-name or search 
     * query.
     * @param string $related A related item name, eg. 'clouds', 'favourites'..
     * @param string $format Output format, defaults to 'json'.
     * @param string $options Optional '&key=value..' to append to the URL.
     * @return string API URL.
     */
    public function url($item_type, $term, $related = NULL, $format = null, $options = null) {
        if (!$format) $format= $this->CI->_api_format();
        $path = "$item_type/$term";
        $params = null;
        switch ($item_type) {
            case 'cloud':  #Drop-through.
            case 'cloudscape':
            case 'user':
              // Convert singular to plural.
              $path = "{$item_type}s/$term";
            break;

            case 'tag':
              $path = "{$item_type}s/".urlencode($term);
            break;
            case 'tags':
              $path = "{$item_type}/".urlencode($term);
            break;

            case 'search':
            case 'suggest':
              $path = $item_type;
              $params = "q=".urlencode($term);
            break;

            default: #Error.
            break;
        }
        if ($related) $path .= "/$related";
        if ($params || $options) {
            $params = "?$params&$options";
        }
        //This was the helper 'site_url'
        return $this->CI->config->site_url("api/$path.$format$params"); 
    }

    /** 
     * Generate a URL to an uploaded resource, eg. a user thumbnail picture
     * @param string $filename Not very clean to check the filename here.
     * @param int    $item_id
     * @param string $item_type Currently either 'user' or 'cloudscape'.
     * @return string URL.
     */
    protected function resource_url($filename, $item_id, $item_type = 'user') {
        if (!$filename || !$item_id) return null;

        switch ($item_type) {
            case 'user': 
                $url = '/image/user_32/'.$item_id;
                break;
            case 'cloudscape':
                $url = '/image/cloudscape/'.$item_id;
                break;
            default:
            return null;
        }
        return $this->CI->config->site_url($url);
    }

    /** 
     * Check if an API key is required, and if it's valid.
     * @param string $api_key A key
     * @return string A username to match against the 'user' table, or '_example_'.
     */
    public function api_key_valid($api_key) {
        $this->api_client = $this->CI->api_client_model->is_valid_key($api_key);
        if ($this->api_client) {
            return $this->api_client->user_name;
        }
        //ELSE.
        $required = $this->CI->config->item('x_api_key_required') && 
                    'suggest' != $this->CI->uri->segment(2);
        if ($required && !$api_key) {
            $this->_api_error("403.1", "Error, 'api_key' is a required parameter.");
        }
        #401.2 Unauthorized.
        if ($required && !$api_client) {
            $this->_api_error('403.2', "Error, the 'api_key' is invalid.");
        }
        return FALSE;
    }

    /** 
     * Return an array of tag name-url 'objects'.
     * @param array $tags
     * @param string $related One of 'clouds', 'cloudscapes', 'users' - note the 's'.
     * @return array
     */
    public function _tags_process($tags, $related) {
        $response = null;
        if (isset($tags) && $tags) {
          foreach ($tags as $tag) {
            // $tag was a string, now an object 
            $tag_name = is_object($tag) ? $tag->tag : $tag;
            $response[] = array(
              'name'   => $tag_name,
              'api_url'=> $this->url('tags', $tag_name, $related), 
                                                               //Can also expose 'html_url'.
              'tag_id' => isset($tag->tag_id) ? $tag->tag_id : NULL,
            );
          }
        }
        return $response;
    }

    /** 
     * Return a date, formatted for API.
     * @param integer $timestamp Unix time stamp
     * @return string ISO formatted date, eg. 2010-06-07T00:00:00:Z..
     */
    public function _date($timestamp) {
        if (false!==strpos($timestamp, '-')) {
        	return $timestamp; #Double-dating?!
        }
        return $timestamp ? date('c', $timestamp) : null;
    }

    /** 
     * Process a raw generic array to an array suitable for output.
     * @param array $items The raw input
     * @param string $array_type The item type(s). One of 'cloud', cloudscape, user, 
     * stream or mixed.
     * @return array Result array.
     */
    public function _array_process($items, $array_type, $has_tag = FALSE) {

        // Deal with streams as a special 'mixed' case.
        if ('stream'==$array_type) {
            foreach ($items as $item) {
                $item->created = $this->_date($item->created);
            }
            return $items;
        }

        // Everything but 'streams'.
        $response = array();
        foreach ($items as $k => $item) {
            $current_type = $array_type;
            if ('mixed'==$array_type) {
                //'user/favourites'
                $current_type = $item->item_type;
            }

            if ('cloud'==$current_type) {
                //'user/favourites' have an item_id, but no cloud_id.
                $cloud_id = isset($item->cloud_id) ? $item->cloud_id : $item->item_id;
                $response[] = array(
                    'cloud_id'=> $cloud_id,
                    'api_url' => $this->url('cloud', $cloud_id),
                    'html_url'=> site_url("cloud/view/$cloud_id"),
                );
            }
            elseif ('cloudscape'==$current_type) {
                $cloudscape_id = isset($item->cloudscape_id) ? 
                                     $item->cloudscape_id : $item->item_id;
                $response[] = array(
                    'cloudscape_id'=> $cloudscape_id,
                    'api_url' => $this->url('cloudscape', $cloudscape_id),
                    'html_url'=> site_url("cloudscape/view/$cloudscape_id"),
                );
            }
            elseif ('comment'==$current_type) {
                $response[] = array(
                    'comment_id'=> $item->comment_id,
                    'body'    => $item->body,
                    'created' => $this->_date($item->timestamp),
                );
            }
            if ('cloud'==$current_type || 'cloudscape'==$current_type) {
                $response[$k]['title']  = $item->title;
                $response[$k]['summary']= isset($item->summary) ? $item->summary : null;
                if (isset($item->total_comments)) { //'clouds/active'
                    $response[$k]['total_comments']= $item->total_comments;
                }

                // Is a nested 'user' required for user/cloudscapes?
                if (isset($item->user_id)) {
                  $response[$k]['user'] = array(
                    'user_id' => $item->user_id,
                    'api_url' => $this->url('user', $item->user_id),
                    'html_url'=> site_url("user/view/$item->user_id"),
                  );
                }
            } else { 
                $user_id = $item->user_id ? $item->user_id : $item->id; 
                $response[] = array(
                    'user_id'=> $user_id,
                    'name' => isset($item->fullname) ? $item->fullname : null,
                    'api_url'=> $this->url('user', $user_id),
                    'html_url' => site_url("user/view/$user_id"),
                );
            }
            if ($has_tag) {
                $response[$k]['tag'] = $item->tag;
            }
        }
        return $response;
    }

    /** 
     * Process the search output to a 'results' array for output.
     * @param array  $items The raw search results.
     * @param string $array_type The requested item type, eg. 'clouds'.
     * @return array Results array.
     */
    public function _search_process($items, $array_type) {
        $results = array();
        foreach ($items as $r) {
          if ($r->item_type) { 
            $item_type = $r->item_type;
            $item_id   = $r->{$r->item_type.'_id'};
          } else {
            // This is a backup, $array_type should be singular.
            $item_type = $array_type;
            if (preg_match('#\/view\/(\d*)#', $r->url, $matches)) {
                $item_id = $matches[1];
            }
          }
          $results[] = array(
            'item_id'  => $item_id,
            'item_type'=> $item_type,
            'title'    => trim(str_replace(config_item('site_name')." -", '', $r->title)),
            'html_url' => $r->url,
            'api_url'  => $this->url($item_type, $item_id),
          );
        }
        return $results;
    }

    /** 
     * Process a cloud object.
     * @param object $data Raw object containing cloud data.
     * @param array  $response The array for output.
     * @return array The response array.
     */
    public function _cloud_process($data, $response) {

        $response['html_url'] = site_url("cloud/view/$data->cloud_id");
        $response['cloud_id'] = $data->cloud_id;
        $response['primary_url'] = $data->primary_url;
        #$response['legacy_url']  = $data->url;
        if ($data->call_deadline) {
            $response['call_deadline']= $this->_date($data->call_deadline);
        }
        if (isset($data->totals)) {
          foreach ($data->totals as $name => $count) {
            $response["total_$name"] = $count;
          }
        }

        if (count($data->extra_content) <= 0) {
            $response['extra_content'] = null;
        } else {
            foreach ($data->extra_content as $k => $extra) {
                $response['extra_content'][] = array(
                    'content_id'=> $extra->content_id,
                    'body'      => $extra->body,
                    'user_id'   => $extra->user_id,
                    'user'    => $this->_user_process($extra),
                    'created' => $this->_date($extra->created),
                    #'modified'=> $this->_date($extra->modified),
                );
            }
        }

        if (count($data->embeds) <= 0) {
            $response['embeds'] = null;
        } else {
            foreach ($data->embeds as $k => $extra) {
                $response['embeds'][] = array(
                    'embed_id'=> $extra->embed_id,
                    'html_url'=> $extra->url,
                    'title'   => $extra->title,
                    'user_id' => $extra->user_id,
                    'created' => $this->_date($extra->timestamp),
                );
            }
        }

        if (count($data->links) <= 0) {
            $response['links'] = null;
        } else {
            foreach ($data->links as $k => $extra) {
                $response['links'][] = array(
                    'link_id' => $extra->link_id,
                    'link_url'=> $extra->url,
                    'title'   => $extra->title,
                    'user_id' => $extra->user_id,
                    'created' => $this->_date($extra->timestamp),
                );
            }
        }

        if (count($data->references) <= 0) {
            $response['references'] = null;
        } else {
            foreach ($data->references as $k => $extra) {
                $response['references'][] = array(
                    'reference_id' => $extra->reference_id,
                    'text'    => $extra->reference_text,
                    'user_id' => $extra->user_id,
                    'created' => $this->_date($extra->timestamp),
                );
            }
        }
        return $response;
    }

    /** 
     * Process a cloudscape object.
     * @param object $data Raw object containing cloudscape data.
     * @param array  $response The array for output.
     * @return array The response array.
     */
    public function _cloudscape_process($data, $response) {

        $response = array_merge($response,
            array(
              'html_url' => site_url("cloudscape/view/$data->cloudscape_id"),
              'cloudscape_id' => $data->cloudscape_id,
              'twitter_tag' => $data->twitter_tag,
              'image' => array(
                'thumbnail_url'   => $this->resource_url($data->image_path, 
                                          $data->cloudscape_id, 'cloudscape'),
                'attribution_name'=> $data->image_attr_name,
                'attribution_url' => $data->image_attr_link,
              ),
            )
        );
        if (isset($data->is_event) && $data->is_event) {
            $response['event'] = array(
                'location'  => $data->location,
                'start_date'=> $this->_date($data->start_date),
                'end_date'  => $this->_date($data->end_date),
            );
        } else {
            $response['event'] = null;
        }
        if (isset($data->totals)) {
          foreach ($data->totals as $name => $count) {
            $response["total_$name"] = $count;
          }
        }
        return $response;
    }

    /** 
     * Process a user with varying degrees of richness.
     * @param object  $data Raw data.
     * @param boolean $rich Whether to do a basic or rich response.
     * @return array
     */
    public function _user_process($data, $rich=FALSE) {
        $user = NULL;
        $user_id = isset($data->user_id) ? $data->user_id : $data->id; 
        if ($rich && isset($data->fullname)) {
            // Don't expose 'last_visit' date, etc., as it's not visible on site.
            $user = array(
                'user_id' => $user_id,
                'name'    => $data->fullname,
                'html_url'=> site_url("user/view/$user_id"),
                'api_url' => $this->url('user', $user_id),
                'description'=> $data->description,
                'home_url'   => $data->homepage,
                'institution'=> $data->institution,
                'department' => $data->department,
                'twitter_name'=>$data->twitter_username,
            );

            if (isset($data->picture)) {
                $user['thumbnail_url'] = $this->resource_url($data->picture, $user_id, 
                                                             'user');
            }
            if ('user'==$this->CI->_item_type()) { 
                $user['tags'] = $this->_tags_process($data->tags, 'users');
            }
            foreach ($data->totals as $name => $count) {
                $user["total_$name"] = $count;
            }
        } elseif (!$rich && isset($user_id)) {
            $user['user_id'] = $user_id;
            if (isset($data->fullname)) {
              $user['name'] = $data->fullname;
            }
            $user['html_url']= site_url("user/view/$user_id");
            $user['api_url'] = $this->url('user', $user_id);
        }
        return $user;
    }
}