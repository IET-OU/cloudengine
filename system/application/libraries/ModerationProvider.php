<?php 

/*

Library to enable checking of content for spam. Uses strategy pattern to allow us to change moderation provider easily if necessary.

*/
class ModerationProvider {
	private $moderation = NULL;
	protected $CI;

	public function __construct($provider) {
		$this->CI =& get_instance();
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
}

interface ModerationInterface {
	public function checkSpam($user, $message);
}

/*
 * Moderation provider to allow all items through i.e. no moderation
 */
class None implements ModerationInterface {
	/* Let everything through */
	public function checkSpam($user, $message) {
		return FALSE;
	}
}

/**
 * Moderation provider to use Akismet to determine if items need moderation
 */
class AkismetProvider implements ModerationInterface {
	public function checkSpam($user, $message) {
		$is_spam = false;

		if (is_object($user) && !($user->whitelist)) { // Only check for non-whitelisted users
			$comment = 	array('comment_type' 			=> 'forum-post',
							  'comment_author' 		    => $user->user_name,
							  'comment_author_email' 	=> $user->email,
							  'comment_content' 		=> $message);
			$this->CI =& get_instance();

			$api_key = $this->CI->config->item('akismet_key');
			$blog_url = $this->CI->config->item('akismet_url');
			$proxy = $this->CI->config->item('proxy');

			$params = array('api_key'=>$api_key, 'blog_url'=>$blog_url, 'proxy'=>$proxy);
			$akismet = new Akismet($params);
						
			$is_spam = $akismet->is_spam($comment);

			if ($is_spam === NULL) {
				log_message('Akismet - not checking spam correctly. NULL result returned');
			}
		}

		return $is_spam;
	}
}