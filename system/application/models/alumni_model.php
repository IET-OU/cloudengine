<?php
/**
 * Model for Cloudworks "Influencers" (founders, emeritus and significant users etc.)
 *
 * @copyright 2009, 2019 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */

class Alumni_model extends Model {

    public function __construct()
    {
        parent::Model();
    }

    /**
     * Get the details of "influential" users.
     *
     * @return array Array of the user details.
     */
    public function get_users() {
            $this->db->select('user.id, fullname, institution, last_visit, created, ISNULL(picture) AS picture');
            $this->db->join('user_profile', 'user_profile.id = user.id');
            $this->db->join('user_picture', 'user_picture.user_id = user.id', 'left outer'); // Get users both with/without a picture.
            $this->db->where('do_not_delete', 1);

            $query = $this->db->get('user');

            return $query->result();
    }
}
