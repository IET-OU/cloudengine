<?php
/**
 * Controller to facilitate translation.
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package I8ln
 */
class Localize extends Controller {

  protected $domain = "cloudworks";

  public function index($lang='en-GB') {
    redirect("localize/pot");
  }

	/**
	* Output a PO(T) template.
	*
	* @param string $domain
	*/
	public function pot($domain = NULL) {
		if (!$domain) {
			$domain = $this->domain;
		}
		
		$lang='en-GB';
		
		if (file_exists(APPPATH ."language/_templates_/$domain.po")) {
			header("Content-Type: text/plain; charset=UTF-8");
			header("Content-Disposition: inline; filename=$domain.po");
			header("Content-Language: $lang");
			echo "# lang=$lang;  TEMPLATE".PHP_EOL.PHP_EOL;
			require_once APPPATH. "language/_templates_/$domain.po";
		} else {
		  	show_404("localize/pot/$domain");
		}
	}
	
	/**
	* Output a PO file for a given language.
	* 
	* @param string $lang
	* @param string $domain
	*/
	public function po($lang = 'el', $domain = NULL) {
		if (!$domain) {
			$domain = $this->domain;
		}
		
		if (file_exists(APPPATH ."language/$lang/LC_MESSAGES/$domain.po")) {
			header("Content-Type: text/plain; charset=UTF-8");
			header("Content-Disposition: inline; filename=$domain-$lang.po");
			header("Content-Language: $lang");
			echo "# lang=$lang;".PHP_EOL.PHP_EOL;
			require APPPATH ."language/$lang/LC_MESSAGES/$domain.po";
		} else {
		  	show_404("localize/po/$lang/$domain");
		}
	}
}
