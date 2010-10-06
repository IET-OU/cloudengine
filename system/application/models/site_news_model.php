<?php 
/**
 * Functions related to the site news 
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Site News
 */
class Site_news_model extends Model {
    
    function Site_news_model() {
        parent::Model();
    }

    /**
     * Get the current site news (i.e. last updated)
     *
     * @return string The HTML sting for the site news
     */
    function get_latest_site_news() {
        $body = FALSE;
        
        $query = $this->db->query("SELECT * FROM site_news ORDER BY timestamp DESC");

        if ($query->num_rows() > 0) {
            $site_news = $query->row();
            $body = $site_news->body;
        }
        return $body;
        
    }
    
    /**
     * Inserts a new site news
     *
     * @param string $body An HTML string containing the news content
     * @param integer $user_id The ID of the user updating the news
     */
    function insert_site_news($body, $user_id) {
        $news->timestamp = time();
        $news->body   = $body;
        $news->user_id   = $user_id;
        $this->db->insert('site_news', $news);
      
    }  
}