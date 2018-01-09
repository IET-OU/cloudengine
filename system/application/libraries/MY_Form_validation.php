<?php
/**
 * Extends the Form_validation class
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package I8ln
 */

class MY_Form_validation extends CI_Form_validation {

  public function __construct($rules = array()) {
	parent::__construct($rules);
    $this->_error_prefix = '<p class="form_errors">';
    $this->_error_suffix = '</p>';
  }

    function valid_url($url) {
		// The following is from http://stackoverflow.com/questions/161738/what-is-the-best-regular-expression-to-check-if-a-string-is-a-valid-url
		$url_format =
		'/^(https?):\/\/'.                                         // protocol
		'(([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+'.         // username
		'(:([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+)?'.      // password
		'@)?(?#'.                                                  // auth requires @
		')((([a-z0-9]\.|[a-z0-9][a-z0-9-]*[a-z0-9]\.)*'.                      // domain segments AND
		'[a-z][a-z0-9-]*[a-z0-9]'.                                 // top level domain  OR
		'|((\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])\.){3}'.
		'(\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])'.                 // IP address
		')(:\d+)?'.                                                // port
		')(((\/+([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)*'. // path
		'(\?([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)'.      // query string
		'?)?)?'.                                                   // path and query string optional
		'(#([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)?'.      // fragment
		'$/i';

        $url_valid = FALSE;
        if(preg_match($url_format, $url)) {
            $url_valid = TRUE;
        } else {
            $this->CI->form_validation->set_message('valid_url', 'The %s must be a valid URL.');
        }
        return $url_valid;
    }

    /**
     * Custom rule. Check if an email address is disposable.
     *
     * @param  string $email  Email address.
     * @return boolean        $result->ok ~~ Is the email address acceptable?
     */
    public function email_nospam( $email ) {
        log_message( 'debug', __FUNCTION__ . '::start, ' . $email );

        $is_email = $this->CI->nospam->check_email( $email );

        if ( ! $is_email->ok ) {
            $this->CI->form_validation->set_message( 'email_nospam', t('The %s must not be a disposable email address.' ));
        }

        log_message( 'debug', __FUNCTION__ . '::end::' . json_encode( $is_email ));

        return $is_email->ok;
    }

    /**
     * Custom rule. reCAPTCHA.
     *
     * @param  string  $recap_response  The 'g-recaptcha-response' POST paramater.
     * @return boolean $result->success ~~ Was the reCAPTCHA valid?
     */
    public function recaptcha( $recap_response ) {
        log_message( 'debug', __FUNCTION__ . '::start, ' . $recap_response );

        $recaptcha = $this->CI->recaptcha->verify( $recap_response );
        if ( ! $recaptcha->success ) {
            $this->CI->form_validation->set_message( 'recaptcha', t('The reCAPTCHA was invalid.' ));
        }

        log_message( 'debug', __FUNCTION__ . '::end::' . json_encode( $recaptcha ));

        return $recaptcha->success;
    }

	/**
	 * Set validation Rules - calls parent::set_message() after parent::set_rules().
	 *
	 * This function takes an array of field names and validation
	 * rules as input, validates the info, and stores it.
	 * @access public
	 * @param	 mixed  $field Array or string.
	 * @param	 string $label
	 * @return void
	 * @uses t()
	 * @uses CI_Form_validation::set_message()
	 */
  public function set_rules($field, $label='', $rules='') {

    if (count($_POST) == 0) {
      return;
    }
    $res = parent::set_rules($field, $label, $rules);

    $substitute = array('!field-name'=>"<em>%s</em>", '!count'=>"<em>%d</em>");
    $lang = array(
    ///Translators: Form validation error messages.
      'required'   => t("The !field-name field is required.",             $substitute),
      'isset'      => t("The !field-name field must have a value.",       $substitute),
      'valid_email'=> t("The !field-name field must contain a valid email address", $substitute),
      'valid_url'  => t("The !field-name field must contain a valid URL.",$substitute),
      'min_length' => t("The !field-name field must be at least !count characters in length.", $substitute),
      'max_length' => t("The !field-name field can not exceed !count characters in length.", $substitute),
	  'alpha_dash' => t("The !field-name field can contain letters, numbers, dash and underscore (no space).", $substitute),
	  'alpha_numeric'=>t("The !field-name field can contain letters and numbers only.", $substitute),
      'matches'    => t("The !field-name field does not match the !field-name field.", $substitute),
      'email_nospam' => t("The !field-name must not be a disposable email address.", $substitute),
      'recaptcha'  => t('The reCAPTCHA was invalid.', $substitute),
      'callback_fullname_check'=> t("Your fullname must contain a space"),  # Used by controllers/user.php
      'callback_does_not_use_url_shortener'=>  # Used by controllers/cloud.php
t("The URL you have specified uses a URL shortener. Please give the original URL instead since URLs from URL shorteners may not exist forever."),
    );

    # views/user.php : edit() uses '|' delimited rules-list.
    if (is_string($rules)) {
      $rules_r = explode('|', $rules);
    } else {
      $rules_r = $rules; #rules-array, test!
    }
    foreach ($rules_r as $rule) {
      # Remove arrays, eg. max_length[140].
      $rule = preg_replace("#\[(.*?)\]#", '', $rule);
      if (isset($lang[$rule])) {
          $res = $this->set_message($rule, $lang[$rule]); #$rules - S?
          # Else: stay quiet for 'trim' etc.

      } else { # Unknown error.
        $res = $this->set_message($rule, t("The !field-name field has an error", $substitute).": $rule");
      }
    }
    return $res;
  }

	/**
	 * IE7 Button Fix
   *
   * IE7 passes the display value of a button rather than the value parameter.
   * Use this function by adding <span class="button-xxxxxx"></span> as the opening part
   * of the display value of your button and replace xxxxxx with the actual value for the button.
   * Do keep the actual value="xxxxxx" attribute of the button as all other browsers will use this.
   *
   * Note: this does not work with IE6 and less because they send all button values in the POST submission
   * rather than the value of the button that was pressed.
	 *
	 * @param	 string  $field string.
	 */
  public function ie7_button_fix($field) {
    if ($startpos = stripos($field,'span class=button-')) {
      //add 18 characters, which is length of 'span class=button-'
      $startpos = $startpos +18;
      //where string ends, which is the closing '>' of '<span class=button-xxxxxx>'
      $endpos   = stripos($field,'>',$startpos);
      //find length of string
      $length   = $endpos - $startpos;
      //get string
      $return = substr($field,$startpos,$length);
    } else {
      $return = $field;
    }
    return $return;
  }



}


/*End. */
