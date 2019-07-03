<?php
/**
 * Model for Cloudworks "alumni" (founders, significant and emeritus users, etc.)
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */

class Alumni_model extends Model {

    public function __construct()
    {
        parent::Model();
    }

    /**
     * Get the details of all activated users
     *
     * @return array Array of the user details of all users of the site
     */
    public function get_users() {
            $this->db->select('user.id, fullname, institution, last_visit, created');
            $this->db->join('user_profile', 'user_profile.id = user.id');
            $this->db->where('do_not_delete', 1);

            $query = $this->db->get('user');

            return $query->result();
    }

}
