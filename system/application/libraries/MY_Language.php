<?php
/**
 * Library to implement gettext-based internationalization.
 * Note: Uses php-gettext v1.0.9
 * @link http://launchpad.net/php-gettext php-gettext
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package I18n
 */
require_once APPPATH.'libraries/php-gettext/gettext.inc';


/**
 * Extend the Language class to implement Gettext-internationalization.
 *
 * @link      http://gnu.org/software/gettext GNU Gettext
 * @link      http://launchpad.net/php-gettext php-gettext
 */
class My_Language extends CI_Language {

	/** The locales array.
	*<code>
	* $this->locales['en-gb@EXT'] = array('name'=>'English / modified', 'locales'=>..)
	*</code>
	* @var array Array of locales indexed by code, with names, OS-specific identifiers, and alias-keys.
	*/
	protected $locales = array();
	
	/** @var string ISO language code for user-interface/reading, determined by initialize(). */
	protected $lang_ui = NULL;
	
	/** Reference to the CodeIgniter object. */
	protected $CI = NULL;

	/**
	 * Set up the MY_Language::locales array.
	 *   Class and helper usage:
	 *<code>
	 * $LANG =& load_class('Language');   //codeigniter/CodeIgniter.php
	 *
	 * $this->obj->lang->initialize();    //app./libraries/Layout::Layout()
	 *
	 * <?= t("Photos: !site_link",        //app./views/cloud/edit.php
	 *     array('!site_link'=>'<a href="http://flickr.com">Flickr</a>')) ?>
	 *</code>
	 *
	 * @see $locales
	 */
	public function __construct() {
		$this->locales = array(
		  # Keys must be lower-case, using '-'.
		  # Order is significant - 'en'+aliases last.
		  'el' => array(
		    'name'   => 'Ελληνικά / Greek',
		    'locales'=> array(
		      'el_GR.UTF-8'/*Mac/10.6*/, 'el_GR.utf8'/*RHEL/Skir*/, 'el_CY.utf8', 
		      'el_GR@euro',
		      'Greek_Greece.x__1253'/*Bug #541: Windows server, don't want 1253 encoding*/)),
		  # Aliases.
		  'el-gr' => 'el',
		  'el-cy' => 'el',
		  'en' => array(
		    'name'   => 'English',
		    'locales'=> array(
		      'en_GB.UTF-8', 'en_GB.utf8', 'en_US.UTF-8', 'en_US.utf8', 'English_United Kingdom.1252', 'English_United States')),
		  #Aliases.
		  'en-gb' => 'en',
		  'en-us' => 'en',
		);
		
		parent::__construct();
		log_message('debug', __CLASS__." Class Initialized");
	}
	
