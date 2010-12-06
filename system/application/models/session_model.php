<?php
/** Session model, for counts of current logged in and guest users.
 *
 * Note, the default session expiration time is 2*60*60 seconds.
 *       Currently called in the 'Home' controller.
 *       NDF, 1 Dec 2010.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 */

class Session_model extends Model {

	protected $table;

    public function __construct() {
        parent::Model();

		$this->table = NULL;
		if ($this->config->item('sess_use_database')
			&& $this->config->item('sess_table_name')) {
			$this->table = $this->config->item('sess_table_name');
		}
    }

	/** Determine the count of guests and logged in users.
	* @return object
	*/
	public function count_users($minutes = 30) {
		if (!$this->table) return NULL;

		$sql =<<<EOF
    SELECT user_agent, session_data FROM $this->table
    WHERE (UNIX_TIMESTAMP() - last_activity < $minutes * 60)
	AND session_data <> ""
EOF;
        $results = $this->db->query($sql)->result();

		$loggedin = 0;
		foreach ($results as $res) {
			#$data = ; #"a:0:{}"
			$ar = unserialize($res->session_data);
			// Whether the user has a role seems to be the deciding factor.
			if ($ar && !empty($ar['role'])) {
			    $loggedin++;
			}
		}
		return (object) array('loggedin'=>$loggedin, 'guests'=>count($results)-$loggedin);
	}

	/** Get the count of every session that is not a Bot.
	* @return integer
	*/
	public function count_has_data($minutes = 30) {
		if (!$this->table) return NULL;

		$sql = <<<EOF
    SELECT COUNT(*) AS count FROM $this->table
    WHERE (UNIX_TIMESTAMP() - last_activity < $minutes * 60)
    AND session_data <> ""
EOF;
        $obj = $this->db->query($sql)->result();
        return $obj[0]->count;
	}


	protected function __hack() {
		if (isset($this->session)) {
          echo ' $this->session, OK ';
        } else {
          echo ' $this->session, woops ?? ';
        }
        echo $db = $this->config->item('sess_use_database');
        echo $this->table = $this->config->item('sess_table_name');
        $minutes = 10;

		$res = $this->db->get($this->table)->result();

		$times = null;
		foreach ($res as $r) {
		  $times .= ' | '.date('c', $r->last_activity);
		}
		$now = $this->db->query('SELECT UNIX_TIMESTAMP() AS now')->result();
		var_dump($times, $now[0]->now);

        echo " Count a: ".$this->count_has_data($minutes);
	}
}
