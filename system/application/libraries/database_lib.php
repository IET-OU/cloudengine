<?php
/**
 * Library for database-related functions
 * 
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package Format
 */
class Database_lib {

  // The CodeIgniter object.
  protected $CI = NULL;

  public function Database_lib() {
      $this->CI =& get_instance();
  }

  /** 
   * Check field exists
   * 
   * Used to check if a field exists on a table before applying a modify table statement to add/delete a column
   * 
   * @param string  $database The name of the database
   * @param string  $table The name of the table
   * @param string  $field The name of the field
   * @return boolean true or false
   */
  function check_field_exists($table,$field) {
    $return   = false;
    $database = $this->CI->db->database;    
    $query    = sprintf("SELECT column_name 
                                    FROM INFORMATION_SCHEMA.Columns 
                                    WHERE
                                    TABLE_SCHEMA      = '%s'
                                    AND TABLE_NAME    = '%s'
                                    AND COLUMN_NAME   = '%s'",
                        mysql_real_escape_string($database),
                        mysql_real_escape_string($table),
                        mysql_real_escape_string($field));
    $result = mysql_query($query);         
    $field_array = mysql_fetch_array($result);
    if (in_array($field, $field_array)) {
      $return = true;
    }          
    return $return;
  }
  
/** 
   * Check field exists
   * 
   * Used to check if a field exists on a table before applying a modify table statement to add/delete a column
   * 
   * @param string  $database The name of the database
   * @param string  $table The name of the table
   * @param string  $field The name of the field
   * @return boolean true or false
   */
  function check_value_exists($table,$field,$value) {
    $return       = false;
    $database     = $this->CI->db->database;    
    $query        = sprintf("SELECT * 
                                    FROM %s
                                    WHERE
                                    %s = '%s';",
                        mysql_real_escape_string($table),
                        mysql_real_escape_string($field),
                        mysql_real_escape_string($value)); 
    $result       = mysql_query($query);         
    $result_count = mysql_num_rows($result);
    if ($result_count) {
      $return = true;
    }          
    return $return;
  }  
  
}