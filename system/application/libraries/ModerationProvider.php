<?php
/**
 * Library to enable checking of content for spam.
 * Uses strategy pattern to allow us to change moderation provider easily if necessary.
 *
 * @copyright 2009, 2016 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @author    Juliette Culver, 10-Jun-2016.
 */

class ModerationProvider {

	private $moderation = NULL;
	protected $CI;
	private $debug;

	public function __construct() {
		$this->CI =& get_instance();
		$this->debug = $this->CI->config->item('moderation_debug');
		$provider = $this->CI->config->item('moderation_provider');
		if ($this->debug) {
			log_message('debug', 'Moderation: Moderation Provider - '.$provider);
	  }
		switch($provider) {
			case "none":
				$this->moderation = new None();
				break;
			case "akismet":
				$this->CI->load->library('Akismet');
				$this->moderation = new AkismetProvider();
				break;
			default:
			   $this->moderation = NULL;
		}
	}

	public function checkSpam($user, $message) {
		return $this->moderation->checkSpam($user, $message);
	}

	public function learnSpam($user, $message, $obj_type = null) {
		return $this->moderation->learnSpam($user, $message);
	}

	public function learnHam($user, $message, $obj_type = null) {
		return $this->moderation->learnHam($user, $message);
	}
}

interface ModerationInterface {
	public function checkSpam($user, $message);

	public function learnSpam($user, $message);
	public function learnHam($user, $message);
}

/*
 * Moderation provider to allow all items through i.e. no moderation
 */
class None implements ModerationInterface {
	/* Let everything through */
	public function checkSpam($user, $message) {
		return false;
	}
	public function learnSpam($user, $message) {
		return false;
	}
	public function learnHam($user, $message) {
		return false;
	}
}

/**
 * Moderation provider to use Akismet to determine if items need moderation
 */
class AkismetProvider implements ModerationInterface {

	public function checkSpam($user, $message) {
    return $this->worker( $user, $message, 'is_spam', 'checking spam' );
	}

	public function learnSpam($user, $message) {
    return $this->worker( $user, $message, 'submit_spam', 'learning spam' );
	}

	public function learnHam($user, $message) {
    return $this->worker( $user, $message, 'submit_ham', 'learning ham' );
	}

	protected function worker($user, $message, $method, $label) {
		$this->CI =& get_instance();
		$debug = $this->CI->config->item('moderation_debug');

		if ( ! is_object($user) || ! $user->email ) {
			$this->CI->_debug([ 'ERROR', __METHOD__, 'Invalid user' ]);
			log_message('error', 'Moderation: $user is not object' . " [$method]");
			show_error( 'Invalid user object.', 500 );
			return;
		}
		if ($debug) {
      log_message('debug', 'Moderation: Akismet '. $label .' for User: '.$user->user_name.' Message Start:'.substr($message, 0, 20) . " [$method]");
		}

		$is_spam = false;

    ///TODO: need a fix for "learn ham" ?!
		if (is_object($user) && !($user->whitelist)) { // Only check for non-whitelisted users
			$comment = 	array('comment_type' 			=> 'forum-post',
							  'comment_author' 		    => $user->user_name,
							  'comment_author_email' 	=> $user->email,
							  'comment_content' 		=> $message);

			$api_key  = $this->CI->config->item('akismet_key');
			$blog_url = $this->CI->config->item('akismet_url');
			$proxy    = $this->CI->config->item('proxy');

			$params = array('api_key'=>$api_key, 'blog_url'=>$blog_url, 'proxy'=>$proxy);
			$akismet = new Akismet($params);

			$is_spam = $akismet->{ $method }($comment);  //->is_spam(..)
      if ($debug) {
				if ($is_spam) {
					log_message('debug', 'Moderation: Akismet returned SPAM' . " [$method]");
				} else {
					log_message('debug', 'Moderation: Akismet returned NOT SPAM' . " [$method]");
				}
			}

      $this->CI->_debug([ __METHOD__, $method, $is_spam, $user->user_name, $message ]);
			self::_akismet_log($akismet, $method, $label, $user->user_name, $message);

			if ($is_spam === NULL) {
				log_message('error', 'Akismet - not checking spam correctly. NULL result returned' . " [$method]");
			}
		}

		return $is_spam;
	}

	protected static function _akismet_log($akismet, $method, $label = null, $author = null, $comment = null) {
			$comment = $comment ? preg_replace( '/([\r\n]|<\/?p>|&nbsp;)/', ' ', substr($comment, 0, 20)) : '';
			$inf = $akismet->get_info();
			$result = str_replace( 'Thanks for making the web a better place.', 'Thanks..', $inf->result );
			$hdr = $inf->akismet_hdr[ 0 ];

			return self::_log(sprintf( "u:%s r:%s m:%s L:%s a:%s c:%s k:%s", $inf->info[ 'url' ], $result, $method, $label, $author, $comment, $hdr ));
	}

	protected static function _log($text) {
			$file = config_item( 'log_path' ) . 'akismet-' . date( 'Y-m-d' ) . '.log';
			// DEBUG - 2017-11-30 16:39:58 --> Total execution time: 5.4667
			$log = sprintf( "DEBUG - %s --> %s\n", date( 'Y-m-d H:i:s' ), $text );
			return file_put_contents( $file, $log, FILE_APPEND );
	}
}
