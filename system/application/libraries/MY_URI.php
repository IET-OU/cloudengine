<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * URI Class
 *
 * Parses URIs and determines routing - separates out any 'file extension' (for API).
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	URI
 * @author		ExpressionEngine Dev Team / The Open University.
 * @link		http://codeigniter.com/user_guide/libraries/uri.html
 */
class My_URI extends CI_URI {

    protected $_file_extension = NULL;

    /** Return any file extension, or NULL.
     */
    public function file_extension() {
        return $this->_file_extension;
    }

	/**
	 * Explode the URI Segments, and remove any file extension. The individual segments will
	 * be stored in the $this->segments array.
	 *
	 * @access	private
	 * @return	void
	 */
	function _explode_segments()
	{
#ou-specific
        // Separate file extension - don't mess with 'uri_string' member variable.
        $_uri_string = $this->uri_string;
        $pos = strrpos($_uri_string, '.');
        if ($pos != FALSE) {
            $_uri_string = substr($_uri_string, 0, $pos);
            $this->_file_extension = $this->_filter_uri(substr($this->uri_string, $pos+1));
        }

		foreach(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $_uri_string)) as $val)
#ou-specific ends.
		{
			// Filter segments for security
			$val = trim($this->_filter_uri($val));

			if ($val != '')
			{
				$this->segments[] = $val;
			}
		}
	}
}
