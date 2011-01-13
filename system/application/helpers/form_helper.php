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


/** Text Input Field
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

		return "<input "._parse_form_attributes($data, $defaults).$extra." />";
	}
}

require_once BASEPATH.'/helpers/form_helper.php';


if (! function_exists('form_email')) {

	function form_email($data = '', $value = '', $extra = '') {
		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}

		$data['type'] = 'email';
		return form_input($data, $value, $extra);
	}
}

if (! function_exists('form_url')) {

	function form_url($data = '', $value = '', $extra = '') {
		if ( ! is_array($data)) {
			$data = array('name' => $data);
		}

		$data['type'] = 'url';
		return form_input($data, $value, $extra);
	}
}
