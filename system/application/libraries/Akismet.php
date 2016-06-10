<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Akismet Class
 *
 * Protect your CodeIgniter applications from comment spam.
 *
 * @package         CodeIgniter
 * @subpackage      Libraries
 * @category        Libraries
 * @author          Elliot Haughin
 * @link
 */

class Akismet {

    private $_api_key = '';
    private $_blog_url = '';
    private $_valid_key = FALSE;
    private $_base_url = 'rest.akismet.com/1.1/';
    private $_proxy = '';

    private static $_curl_opts = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'codeigniter-akismet-2.0',
        CURLOPT_FOLLOWLOCATION => '1'
    );

    function __construct($params = array())
    {
        $this->_ci = get_instance();
        $this->initialize($params);
    }

    // --------------------------------------------------------------------

    /**
     * Initialize preferences
     *
     * @access  public
     * @param   array
     * @return  void
     */
    public function initialize($params = array())
    {    
        $this->_api_key = '';
        $this->_blog_url = '';
        isset($params['api_key']) AND $this->_api_key = $params['api_key'];
        isset($params['blog_url']) AND $this->_blog_url = $params['blog_url'];
 
        if ($params && isset($params['proxy'])) {
            self::$_curl_opts[CURLOPT_PROXY] = $params['proxy'];
        }       

        $this->_valid_key = $this->_verify_key();

        if ( ! $this->_valid_key && $params)
        { 
            log_message('error', 'Akismet could not verify the api key: '.$this->_api_key.' for blog: '.$this->_blog_url);
        } 

    }

    private function _post($url, $data)
    {
        $ch = curl_init();
        $opts = self::$_curl_opts;
        $opts[CURLOPT_POSTFIELDS] = http_build_query($data, null, '&');
        $opts[CURLOPT_URL] = $url;

        curl_setopt_array($ch, $opts);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function _verify_key()
    {
        $url = 'http://'.$this->_base_url.'verify-key';

        return 'valid' == $this->_post($url, array(
            'key'   => $this->_api_key,
            'blog'  => $this->_blog_url
        ));
    }

    private function _build_url($location = '')
    {
        return 'http://'.$this->_api_key.'.'.$this->_base_url.$location;
    }

    private function _build_comment($comment = array())
    {
        //  Structure of comment:
        //      blog (required)
        //          The front page or home URL of the instance making the request. For a blog or wiki this would be the front page. Note: Must be a full URI, including http://.
        //      user_ip (required)
        //          IP address of the comment submitter.
        //      user_agent (required)
        //          User agent information.
        //      referrer (note spelling)
        //          The content of the HTTP_REFERER header should be sent here.
        //      permalink
        //          The permanent location of the entry the comment was submitted to.
        //      comment_type
        //          May be blank, comment, trackback, pingback, or a made up value like "registration".
        //      comment_author
        //          Submitted name with the comment
        //      comment_author_email
        //          Submitted email address
        //      comment_author_url
        //          Commenter URL.
        //      comment_content
        //          The content that was submitted.

        $this->_ci->load->library('user_agent');

        return array_merge($comment, array(
            'blog'          => $this->_blog_url,
            'user_ip'       => $this->_ci->input->server('REMOTE_ADDR'),
            'user_agent'    => $this->_ci->agent->agent_string(),
            'referrer'      => $this->_ci->agent->referrer()
        ));
    }

    private function _submit_comment($uri = '', $comment = array())
    {
        // Check the key is valid.

        if ( ! $this->_valid_key) return NULL;

        //  Structure of comment (sent from controller):
        //      permalink
        //          The permanent location of the entry the comment was submitted to.
        //      comment_type
        //          May be blank, comment, trackback, pingback, or a made up value like "registration".
        //      comment_author
        //          Submitted name with the comment
        //      comment_author_email
        //          Submitted email address
        //      comment_author_url
        //          Commenter URL.
        //      comment_content
        //          The content that was submitted.

        $url    = $this->_build_url($uri);
        $data   = $this->_build_comment($comment);

        return 'true' == $this->_post($url, $data);
    }

    public function is_spam($comment = array())
    {
        return $this->_submit_comment('comment-check', $comment);
    }

    public function submit_ham($comment = array())
    {
        return $this->_submit_comment('submit-ham', $comment);
    }

    public function submit_spam($comment = array())
    {
        return $this->_submit_comment('submit-spam', $comment);
    }
}