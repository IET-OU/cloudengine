<?php
/**
 * Form helper functions for new HTML5 controls.
 *
 * (Not yet comprehensive - initially based on CloudEngine relevance. However, the library is generic to CodeIgniter. Dual-license?)
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package
 */


/** Text Input Field (HTML 3+).
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if (! function_exists('form_input')) {

	function form_input($data = '', $value = '', $extra = '') {
		$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		// By default id=name.
		if (isset($data['name'])) {
		    $defaults['id'] = $data['name'];
		}
		// HTML5 attributes - ensure they are valid.
		if (isset($data['required'])) {
		    $data['required'] = 'required';
		}
		if (isset($data['autocomplete'])) {
		    $data['autocomplete'] = $data['autocomplete'] ? 'on' : 'off';
		}

		return "<input "._parse_form_attributes($data, $defaults).$extra." />";
	}
}

require_once BASEPATH.'/helpers/form_helper.php';


/** Email input field (HTML5).
* Maxlength: 254
* http://rfc-editor.org/errata_search.php?rfc=3696&eid=1690
* http://stackoverflow.com/questions/386294/maximum-length-of-a-valid-email-address
*/
if (! function_exists('form_email')) {

	function form_email($data = '', $value = '', $extra = '') {
		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}

		$data['type'] = 'email';
		$data['maxlength'] = 254;
		return form_input($data, $value, $extra);
	}
}

/** Web address input field (HTML5).
*/
if (! function_exists('form_url')) {

	function form_url($data = '', $value = '', $extra = '') {
		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}

		$data['type'] = 'url';
		return form_input($data, $value, $extra);
	}
}

/** Search input field (HTML5).
 *  CSS:  [type=search]{-moz-appearance:searchfield;}
 */
if (! function_exists('form_search')) {

	function form_search($data = '', $value = '', $extra = '') {
		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}

		$data['type'] = 'search';
 		return form_input($data, $value, $extra);
	}
}


/** Date(-time) input field (HTML5).
 *
 * http://w3.org/TR/html-markup/input.date.html
 * http://w3.org/TR/html-markup/input.datetime-local.html
 *
 * We start by doing some
 * Note: this form helper function assumes that a function like strtotime is used to parse the input on the server.
 */
if (! function_exists('form_datetime')) {

	function form_datetime($data = '', $value = '', $extra ='') {
		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}
		$ci =& get_instance();

        // CodeIgniter 1.7.x - not, is_browser('Opera').
		if ('Opera' == $ci->agent->browser()) {
			$data['type'] = 'date';  #'datetime-local';
			$data['title'] = t('Date format').': yyyy-mm-dd';
			$value = $value ? date('Y-m-d', $value) : '';
		} else {
			///Translators: placeholder-text for date form controls.
			$data['placeholder'] = t('dd MMMMM yyyy');
			$data['title'] = t('Date format').': '.t('dd MMMMM yyyy');
			$data['pattern'] = '\d{1,2} \w{3,} 2\d{3}';
			$data['class'] = 'date-pick';
			$value = $value ? date('d F Y', $value) : '';
		}
		$data['size'] = 95;
		$data['maxlength'] = 128;

 		return form_input($data, $value, $extra);
	}
}
