<?php 
/**
 *  Model file for functions related to events in cloudstreams
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Cloudstream
 */
class Event_model extends Model {
    
    function Event_model() {
        parent::Model();
    }
    
    /**
     * Add a cloudstream event 
     *
     * @param string $follow_item_type The item type e.g. 'cloud', 'cloudscape', 'user'
     * @param integer $follow_item_id The ID of the 
     * @param string $event_type The event type e.g. 'newcloud', 'newcloudscape', 
     * 'newcomment', 'newfollow'
     * @param integer $event_item_id The ID of the item that the event pertains to
     * @param integer $timestamp The time of the event as a Unix timestamp
     * @return integer The ID of the event
     */
    function add_event($follow_item_type, $follow_item_id, $event_type, $event_item_id, 
                       $timestamp = false) {
 
        $event->follow_item_type     = $follow_item_type;
        $event->follow_item_id       = $follow_item_id;
        $event->event_type    = $event_type;
        $event->event_item_id = $event_item_id;
        $event->timestamp = $timestamp;

        if (!$timestamp) {
            $event->timestamp = time();
        }

        $this->db->insert('event', $event);
        $event_id =  $this->db->insert_id();

        return $event_id;
    }
    

    /**
     * Delete all events associated with a particular item
     *
     * @param string $item_type The item type e.g. 'cloud', 'cloudscape', 'user'
     * @param integer $item_id The ID of the item
     */
    function delete_events($item_type, $item_id) {
        switch($item_type) {
            case 'cloud':
                $this->CI = &get_instance();
                $this->CI->load->model('comment_model');
                $comments = $this->CI->comment_model->get_comments($item_id);
                foreach ($comments as $comment) {
                  $this->db->delete('event', 
                                    array('event_item_id' => $comment->comment_id, 
                                    'event_type' => 'comment'));   
                }
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'cloud')); 
                $this->db->delete('event', array('follow_item_id' => $item_id, 
                                  'follow_item_type' => 'cloud'));
                break;
            case 'cloudscape':
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'cloudscape'));  
                $this->db->delete('event', array('follow_item_id' => $item_id, 
                                  'follow_item_type' => 'cloudscape')); 
                break;
            case 'comment': 
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'comment')); 
                break;
            case 'user':
                $this->db->delete('event', array('follow_item_id' => $item_id, 
                                  'follow_item_type' => 'user'));
                break;
            case 'news':
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'news')); 
                break;
            case 'news_comment':
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'news_comment')); 
                break; 
            case 'link':
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'link'));               
                break;
            case 'reference':
                $this->db->delete('event', array('event_item_id' => $item_id, 
                                  'event_type' => 'reference'));                 
                break;  
            case 'content':
                $this->db->delete('event', array('event_item_id'=>$item_id, 
                                  'event_type' => 'content'));
                break;  
            case 'embed':
                $this->db->delete('event', array('event_item_id'=>$item_id, 
                                  'event_type' => 'embed'));
                break;
        }
    }
    
    /**
     * Get all events for the site's cloudstream 
     *
     * @param integer $num Limit on number of events to get
     * @return array Array of events
     */
    function get_all($num = 20) {
        $query = $this->db->query("SELECT * FROM event WHERE event_type <> 'follow' 
                                   AND event_type <> 'new_user' 
                                   AND event_type <> 'login_attempt'
                                   AND event_type <> 'profile_edit'
                                   AND omit_from_site_cloudstream = 0 
                                   ORDER BY timestamp DESC LIMIT $num");
        return $query->result();
    }
    
    /**
     * Get all events of a particular type for the site's cloudstream.
     *
     * @param string $type The event type e.g. 'newcloud', 'newcloudscape', 
     * 'newcomment', 'newfollow'
     * @param integer $num Limit on number of events to get
     * @return array Array of events
     */
    function get_all_type($type, $num = 20) {
        $where = "";
        switch($type) {
            case 'cloud': 
            	$where = "event_type = 'cloud'"; 
            	break;
            case 'cloudscape': 
            	$where = "event_type = 'cloudscape'"; 
            	break;
            case 'comment': 
            	$where = "event_type = 'comment' OR event_type = 'news_comment'"; 
            	break;    
            case 'link': 
            	$where = "event_type = 'link'"; 
            	break; 
            case 'reference': 
            	$where = "event_type = 'reference'"; 
            	break; 
            case 'content': 
            	$where = "event_type = 'content' OR event_type = 'embed'"; 
            	break;                                             
        }
        
        $query = $this->db->query("SELECT * FROM event WHERE $where 
                                   AND omit_from_site_cloudstream = 0 
                                   ORDER BY timestamp DESC LIMIT $num");
        return $query->result();       
    }
    
    /**
     * Get the events in a user's cloudstream i.e. for items that they are following
     *
     * @param integer $user_id The ID of the user
     * @param integer $num Limit on number of events to get
     * @return array Array of events
     */
    function get_events_for_following($user_id, $num, $type = '') {
        $where = "";
        switch($type) {
            case 'cloud': $where = "AND event_type = 'cloud'"; 
                          break;
            case 'cloudscape': $where = "AND event_type = 'cloudscape'"; 
                               break;
            case 'comment': $where = "AND (event_type = 'comment' 
                                      OR event_type = 'news_comment')"; 
                              break;    
            case 'link': $where = "AND event_type = 'link'"; break; 
            case 'reference': $where = "AND event_type = 'reference'"; break; 
            case 'content': $where = "AND (event_type = 'content' 
                                      OR event_type = 'embed')"; break;                                             
        }        
        $this->CI = &get_instance();
        $this->CI->load->model('user_model');
        
        // Get the cloudscapes followed by this user and get the related events
        $cloudscapes = $this->CI->user_model->get_following_cloudscapes($user_id);
        $cloudscape_where = "1=0";
        if ($cloudscapes) {
            foreach($cloudscapes as $cloudscape) {
                $cloudscape_ids[] = $cloudscape->cloudscape_id;
            }
        
            $cloudscape_where = "follow_item_type = 'cloudscape' AND follow_item_id 
                                 IN (".implode(',', $cloudscape_ids).")";
            $cloud_query = "UNION SELECT e.* FROM event e INNER JOIN cloudscape_cloud c 
                            ON e.follow_item_id = c.cloud_id 
                           WHERE (e.follow_item_type = 'cloud' AND c.cloudscape_id 
                           IN (".implode(',', $cloudscape_ids).")) $where";
        }

        // Get the users followed by this user and the related events 
        $users = $this->CI->user_model->get_following($user_id);
        $user_where = "1=0";
        if ($users) {
            
            foreach($users as $user) {
                $user_ids[] = $user->followed_user_id;
            }
            $user_where = "follow_item_type = 'user' AND follow_item_id 
            IN (".implode(',', $user_ids).")";
        }
        
        // Get the clouds followed by this user and get the related events 
        $clouds = $this->CI->user_model->get_following_clouds($user_id);
        $cloud_where = "1=0";
        if ($clouds) {
            foreach($clouds as $cloud) {
                $cloud_ids[] = $cloud->cloud_id;
            }
        
            $cloud_query2 = "UNION SELECT e.* FROM event e 
                           WHERE (e.follow_item_type = 'cloud' AND e.follow_item_id 
                           IN (".implode(',', $cloud_ids).")) $where";
        }
        // Combine the two queries 
        $query = $this->db->query("SELECT * FROM event WHERE (($cloudscape_where) 
                                   OR ($user_where)) $where
        $cloud_query $cloud_query2 ORDER BY timestamp DESC LIMIT $num");
        return $query->result();
    }    
    
    /**
     * Get all events in the admin cloudstream 
     *
     * @param integer $num Limit on number of events to get
     * @return array Array of events
     */
    function get_events_for_admin($num = 20) {
        $query = $this->db->query("SELECT * FROM event  ORDER BY timestamp DESC LIMIT $num");
        return $query->result();
    }    
 

    /**
     * Get all the events in a user's cloudstream i.e. events for actions by the user
     *
     * @param integer $user_id The ID of the user
     * @param integer $num Limit on number of events to get
     * @param string $type Filter by a particular event type e.g. 'newcloud', 
     * 'newcloudscape', 
     * 'newcomment', 'newfollow'. , If empty, show all events
     * @return array Array of events
     */
    function get_events_for_user($user_id, $num = 50, $type='') {
        $where = "";
        
        switch($type) {
            case 'cloud': 
            	$where = "AND event_type = 'cloud'"; 
            	break;
            case 'cloudscape': 
            	$where = "AND event_type = 'cloudscape'"; 
            	break;
            case 'comment': 
            	$where = "AND (event_type = 'comment' OR event_type = 'news_comment')"; 
            	break;    
            case 'link': 
            	$where = "AND event_type = 'link'"; 
            	break; 
            case 'reference': 
            	$where = "AND event_type = 'reference'"; 
            	break; 
            case 'content': 
            	$where = "AND (event_type = 'content' OR event_type = 'embed')"; 
                break;                                             
        }          
        if (is_numeric($user_id)) {
            $query = $this->db->query("SELECT * FROM event WHERE event_type <> 'follow' 
                                       AND follow_item_type = 'user' 
                                       AND follow_item_id = $user_id $where 
                                       ORDER BY timestamp DESC LIMIT $num");
            return $query->result();   
        } else {
            return false;
        }
    }
    
    /**
     * Get the events for a cloudscape's cloudstream 
     *
     * @param integer $cloudscape_id The ID of the cloudscapte
     * @param integer $num Limit on number of events to get
     * @return array Array of events
     */
    function get_events_for_cloudscape($cloudscape_id, $num, $type = '') {
        $this->CI = &get_instance();
        $where = "";
        switch($type) {
            case 'cloud':       $where = "AND event_type = 'cloud'"; break;
            case 'cloudscape':  $where = "AND event_type = 'cloudscape'"; break;
            case 'comment':     $where = "AND (event_type = 'comment' 
                                      OR event_type = 'news_comment')"; break;    
            case 'link':        $where = "AND event_type = 'link'"; break; 
            case 'reference':   $where = "AND event_type = 'reference'"; break; 
            case 'content':     $where = "AND (event_type = 'content' OR event_type = 'embed')";
             break;                                             
        } 
        
        $cloudscape_where = "follow_item_type = 'cloudscape' 
                             AND follow_item_id = $cloudscape_id";
                             
        $cloud_query      = "UNION SELECT e.* FROM event e INNER JOIN cloudscape_cloud c 
                              ON e.follow_item_id = c.cloud_id WHERE (e.follow_item_type = 'cloud' 
                              AND c.cloudscape_id = $cloudscape_id) $where";


        $query = $this->db->query("SELECT * FROM event WHERE ($cloudscape_where) $where
        $cloud_query ORDER BY timestamp DESC LIMIT $num");
        
        return $query->result();
    }
    
    
    function event_category($event_type) {
        $category = '';
        
        switch($event_type) {
            case 'cloud': 
            	$category = 'cloud'; 
            	break;
            case 'cloudscape': 
            	$category = 'cloudscape'; 
            	break;
            case 'comment':
            	$category = 'comment'; 
            	break;
            case 'news': 
            	$category = 'comment'; 
            	break;
            case 'follow': 
            	$category = 'user'; 
            	break;
            case 'news_comment': 
            	$category = 'comment'; 
            	break;
            case 'new_user': 
            	$category = 'user'; 
            	break;
            case 'link': 
            	$category = 'link'; 
            	break;
            case 'reference': 
            	$category = 'reference'; 
            	break;
            case 'content': 
            	$category = 'extra'; 
            	break;
            case 'embed': 
            	$category = 'extra'; 
            	break;   
            case 'profile_edit':
            	$category = 'user'; 
            	break;
            case 'login_attempt':
            	$category = 'user'; 
            	break; 
        }
        
         return $category;
    }

    /**
     * Turn an event into an HTML string that can be displayed in a cloudstream 
     *
     * @param obejct $event Details of the event
     * @return string The HTML string
     */
    function to_string($event, $simple = false) {
        
        $this->load->helper('format');
        $this->CI = &get_instance();
        $attr_author = array('rel'=>'author'); // Commonly used HTML attributes.

        switch ($event->event_type) {
            case 'cloud':            
                if ($event->follow_item_type == 'user') {
                    // New cloud created by user
                    $cloud_id = $event->event_item_id;
                    $this->CI->load->model('cloud_model');
                    $cloud = $this->CI->cloud_model->get_cloud($cloud_id);
                    $string = anchor("cloud/view/$cloud_id", $cloud->title).'<br />'; 
                    if ($simple) {
                        /*@i18n: Simplified/modified logic. */
                        ///Translators: Cloudstreams - short and long event messages.
                        // Add 'rel' for semantics/styling, http://whatwg.org/specs/web-apps/current-work/multipage/links.html#linkTypes
                        $string .= '<em>'.t("created by !person",
                            array('!person' => anchor('user/view/'.$cloud->id, 
                                  $cloud->fullname, $attr_author))).'</em>'; 
                    } else {
                        $string .= '<em>'.t("new cloud created by !person",
                            array('!person' => anchor('user/view/'.$cloud->id, 
                                   $cloud->fullname, $attr_author))).'</em>';
                    }
                } elseif ($event->follow_item_type = 'cloudscape') {
                    // Cloud added to a cloudscape 
                    $cloud_id = $event->event_item_id;
                    $cloudscape_id = $event->follow_item_id;
                    $this->CI->load->model('cloud_model');
                    $cloud = $this->CI->cloud_model->get_cloud($cloud_id);
                    if (!$cloud->user_id) {
                      return false;
                    } 
                    else {
                      $this->CI->load->model('cloudscape_model'); 
                      $cloudscape = $this->CI->cloudscape_model->get_cloudscape($cloudscape_id);
                      $user = $this->CI->cloudscape_model->get_cloud_added_user($cloud_id, 
                                                                 $cloudscape_id);
                      $string = anchor('cloud/view/'.$cloud->cloud_id, $cloud->title).'<br />'; 
                      if ($user) {
                          $string .= '<em>'.t("cloud added to the cloudscape !title by !person", array(
                              '!person'=> anchor('user/view/'.$user->user_id,
                                      $user->fullname, $attr_author),
                              '!title' => anchor('cloudscape/view/'.
                                      $cloudscape->cloudscape_id, 
                                      $cloudscape->title))).'</em>';
                      } else {
                          $string .= '<em>'.t("cloud added to the cloudscape !title",
                                  array('!title' => 
                                  anchor('cloudscape/view/'.
                                      $cloudscape->cloudscape_id, 
                                      $cloudscape->title))).'</em>';
                      }
                  }
                }
                break; 

           case 'cloudscape':
                // New cloudscape created
                $this->CI->load->model('cloudscape_model');
                $cloudscape_id = $event->event_item_id; 
                $cloudscape = $this->CI->cloudscape_model->get_cloudscape($cloudscape_id); 
                $string = anchor("cloudscape/view/$cloudscape_id", $cloudscape->title).
                                '<br />';
                if ($simple) {
                    $string .= '<em>'.t("created by !person",
                                      array('!person' => 
                                          anchor('user/view/'.$cloudscape->user_id, 
                                          $cloudscape->fullname,
                                              $attr_author))).'</em>';
                } else {
                    $string .= '<em>'.t("new cloudscape created by !person",
                                      array('!person' => 
                                          anchor('user/view/'.$cloudscape->user_id, 
                                          $cloudscape->fullname, 
                                              $attr_author))).'</em>';
                }
                break; // Always break last - defensive.

            case 'comment': 
                // New comment on a cloud
                $this->CI->load->model('comment_model');
                $comment_id         = $event->event_item_id; 
                $comment            = $this->CI->comment_model->get_comment($comment_id);
                if (!$comment) {
                  return false;
                }
                else {
                  $truncated_comment  = truncate_content(strip_tags($comment->body));
                  $string             = '<strong>'.'"'.$truncated_comment.'</strong><br />';
                  
                  if ($simple) {
                      $string .= '<em>'.t("added to !cloud by !person",
                          array('!cloud' => anchor('cloud/view/'.$comment->cloud_id, 
                                           $comment->cloud_title),
                                '!person'=> anchor('user/view/'.$comment->user_id, 
                                           $comment->fullname, 
                                           $attr_author))).'</em>';
                  } else {
                      $string .= '<em>'.t("new comment on the cloud !cloud by !person",
                                   array('!cloud' => 
                                       anchor('cloud/view/'.$comment->cloud_id, 
                                           $comment->cloud_title),
                                       '!person'=> 
                                       anchor('user/view/'.$comment->user_id,
                                       $comment->fullname, $attr_author))).'</em>';
                  }
                }
                break;

            case 'news':
                // New blog post
                $this->CI->load->model('blog_model');
                $post_id = $event->follow_item_id;
                $news = $this->CI->blog_model->get_blog_post($post_id);
                $string = anchor("blog/view/$post_id", $news->title).'<br />'; 
                $string .= '<em>'.t("New blog post by !person",
                                   array('!person' => 
                                       anchor('user/view/'.$news->user_id, 
                                           $news->fullname, $attr_author))).'</em>'; 
                break;

            case 'follow':
                // New follow
                $followed_id  = $event->event_item_id;
                $following_id = $event->follow_item_id;
                $this->CI->load->model('user_model');
                $followed_user  = $this->CI->user_model->get_user($followed_id);
                $following_user = $this->CI->user_model->get_user($following_id);
                $string = '<em>'.t("!following is now following !followed",
                                   array('!following'=> 
                                       anchor('user/view/'.$following_user->user_id, 
                                           $following_user->fullname, $attr_author),
                                      '!followed' => 
                                       anchor('user/view/'.$followed_user->user_id, 
                                           $followed_user->fullname))).'</em>';
                break;

            case 'news_comment':
                $this->CI->load->model('blog_model');
                $this->CI->load->model('user_model');
                $comment_id        = $event->event_item_id;
                $comment           = $this->CI->blog_model->get_comment($comment_id);
                $truncated_comment = truncate_content(strip_tags($comment->body));
                $user = $this->CI->user_model->get_user($comment->user_id);
                $string = '<strong>'.'"'.$truncated_comment.'</strong><br />';
                $string .= '<em>'.t("!person commenting on the blog post !title",
                                    array('!person'=>
                                        anchor('user/view/'.$comment->user_id,
                                        $comment->fullname, $attr_author),
                                    '!title' =>
                                        anchor('blog/view/'.$comment->post_id,
                                        $comment->news_title))).'</em>';
                break;

            case 'new_user':
                $this->CI->load->model('user_model');
                $user_id = $event->event_item_id;
                $user = $this->CI->user_model->get_user($user_id);
                $string = '<em>'.t("!person has registered on !site-name!",
                                   array('!person' => 
                                         anchor('user/view/'.$user_id, $user->fullname) )).'</em>';
                break;

            case 'link': 
                $this->CI->load->model('cloud_model');
                $this->CI->load->model('link_model');
                $link_id = $event->event_item_id;
                $link = $this->CI->link_model->get_link($link_id);
                if (!$link) {
                  return false;
                }
                else {
                  $cloud = $this->CI->cloud_model->get_cloud($link->cloud_id);
  
                  $string = '';
                  if ($link->title) {
                      $string = '<strong>'.$link->title.' </strong><br />';
                  }
  
                 if ($simple) {
                      $string .= '<em>'.t("added to !title by !person",
                          array('!person'=> 
                                 anchor('user/view/'.$link->user_id, 
                                        $link->fullname, $attr_author),
                                 '!title' => 
                                  anchor('cloud/view/'.$cloud->cloud_id, 
                                  $cloud->title))).'</em>';
                 } else {
                      $string .= '<em>'.t("new link on !title added by !person",
                                         array('!person'=> 
                                             anchor('user/view/'.$link->user_id, 
                                                    $link->fullname, 
                                                    $attr_author),
                                               '!title' => 
                                             anchor('cloud/view/'.$cloud->cloud_id, 
                                                    $cloud->title))).'</em>';
                 }
               }
               break;

            case 'reference': 
                $this->CI->load->model('cloud_model');
                $reference_id = $event->event_item_id;
                $reference = $this->CI->cloud_model->get_reference($reference_id);
                if (!$reference) {
                  return false;
                }
                else {
                  $cloud = $this->CI->cloud_model->get_cloud($reference->cloud_id);
                  $truncated_reference = truncate_content(
                                         strip_tags($reference->reference_text));
  
                  $string = '<strong>'.'"'.$truncated_reference.'..."</em> </strong><br />';
                  if ($simple) {
                      $string .= '<em>'.t("on the cloud !title by !person",
                                        array('!person'=> 
                                            anchor('user/view/'.$reference->user_id, 
                                                   $reference->fullname, 
                                                   $attr_author),
                                            '!title' => 
                                            anchor('cloud/view/'.$cloud->cloud_id, 
                                                   $cloud->title))).'</em>';
                  } else {
                      $string .= '<em>'.t("reference added to the cloud !title by !person",
                                       array('!person'=> 
                                            anchor('user/view/'.$reference->user_id, 
                                                   $reference->fullname, 
                                                   $attr_author),
                                            '!title' => 
                                            anchor('cloud/view/'.$cloud->cloud_id, 
                                                   $cloud->title))).'</em>';
                  }
                }
                break;

            case 'content': 
                $this->CI->load->model('cloud_model');
                $this->CI->load->model('content_model');
                $content_id = $event->event_item_id;
                $content = $this->CI->content_model->get_content_item($content_id);
                if (!$content) {
                  return false;
                }
                else {
                  $truncated_content = truncate_content(strip_tags($content->body));
                  $cloud = $this->CI->cloud_model->get_cloud($content->cloud_id);
                  $string = '<strong>'.$truncated_content.'..."</strong><br />'; 
                  if ($simple) {
                      $string .= '<em>'.t("on !title by !person",
                                         array('!person'=> 
                                             anchor('user/view/'.$content->user_id, 
                                                    $content->fullname, 
                                                    $attr_author),
                                             '!title' => 
                                             anchor('cloud/view/'.$cloud->cloud_id, 
                                                     $cloud->title))).'</em>';
                  } else {
                      $string .= '<em>'.t("new content on !title by !person",
                                           array('!person'=> 
                                             anchor('user/view/'.$content->user_id, 
                                                 $content->fullname, 
                                                 $attr_author),
                                             '!title' => 
                                             anchor('cloud/view/'.$cloud->cloud_id, 
                                                      $cloud->title))).'</em>';
                  }
                }
                break;

            case 'embed':
                $this->CI->load->model('cloud_model');
                $this->CI->load->model('embed_model');
                $embed_id = $event->event_item_id;
                $embed = $this->CI->embed_model->get_embed($embed_id);
                if (!$embed) {
                  return false;
                }
                else {
                  $cloud = $this->CI->cloud_model->get_cloud($embed->cloud_id);
                  $string = '';
                  if ($embed->title) {
                      $string .= '<strong>'.$embed->title.'</strong><br />';
                  }
                  if ($simple) {
                    $string .= '<em>'.t("new embedded content added to the cloud !title by !person",
                                         array('!person'=> 
                                             anchor('user/view/'.$embed->user_id, 
                                                  $embed->fullname, 
                                                      $attr_author),
                                             '!title' => 
                                             anchor('cloud/view/'.$cloud->cloud_id, 
                                                      $cloud->title))).'</em>';
                  } else {
                    $string .= '<em>'.t("new embedded content added to the cloud !title by !person",
                        array('!person'=> anchor('user/view/'.$embed->user_id, 
                                                  $embed->fullname, 
                                                  $attr_author),
                                                 '!title' =>
                                                 anchor('cloud/view/'.$cloud->cloud_id, 
                              $cloud->title))).'</em>';
                  }
                }
                break;
                
            case 'profile_edit': 
                $this->CI->load->model('user_model');
                $user_id = $event->event_item_id;
                $user = $this->CI->user_model->get_user($user_id);
                $string = '<em>'.t("!person editted their profile",
                                   array('!person' => 
                                         anchor('user/view/'.$user_id, $user->fullname) )).'</em>';
                break;
            case 'login_attempt': 
                $this->CI->load->model('user_model');
                $user_id = $event->event_item_id;
                $user = $this->CI->user_model->get_user($user_id);
                $string = '<em>'.t("!person made an unsuccessful login attempt",
                                   array('!person' => 
                                         anchor('user/view/'.$user_id, $user->fullname) )).'</em>';
                break;
                break;
        }

        return $string;
    }
    
	/**
	 * Turns an array of events into a suitable format for display on the site. 
	 * Strictly this code ought to be handled by views somehow. 
	 *
	 * @param array $events The array of cloudstream events 
	 * @param boolean $simple Use the 'simple' format for events
	 * @return string The formatted events 
	 */
    function display_format($events, $simple = false) {
        $this->load->helper('format');
        $last_event_item_id = false;
        $last_event_type    = false;
        $new_events = array();
        if ($events) {
            foreach ($events as $event) {
            	// If somebody is following e.g. both a cloud and user that the item
            	// pertains to then don't display the item twice 
                if ($event->event_item_id != $last_event_item_id || 
                    $event->event_type != $last_event_type) {
                    $new_event    = $this->event_model->to_string($event, $simple);    
                    if ($new_event) {
                      $new_events[] = '<li class="'.$this->event_category($event->event_type).
                                      '">'.$new_event.' <em>'.time_ago($event->timestamp).'</em></li>';
                                    }
                    $last_event_item_id = $event->event_item_id;
                    $last_event_type    = $event->event_type;
                }
            }
        }
        return $new_events;
    }

    /**
     * Format the array of events so that it's suitable for an RSS feed, or the API.
     *
	 * @param array $events The array of cloudstream events 
	 * @return string The formatted events 
     */
    function display_rss_format($events, $api=FALSE) {
        $new_events = NULL;
        if ($events) {
            $last_event_item_id = NULL;
            foreach($events as $event) {
            	// If somebody is following e.g. both a cloud and user that the item
            	// pertains to then don't display the item twice 
                if ($event->event_item_id != $last_event_item_id || 
                    $event->event_type != $last_event_type) {
                    $description = $this->event_model->to_string($event);
                    // Strip the links to get the title
                    $modified_description = str_replace('</a>', '', $description);
                    $modified_description = preg_replace('/<a[^>]+href[^>]+>/', '', 
                                                         $modified_description);
                    
                    $new_event->title = $modified_description;
                    
                    if ($api) { //API-specific.
                        $new_event->title = strip_tags($new_event->title);
                        $new_event->item_type = $event->event_type;
                        $new_event->item_id= $event->event_item_id;
                        $new_event->status = str_replace('<br />', '<small>', 
                        $description).'</small>';
                        $new_event->created= $event->timestamp;
                        $new_event->html_url = 
                                        site_url("#$event->event_type-$event->event_item_id");
                    } else { //RSS-specific.
                        $new_event->description = $description;
                        $new_event->timestamp= $event->timestamp;
                        $new_event->category = $event->event_type;
                        $new_event->link = 
                                        site_url("#$event->event_type-$event->event_item_id");
                    }
                    
                    switch ($event->event_type) {
                    	case 'cloud':
                      	case 'cloudscape':  # Drop-through.
                      		if ($api) {
                          		$new_event->html_url = 
                                    site_url("$event->event_type/view/$event->event_item_id");
                        	}
                        	$new_event->link = 
                                    base_url("$event->event_type/view/$event->event_item_id");
                      		break;
                    }
                    
                    $new_events[] = $new_event;
                    unset($new_event);
                    $last_event_item_id = $event->event_item_id;
                    $last_event_type = $event->event_type;
                }  
            }
        }
        
        return $new_events;
    }    

}