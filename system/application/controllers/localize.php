<?php
/**
 * Controller to facilitate translation.
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
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

    /** EXPERIMENTAL.
     *  Output PO files for dynamic support/about pages.
     *     Currently this only outputs templates.
     *
     * @param $lang Language code, eg. 'en', 'da'.
     * @param $section Section of the site (about or support) (Gettext 'domain').
     */
	public function pages($lang, $section) {
	    $this->load->model('page_model');

	    $pages = $this->page_model->get_pages($section);

	    if (!isset($pages[0])) {
	        show_error("Section '$section' not found.");
	    }

        $pg_out = array();
	    foreach ($pages as $n => $page) {
	        if ('en' != $page->lang) break;

            $pg_out[$n]->ref = "$page->section/$page->name";
            $pg_out[$n]->url = site_url("$page->section/$page->name");
            $pg_out[$n]->title = $page->title;
	        $pg_out[$n]->lines = explode("\n", addcslashes($page->body, '"'));
	    }

	    header("Content-Type: text/plain; charset=UTF-8");
	    header("Content-Disposition: inline; filename=pages-$section-$lang.po");
		header("Content-Language: $lang");

	    #require APPPATH ."language/_templates_/_header_.po";
	    $this->load->view('localize/po-header', array('date'=>date('c'), 'lang'=>$lang,
							'count'=>count($pg_out), 'section'=>$section));

	    $this->load->view('localize/po-pages', array('pages'=>$pg_out));
	}

	protected function _parse() {
		//http://code.google.com/p/php-po-parser/#modify
		require APPPATH ."libraries/POParserEx.php";

	    $parser = new POParser();
	    $po = $parser->parse(APPPATH."language/pages-support-en.po");
	var_dump($po);
	}
}
