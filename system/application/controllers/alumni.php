<?php
/**
 * Controller for Cloudworks "influencers" (founders, emeritus and significant users etc.)
 *
 * @copyright 2009, 2019 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package User
 */

class Alumni extends MY_Controller {

  public function index () {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $this->load->model('alumni_model');
    $this->load->model('favourite_model');

    $alumni = $this->alumni_model->get_users();

    foreach ($alumni as $user) {
        $user->reputation = $this->favourite_model->get_reputation($user->id);

        $user->picture = ! $user->picture;

        if (preg_match('/((The )?Open University|OU)/i', $user->institution)) {
                $user->institution = 'ou';
        }
    }

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode([
        '#' => 'Founders, emeritus and significant users etc.',
        'date' => date('c'),
        'self' => 'https://cloudworks.ac.uk/alumni',
        'alumni' => $alumni,
    ]);
  }
}

// End.
