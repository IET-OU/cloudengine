<?php

/**
 * Controller for various static pages about the site in the 'about' section.
 * Admins can update URLs under 'about' using the admin interface - this controller is used
 * to then display those pages
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Page
 */
class About extends MY_Controller {

	public function About ()
	{
		parent::MY_Controller();
		$this->load->library('layout', 'layout_main');
		$this->load->model('page_model');
	}

	/**
	 * Takes the page name specified in the URL and retrieves from the database the appropriate
	 * page for the about section in the current language and then displays it.
	 *
	 * As a backup, gets a "static page" from the file-system.
	 *
	 * @param string $name The page name
	 */
	public function _remap($name) {
		$this->_debug([ 'remap' => $name ]); // Was: 'X-about-remap-00: '

		$page = $this->page_model->get_page('about', $name, $this->lang->lang_code());

		$page = self::_fix_page_urls($page);
		$page = self::_try_get_static_page($page);

		if (! $page || ! $page->body) {
			return show_404();
		}

		$page->class_name = 'page-' . $name;

		$data['title']      = $page->title;
		$data['navigation'] = 'about';
		$data['page']       = $page;

		$this->layout->view('page/view', $data);
	}

	// -------------------------------------------------------------------------

	/** Dynamically auto-hyperlink, and fix OU and Jisc logos on main "about" page.
	 *
	 * @param object $page Page database result.
	 * @return object Page
	 */
	protected static _fix_page_urls($page) {
		$page->body = preg_replace('@([\[\( ])(https?:\/\/[\w\.\/]+)@', '$1<a href="$2">$2</a>', $page->body);

		// Dynamically fix OU and Jisc logos.
		$page->body = preg_replace('@src="[^"]+oulogo\-56\.jpg"@', 'src="/_design/ou-logo.svg"', $page->body);

		$page->body = preg_replace('@src="[^"]+JISCcolour23.jpg"@', 'src="/_design/jisc-logo.svg"', $page->body);

		// Was: $page->body = preg_replace('@src="http:\/\/@', 'src="https://', $page->body);

		return $page;
	}

	/* GDPR/privacy */

	/** As a backup, gets a "static page" from the file-system.
	 *
	 * @param object $page Page database result.
	 * @return object Page
	 */
	protected static _try_get_static_page($page) {

		// Static pages are only available in English!
		$file_path = __DIR__ . '/../../../static_pages/' . $name . /* '.en' */ '.html';

		if ((! $page || ! $page->body) && file_exists( $file_path )) {
			$page = (object) [
				'title' => ucwords( $name ),
				'body' => file_get_contents( $file_path ),
			];
		}
	}
}
