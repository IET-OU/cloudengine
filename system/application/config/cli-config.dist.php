<?php
/**
 * Command-line configuration options. (Currently, only needed for CLI::test_spam & CLI::compare_spam methods.)
 *
 * @package CLI
 * @link    https://github.com/IET-OU/cloudengine/blob/master/system/application/controllers/cli.php#L27
 */

// Alternative Akismet anti-spam account (for comparison).
// $config['akismet_key'] = 'EDIT ME';  // Nick personal.
$config[ 'akismet_key_live' ] = 'EDIT ME'; // Live cloudworks.
// $config['akismet_url'] = 'http://cloudworks-approval.open.ac.uk';
$config[ 'akismet_url_live' ] = 'http://cloudworks.ac.uk';

// MySQL REGEX syntax.
$config[ 'spam_words_regex' ] = '(pirate|cyberlink|escort|essay|movers)';

// TEST content !!

// "Test your API calls" ~ https://akismet.com/development/api/#detailed-docs
$config[ 'spam_test_author' ] = 'EDIT ME';
$config[ 'spam_test_email' ]  = 'akismet-guaranteed-spam@example.com';
$config[ 'spam_test_content'] = 'Hello world!';
$config[ 'spam_test_role' ]   = 'user';  // 'administrator' - Guaranteed ham result!


// --------------------------------------------------------------------------

// http://cloudworks.ac.uk/cloud/view/11423
$config[ 'spam_comment_ex_0' ] = (object) [
   'cloud_id' => 12345,
   'comment_id' => 67890,
   'comment_label' => 'comment-example-0',
   'title'    => 'EDIT ME',
   'primary_url' => 'EDIT ME',
   'timestamp'=> strtotime( '2017-12-04T10:00:00' ),
   'body'     => null, // See below !!
   'id'       => 1357,
   'user_id'  => 1357,
   'role'     => 'user',
   'user_name'=> 'EDIT ME',
   'email'    => 'EDIT ME',
   'full_name'=> 'EDIT ME',
   'institution?' => 'EDIT ME',
];
$config[ 'spam_comment_ex_0' ]->body = <<<EOT
  'EDIT ME'
EOT;

// End.