	/** 
	 * Determine which locale/language to use.
	 * 
	 *  Note, $CI doesn't exist when this class is created (front controller, 
	 *  CodeIgniter.php)
	 *  Hence, initialize() is called from app./libraries/Layout.php, not the 
	 * My_Language::__construct.
	 *
	 * How the locale/language is chosen:<ol>
	 *<li> Content negotiation with browser, using 'Accept-Language' HTTP header,
	 *<li> If there's a COOKIE, use that,
	 *<li> If there is a POST[lang] HTTP parameter, set a cookie,
	 *<li> Finally, if there's a GET[lang] HTTP parameter, then override (unless a cookie 
	 * was just set?)
	 *</ol>
	 *
	 * @link http://w3.org/International/questions/qa-accept-lang-locales Accept-Language 
	 * used for locale setting, W3C
	 * @link http://codeigniter.com/user_guide/libraries/user_agent.html  Uses 
	 * CI_User_agent::accept_lang()
	 * @link http://codeigniter.com/user_guide/libraries/input.html Uses class CI_Input
	 * @return string The chosen locale.
	 */
	public function initialize() {
		$this->CI =& get_instance();

		$this->lang_ui = str_replace('english', 'en', $this->CI->config->item('language'));

		if (!$this->CI->config->item('x_translate')) {
		  return FALSE;
		}

		# 1. Content negotiation, using 'Accept-Language' header.
		$this->CI->load->library('user_agent');
		$_lang = $this->lang_ui; #str_replace('english', 'en', $this->CI->config->item('language'));
		$method= 'non';

		# Order is significant :(
		foreach ($this->locales as $lang => $item) {
			if ($this->CI->agent->accept_lang(strtolower($lang))) {
				# If it's an alias, follow it.
				$_lang = is_string($item) ? $item : $lang;
				$method= 'ACC';
				break;
			}
		}

		# 2. If there's a COOKIE, use that.
		$lc = $this->CI->input->cookie('language', FALSE);
		if ($lc && isset($this->locales[$lc])) {
			if (is_string($this->locales[$lc])) { # It's an alias, follow it.
				$lc = $this->locales[$lc];
			}
			#(And, renew the cookie?)
			$_lang = $lc;
			$method= 'CKF';
		}
	
		# 3. If it's POST[lang], set a cookie (or session?)
		$lp = $this->CI->input->post('lang', FALSE);
		if ($lp && isset($this->locales[$lp])) {
			if (is_string($this->locales[$lp])) {
				echo 'MY_Lang, woops, unexpected!';
				$lp = $this->locales[$lp];
			}
			
			$bok = $this->set_cookie($lp);
			$_lang = $lp;
			$method= 'CKP';
		}
	
		# 4. Finally, if there's a GET[lang], then override (unless a cookie was just set?)
		$lg = strtolower(str_replace('_', '-', $this->CI->input->get('lang', FALSE)));
		if ($lg && isset($this->locales[$lg])) {
			if (is_string($this->locales[$lg])) {
				$lg = $this->locales[$lg];
			}
			if ('CKP'==$method) {
				$method = 'CKI';
			} else {
				$_lang = $lg;
				$method = 'GET';
			}
		}
		
		log_message('debug', "My_Lang: $_lang | how=$method | ".$_SERVER['REQUEST_URI']
		                      ." | ".$this->CI->agent->agent_string()." | ".
		                      $this->accept_lang()." | ".$_SERVER['REMOTE_ADDR']);
		
		$this->lang_ui = $_lang;
		$locale_r = $this->locales[$_lang]['locales'];
		
		$this->content_lang($header=TRUE);
		return $this->load_gettext($locale_r);
	}
	
	/**
	 * Get the HTTP Accept-Language request header.
	 *
	 * @param array $try_langs
	 * @return boolean
	 */
	protected function accept_lang($try_langs = array()) {
		if (! $try_langs) {
		  return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? 
		                  $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'no AL';
		}
		return NULL;
	}
	
	/**
	* Set a "language" cookie.
	*
	* @param string $lang
	* @return unknown
	*/
	protected function set_cookie($lang) {
		unset($_COOKIE['language']);
		$b_cookie=FALSE;
		
		// The expires default is 2 hours. See 
		// app/config/config.php and app/libs./DB_session.php
		$expires = config_item('sess_expiration');
		if (is_numeric($expires) && $expires > 0) {
			$expires += time();
		} else {
			$expires = time() + 60*60*2; #7200.
		}
		
		#Security/ paranoia: for PHP 5.2+ add $httponly = TURE.
		if (phpversion() < 5.2) {
			$b_cookie = setcookie('language', $lang, $expires, '/', $dom = NULL, 0);
		} else {
			$b_cookie = setcookie('language', $lang, $expires, '/', $dom = NULL, 0, 
			                      $httponly = TRUE);
		}
		
		if ($b_cookie) {
			log_message('debug', "My_Lang setcookie: language=$lang");
		} else {
			log_message('error', "My_Lang setcookie problem");
		}
		return $b_cookie;
	}
	
	/** 
	* Initialize the gettext library, including setting the locale, and binding the text 
	* domain (we don't directly "load" a MO binary language file).
	* @param array   $locale_r Array of OS-specific identifiers for the locale.
	* @param string  $domain   The text domain, "cloudworks" by default.
	* @param string  $encoding Character encoding.
	* @return string The chosen locale.
	* @uses T_setlocale
	* @uses T_bindtextdomain
	*/
	protected function load_gettext($locale_r, $domain='cloudworks', $encoding='UTF-8') {		
		// For dates etc. - and to filter arrays!
		# Windows hack - don't want "Greek_Greece.1253" :(
		$locale = setlocale(LC_TIME, $locale_r);
		if (!$locale || false===stripos($locale, '.utf')) {
			$locale = $locale_r[0];
		}
		
		// php-gettext setup
		# T_setlocale doesn't accept arrays :(
		$locale = T_setlocale(LC_MESSAGES, $locale);
		
		// Hack: we always want to emulate, so that eg. el_GR.UTF-8 is used correctly.
		global $EMULATEGETTEXT;
		$EMULATEGETTEXT = 1;
		
		T_bindtextdomain($domain, APPPATH."language");
		T_bind_textdomain_codeset($domain, $encoding);
		T_textdomain($domain);
		return $locale;
	}
	
