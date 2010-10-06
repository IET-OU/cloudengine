<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
* Mollom Class
*
* @package        CodeIgniter
* @subpackage    Libraries
* @category    Mollom
* @author        Cameron Junge
* @license        public domain, just keep this header
* @version        1.0 beta
*/
class CI_Mollom {

    /**
    * @type string
    * @desc Path to the mollom.lib.php file.
    * @access private
    */
    var $mollom_lib_path = 'mollom.lib.php';
    /**
    * @var string
    * @desc The Mollom session id
    * @access private
    */
    var $mollom_session_id;
    
    /**
    * @var array
    * @desc Captcha returned by Mollom
    * @access private
    */
    var $mollom_captcha;
    
    /**
    * @var object
    * @access private
    */
    var $CI;
    /**
    * @var string
    * @access private
    */
    var $cache_path;

    /**
     * Mollom Constructor
     * @access public
     */
    function CI_Mollom() {
        $this->CI =& get_instance();
        $this->CI->config->load('mollom');
        $this->cache_path = ($this->CI->config->item('cache_path') == '') ? BASEPATH.'cache/' : $this->CI->config->item('cache_path');
        
        $session_id = $this->CI->db_session->userdata('mollom_session_id');
        if (!empty($session_id)) { // load the session_id from session if available
            $this->mollom_session_id = $session_id;
        }
        include_once($this->mollom_lib_path);
        
        Mollom::setPrivateKey($this->CI->config->item('mollom_privateKey'));
        Mollom::setPublicKey($this->CI->config->item('mollom_publicKey'));
        if ((int)$this->CI->config->item('mollom_timeout') > 0) {
            Mollom::setTimeOut($this->CI->config->item('mollom'));
        }
        
        $servers = array('http://xmlrpc1.mollom.com', 'http://xmlrpc2.mollom.com', 'http://xmlrpc3.mollom.com');
        Mollom::setServerList($servers);
        log_message('debug', "Mollom Class Initialized");
    }
    
    // --------------------------------------------------------------------
    
    /**
    * @desc Load the servers for Mollom, only one time!
    * @access private
    */
    function _loadServers($force = false) {
        static $servers_loaded = false;
        if (!$servers_loaded || $force) {
            $start_time = microtime();
            $cache_path = $this->cache_path.'mollom_servers.cache.php';
            $cache_mins = (int)$this->CI->config->item('mollom_cacheServers');
            $servers = array();
            if ($cache_mins > 0 && file_exists($cache_path)) { // load from cache file
                $cache = unserialize(file_get_contents($cache_path));
                if (isset($cache['expires']) && (int)$cache['expires'] >= time()) { // expried?
                    $servers = $cache['servers'];
                    Mollom::setServerList($servers);
                    log_message('debug', sprintf("Mollom servers loaded from cache (%d servers)", count($servers)));
                }
            }
            if (empty($servers)) { // get the servers from Mollom
                $servers = Mollom::getServerList();
                log_message('debug', sprintf("Mollom servers loaded from server (%d servers)", count($servers)));
                if ($cache_mins > 0 && !empty($servers)) { // save to disk cache
                    $cache_expires = time() + (60 * $cache_mins);
                    $cache = array('saved'=>time(), 'expires'=>$cache_expires, 'servers'=>$servers);
                    $this->CI->load->helper('compatibility');
                    file_put_contents($cache_path, serialize($cache));
                    log_message('debug', "Mollom servers cached to disk");
                }
            }
            $servers_loaded = !empty($servers);
            $time_taken = array_sum(explode(' ', microtime())) - array_sum(explode(' ', $start_time));
            log_message('debug', sprintf('Mollom servers loaded in %0.4f seconds', $time_taken));
        }
    }

    // --------------------------------------------------------------------
    
    /**
    * @desc Validate that the answer for the captcha is correct
    * @access public
    * @return boolean
    */
    function checkCaptcha($answer) {
        if (empty($answer)) {
            return false;
        }
        $this->_loadServers();
        return Mollom::checkCaptcha($this->mollom_session_id, $answer);
    }
    
    /**
    * @desc Get a captcha from Mollom
    * @access public
    * @return array
    */
    function getCaptcha($type='image') {
        settype($type, 'string');
        $this->_loadServers();
        switch ($type) {
            case 'audio': {
                $captcha = Mollom::getAudioCaptcha();
                log_message('debug', "Mollom audio captcha requested.");
                break;
            }
            case 'image':
            default: {
                $captcha = Mollom::getImageCaptcha();
                log_message('debug', "Mollom image captcha requested.");
                break;
            }
        }
        $this->mollom_session_id = $captcha['session_id'];
        $this->mollom_captcha = $captcha;
        if (isset($this->CI->session)) { // save the session_id
            $this->CI->db_session->set_userdata('mollom_session_id', $this->mollom_session_id);
        }
        return $this->mollom_captcha;
    }
    /**
    * @desc Get an image captcha from Mollom
    * @access public
    * @return array
    */
    function getImageCaptcha () {
        return $this->getCaptcha('image');
    }
    /**
    * @desc Get an audio captcha from Mollom
    * @access public
    * @return array
    */
    function getAudioCaptcha () {
        return $this->getCaptcha('audio');
    }
    
    /**
    * @desc Return the HTML for a captcha
    * @access public
    * @return string
    */
    function getCaptchaHTML($type='image') {
        if (!isset($this->mollom_captcha)) {
            $this->getCaptcha($type);
        }
        return $this->mollom_captcha['html'];
    }
    
    /**
    * @desc Verify your Mollom keys
    * @access public
    * @return boolean
    */
    function verifyKey() {
        $this->_loadServers();
        return Mollom::verifyKey();
    }

    /**#@+
    * @desc Method not implemented yet!
    * @access private
    */
    function checkContent( $postTitle = null, $postBody = null, $authorName = null, $authorUrl = null, $authorEmail = null, $authorOpenId = null, $authorId = null) {
        
        return Mollom::checkContent($this->mollom_session_id, $postTitle, $postBody, $authorName, $authorUrl, $authorEmail, $authorOpenId, $authorId);
    }
    function getStatistics() {
        $this->_loadServers();
        error_log('CI_Mollom::getStatistics not implimented yet.', E_USER_NOTICE);
        return false;
    }
    function sendFeedback($feedback) {
        $this->_loadServers();
        Mollom::sendFeedback($this->mollom_session_id, $feedback);
    }
    /**#@-*/
}
// END Mollom Class
