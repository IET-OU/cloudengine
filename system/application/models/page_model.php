<?php 
/**
 *  Model file for functions related to static pages
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Page
 */
class Page_model extends Model {   
    
	function Page_model() {
        parent::Model();
    }
    
    /**
     * Get a page 
     *
     * @param string $section The section that the page belongs to
     * @param string $name The name of the page
     * @param string $lang The language code for the page
     * @return string A string containing the HTML for the page
     */
    function get_page($section, $name, $lang = 'en') {
        $page = FALSE;
        $this->db->where('section', $section);
        $this->db->where('name', $name);
        $this->db->where('lang', $lang);
        $query = $this->db->get('page');
        if ($query->num_rows() > 0) {
            $page = $query->row();
        } elseif ($lang != $this->config->item('default_language')) {
            // If there's no page in the language specified, get the page in the site's 
            // default language
            $page = $this->get_page($section, $name, $this->config->item('default_language'));
        }
        return $page; 
    }
    
    /**
     * Get all the pages in a section
     *
     * @param string $section The section
     * @return array Array of pages 
     */
    function get_pages($section) {
        $this->db->where('section', $section);
        $this->db->order_by('lang', 'DESC');
        $query = $this->db->get('page');
        return $query->result();
    }
    
    /**
     * Update a page
     *
     * @param object $page The page details to update
     */
    function update_page($page) {      
        $this->db->update('page', $page, array('name' => $page->name, 
                          'section' => $page->section, 'lang' =>$page->lang)); 
    }
    
    /**
     * Insert a new page
     *
     * @param object $page Details of the new page
     * @return integer The ID of the new page
     */
    function insert_page($page) {
        $this->db->insert('page', $page);
        $page_id =  $this->db->insert_id();
        return $page_id;
    }
    
    /**
     * Delete a page
     *
     * @param string $section The section that the page belongs to
     * @param string $name The name of the page
     * @param string $lang The language code for the page
     */
    function delete_page($section, $name, $lang) {
        $this->db->delete('page',  array('name' => $name, 'section' => $section, 
                          'lang' =>$lang)); 
    }
    
    
}