	/** 
	* Get an array of languages indexed by ISO code, suitable for a form drop-down menu.
	* @return array eg. array('el'=>'Greek', 'en'=>'English')
	*/
	public function get_options() {
		$options = array();
		foreach ($this->locales as $code => $loc) {
			if (is_array($loc)) {
				# It's not a alias.
				$options[$code] = $loc['name'];
			}
		}
		return $options;
	}
	
	/** 
	* Get the ISO language code.
	* @return string A language string, eg. "el_GR"
	*/
	public function lang_code() { 
		return $this->lang_ui; 
	}
	
	/** 
	* Send a Content-Language HTTP response header, or return a meta-tag.
	* @param  bool  $header Flag, whether we want a header, default FALSE.
	* @return mixed If the $header parameter is TRUE returns void, otherwise returns string
	*/
	public function content_lang($header = FALSE) {
		$lang_ui = $this->lang_ui;
		if ('en'!=$lang_ui) {
		  	$lang_ui .= ',en';
		}
		if ($header) {
		  	@header("Content-Language: $lang_ui");
		} else {
		  	return '<meta http-equiv="Content-Language" content="'.$lang_ui.'" />'.PHP_EOL;
		}
	}
	
	/** 
	* Get a HTML language attribute.
	* @return string A language attribute, eg. 'lang="el" '.
	*/
	public function lang_tag() {
		return ' lang="'.$this->lang_code().'" ';
	}
	
	/** 
	 * Return a HTML <link> to alternative language versions of page.
	 * @return string
	 */
	public function meta_link() { 
		return NULL; 
	}
	
} 
	
/** 
 * Translate strings to the page language or a given language.
 <code>
   <?= ///Translators: this substitutes !title with a dynamic link.
      t("Your cloud !title has been created!",  //app./views/cloud/cloud_added.php
        array('!title' => anchor("cloud/view/$cloud->cloud_id", $cloud->title))) ?>

   <?= ///Translators: replaces !site-name! with the predefined $config variable.
      t("Visit !site-name!.") ?>
 </code>

 * @param  string $string A string containing the English string to translate.
 * @param  array  $args   An associative array of replacements to make after translation.
 *   Incidences of any key in this array are replaced with the corresponding value. (..):
 *     !variable: inserted as is (..)
 *   Reserved keys - see comment below:
 *     !email! , !required!
 *
 * @param  string $langcode Optional language code to translate to a language other than what is used to display the page.
 * @return string The translated string.
 * @link http://api.drupal.org/api/function/t Drupal API: 't' function.
 * @uses T_gettext()
 */
function t($string, $args = array(), $langcode = NULL) {
	$msgid = $string;

	// Deployment: use the 'php-gettext' emulator.
	$string = T_gettext($string);

	if (FALSE!==$args) { 
		// Reserved keys - !email! , !required! , !site-name!
		$CI = & get_instance();
		$email    = $CI->config->item('site_email');
		$site_name= $CI->config->item('site_name');

		$args = array_merge($args, array(
		  '[/link]'=> '</a>',
		  '!email!'=>  $email,
		  '!email-link!'=>"<a href=\"mailto:$email\">$email</a>",
		  '!site-name!' => $site_name,
		  '!site-link!' => anchor('', $site_name),
		  '!required!'  => form_required(),    //A required form field. (Recurse.)
		  'KB'     => '<abbr title="'._('Kilo Bytes').'">&thinsp;KB</abbr>', 
		));
	}
	if (FALSE==$args) {
		$args = array();
	}
	
	// Debugging: prefix with eg. '>'.
	return /*'^ './**/ strtr($string, $args);
}

