<?php
/**
 * Extends the Form_validation class
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package I8ln
 */

class My_Form_validation extends CI_Form_validation {

  public function __construct($rules = array()) {
	parent::__construct($rules);
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
      'matches'    => t("The !field-name field does not match the !field-name field.", $substitute),
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

}


/*End. */
