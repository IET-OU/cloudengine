<?php
/**
 * Settings model - version numbers, configuration variables and so on are stored as 
 * name-value pairs.
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Settings
 */
class Settings_model extends Model {

	/**
	* Get settings with a name equal to or like $name.
	*
	* @param string $name The name for the setting to get
	* @param boolean $like Look for settings that are 'LIKE' not necessarily identical to the
	* name
	* @return object An array containing all the matching settings
	*/
	public function get_setting($name, $like = FALSE) {
        $name = $this->db->escape_str($name);
		if ($like) {
			$query = $this->db->query("SELECT * FROM settings WHERE name 
		                             LIKE '$name'"); 
		} else {
			$query = $this->db->query("SELECT value FROM settings WHERE name = ?", 
			                          array($name));       
		}
		
		if (!$query) {
			return FALSE;
		}
		
		$results = $query->result();
		
		if (!$like) {
			return $results[0]->value;
		}
		
		$output = NULL;
		
		foreach ($results as $result) {
			$output[$result->name] = $result->value;
		}
		
		return (object) $output;
	}

	/**
	* Insert or update a name-value pair in the settings
	*
	* @param array $data  of name value pairs
	* @return boolean TRUE if the insert/update was successful. 
	*/
	public function update_setting($data) {
				
    $this->db->where('name', $data['name']);
    $this->db->update('settings', $data); 
		return $this->db->result;
	}
	
	/**
	* Insert or update a name-value pair in the settings depending on whether the
	* setting already exists for the specified name
	*
	* @param string $name The settings name
	* @param string $value The settings value
	* @return boolean TRUE if the insert/update was successful. 
	*/
	public function replace_setting($name, $value) {
        $name = $this->db->escape_str($name);
        $value = $this->db->escape_str($value);

        $current = $this->get_setting($name);
       
		if (!is_null($current)) {
		  	$sql = "UPDATE settings SET value = ? WHERE name = ?";
		  	$array = array($value, $name);
		} else {
		  	$sql = "INSERT INTO settings (name, value) VALUES (?, ?)";
		  	$array = array($name, $value);
		}

		$query = $this->db->query($sql, $array);
		return (bool) $query;
	}

	/**
	* Get all settings
  *
	* @return array  
	*/
	public function get_all()
	{
   return $this->db->get('settings')->result();
	}

}