/** Used within t() calls, to replace text like [link-c2525] with the start of a link.
 *    (See [/link] in t() above).
 *
 *<code>
 * <?= t("You can find answers... in [link-faq]our FAQ[/link].",
 *     array('[link-faq]' => t_link('about/faq_site'))) ?>
 *</code>
 *
 * @param  string A local (relative) or absolute URL.
 * @param  bool 
 * @return string An opening tag for a HTML hyperlink.
 * @see t()
 */
function t_link($url, $local=TRUE) {
	if ($local) {
	  	return '<a href="'.site_url($url).'">';
	}
	
	return '<a href="'.$url.'">';
}

/** Translate singular-plural strings.
 *<code>
 * <?= plural(_("!count comment"), _("!count comments"), $row->total_comments) ?>
 *</code>
 *
 * @param  string $string1 Singular form, with optional placeholders.
 * @param  string $string2 Plural form, with optional placeholders.
 *   Placeholder/ reserved keys - "!count".
 * @param  int    $number Used to determine which form to use.
 * @return string The translated string.
 * @uses T_ngettext()
 */
function plural($string1, $string2, $number) {
	$args = array('!count' => $number, '!number' => $number);
	$string1 = strtr($string1, $args);
	$string2 = strtr($string2, $args);
	
	$string = T_ngettext($string1, $string2, $number);
	
	# For the moment, treat as English.
	# Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;
	return $string;
}

/** Translate strings containing a date and/or time.
 *<code>
 * <?= format_date(_("!event on !date!"), $cloud->modified_date) ?>
 *</code>
 *
 * @param string $format String containing '!date!' or '!date-time!', and optionally other placeholders, like in t().
 * @param int    $timestamp A Unix timestamp, or uses time() if it's NULL.
 * @param array  $args An array of arguments, like in t().
 * @return string The translated string.
 * @uses strftime()
 * @uses t()
 */
function format_date($format, $timestamp=NULL, $args=array()) {
    if (!$timestamp) {
      	$timestamp = time();
    }
    
    if (!$format) {
     	 $format = '!date-time!';
    }
    
    $date_args = array(
    /*/Translators: date/ date with time format, according to the strftime documentation.
    For example, %l (lower-case L) is the hour in 12-hour format (1 through 12).
    Eg. "9:49am 7 December 2009". http://php.net/manual/en/function.strftime.php */
      '!date-time!'         => _("%H:%M on %e %B %Y"),  #'g:ia j F Y', content_block.php, zh 2010?3?13? ??? 23:38.
      '!date-time-message!' => _("%e %B %Y at %H:%M"),  #e.g. 14:24 on 15 Nov 2010    
      '!date-time-abbr!'    => _("%e %b %Y at %H:%M"),  #e.g. 14:24 on 15 Nov 2010      
      /*/Translators: date format, eg. "7 December 2009". */
      '!date!'              => _("%e %B %Y"),  #'j F Y'
      '!month-year!'        =>_("%B %Y"),     #'F Y'
      /*/Translators: !month! eg. "December", "Dec" (homepage events block). */
      '!month!'             => t("%B"),
    );

    # Hack: '%e' and others don't work in strftime on Windows :(
    if (isset($_SERVER['WINDIR'])) {
      $date_args = str_replace('%e', '%#d', $date_args);     
    }

    foreach ($date_args as $j => $a) {
      	$date_args[$j] = strftime($a, $timestamp);          
    }

    $date_args['!date'] = $date_args['!date!'];
    $date_args['!event']= NULL;
    $args = array_merge($date_args, $args);

    return t($format, $args);
}

if (!function_exists('_')) {
	/** A no-op function to act as a placeholder in plural()/format_date() function calls. 
	* @param  string A translatable string.
	* @return string
	* @see plural()
	* @see format_date()
	*/
	function _($s) { 
		return $s; 
	}
}

/** Text/markup to indicate an optional/required form field, within a <label>.
 *<code>
 * <?= t("Title !required!") ?>
 *</code>
 * @param string
 * @return string The translated string.
 */
function form_required($string = NULL) {
	if (!$string) {
		$string = t("required", FALSE); #"required";
	}
	
	return "($string)";
}