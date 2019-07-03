<?php
/**
 * Controller for Cloudworks "alumni" (founders, significant and emeritus users, etc.)
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */

class Alumni extends MY_Controller {

  public function index () {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $this->load->model('alumni_model');
    // $this->load->model('user_model');
    $this->load->model('favourite_model');

    // $reputation = $this->favourite_model->get_reputation($user_id);

    $alumni = $this->alumni_model->get_users();

    foreach ($alumni as $user) {
        $user->reputation = $this->favourite_model->get_reputation($user->id);
    }

    // echo 'JSON: [] ';
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([ 'date' => date('c'), 'alumni' => $alumni ]);
  }
}

// End.
