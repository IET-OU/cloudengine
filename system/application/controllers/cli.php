<?php
/**
 * CLI. Commandline tools, particularly to analyse and process spam/ ham.
 *
 * @copyright 2017 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 * @package   CLI
 *
 * @author Nick Freear, 2017-11-29.
 * @link  https://codeigniter.com/userguide2/database/queries.html
 * @link  https://docs.akismet.com/general/teach-akismet/
 * @link  https://akismet.com/development/api/#comment-check
 *
 * @example Usage >  php index.php CLI count_spam_comments
 *
 * ~/cloudengine-clean/_BAK/cloudworks-live-lindir-schema-29-nov-2017.sql
 */

class CLI extends MY_Controller {

    const SPAM_WORDS_REGEX = '(pirate|cyberlink|escort|essay|movers)';

    const SQL_FROM_DATE = '2017-01-01';

    const SQL_COUNT_REGEX_COMMENT =
    'SELECT count(*) FROM comment WHERE (SELECT FROM_unixtime(timestamp)) >= ? AND body REGEXP ?';

    const SQL_BANNED_USER_COMMENTS =
    'SELECT u.*, c.* FROM user u JOIN comment AS c ON c.user_id = u.id WHERE (SELECT FROM_unixtime(c.timestamp)) >= ? AND u.banned = 1';

    const SQL_WHITELIST_USER_CLOUDS =
    'SELECT u.*, c.* FROM user u JOIN cloud AS c ON c.user_id = u.id JOIN user_profile AS p ON p.id = user.id WHERE (SELECT FROM_unixtime(c.created)) >= ? AND p.whitelist = 1';

    public function __construct() {
        parent::MY_Controller();
        // $this->load->model('user_model');

        if ( ! is_cli()) {
            show_404();
        }
        error_reporting( E_ALL );
        ini_set( 'display_errors', 1 );
    }

    public function index() { return $this->help(); }

    public function help() {
        echo __METHOD__ . " ~ List sub-commands: \n * ";

        $class = new ReflectionClass( $this );
        $methods = $class->getMethods( ReflectionMethod::IS_PUBLIC );
        $names = [];
        foreach ($methods as $md) {
            if ($md->class == __CLASS__ && $md->name != '__construct') { $names[] = $md->name; }
        }
        echo implode( "\n * ", $names ) . "\n";
    }

    // ------------------------------------------------------------------

	/** Count the number of spam comments after a certain date.
	 *
	 * @param string $sql_from_date  From date, e.g. '2017-01-01'
	 */
	public function count_spam_comments( $sql_from_date = self::SQL_FROM_DATE ) {
        echo __METHOD__ . PHP_EOL;

        // CI_DB_mysql_result
        $result = $this->db->query( self::SQL_COUNT_REGEX_COMMENT, [ $sql_from_date, self::SPAM_WORDS_REGEX ] );

        print_r( $result );
    }

    /** Get comments from banned users after a date.
    */
    public function banned_user_comments( $sql_from_date = self::SQL_FROM_DATE ) {
        echo __METHOD__ . PHP_EOL;

        $result = $this->db->query( self::SQL_BANNED_USER_COMMENTS, [ $sql_from_date ] );

        print_r( $result->num_rows );
        echo PHP_EOL;

        return $result;
    }

    /** Get clouds from whitelisted users after a date.
    */
    public function whitelisted_user_clouds( $sql_from_date = self::SQL_FROM_DATE ) {
        echo __METHOD__ . PHP_EOL;

        $result = $this->db->query( self::SQL_WHITELIST_USER_CLOUDS, [ $sql_from_date ] );

        print_r( $result->num_rows );
        echo PHP_EOL;

        return $result;
    }

    // ------------------------------------------------------------------

    public function test_spam( $author = 'viagra-test-123', $content = 'Hello world!' ) {
        echo __METHOD__ . PHP_EOL;

        $this->load->library( 'Akismet' );

        $result = $this->akismet->is_spam([
            'comment_type' => 'comment',
            'comment_author' => $author,
            'comment_content' => $content,
        ]);

        echo 'Result: ';
        print_r( $result );
        echo PHP_EOL;
    }

    public function learn_spam( $sql_from_date = self::SQL_FROM_DATE ) {
        echo __METHOD__ . PHP_EOL;

        $comments = $this->banned_user_comments( $sql_from_date );

        $this->load->library( 'Akismet' );

        foreach ($comments->result_array as $comment) {
            $api_result = $this->akismet->submit_spam([
                'comment_type' => 'comment',
            ]);
        }
    }

    public function learn_ham( $sql_from_date = self::SQL_FROM_DATE ) {
        echo __METHOD__ . PHP_EOL;

        $clouds = $this->whitelisted_user_clouds( $sql_from_date );

        $akismet = $this->load->library( 'Akismet' );

        foreach ($clouds->result_array as $cloud) {
            $api_result = $this->akismet->submit_ham(self::_assemble_cloud( $cloud ));
        }
    }

    // ------------------------------------------------------------------

    protected static function _assemble_cloud( $cloud ) {
        $cloud_content = <<<EOT
        <p>$cloud->summary
        <p><a href="$cloud->primary_url">$cloud->primary_url</a>
        <p><a href="$cloud->url">$cloud->url</a></p>

        $cloud->body
EOT;
        return [
            'comment_type' => 'blog-post',
            'comment_content' => $cloud_content,
        ];
    }
}
