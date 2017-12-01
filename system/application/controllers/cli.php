<?php
/**
 * CLI. Commandline tools, particularly to analyse and process spam/ ham.
 *
 * @example Usage $$  php index.php CLI -h
 * @example Usage $$  php index.php CLI banned_user_comments 2017-01-01 --limit:-1  # Un-limited.
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
 * ~/cloudengine-clean/_BAK/cloudworks-live-lindir-schema-29-nov-2017.sql
 */

class CLI extends MY_Controller {

    const HELP = "Usage:\n    php index.php CLI banned_user_comments 2017-01-01 --limit:-1\n\n";

    const SLEEP = 2;
    const LIMIT = 2;
    const SQL_FROM_DATE = '2017-01-01';

    const SPAM_WORDS_REGEX = '(pirate|cyberlink|escort|essay|movers)';

    const SQL_COUNT_REGEX_COMMENT =
    'SELECT count(*) AS num FROM comment WHERE (SELECT FROM_unixtime(timestamp)) >= ? AND body REGEXP ?';

    const SQL_BANNED_USER_COMMENTS =
    'SELECT u.*, c.* FROM user u JOIN comment AS c ON c.user_id = u.id WHERE (SELECT FROM_unixtime(c.timestamp)) >= ? AND u.banned = 1 LIMIT ?';

    const SQL_WHITELIST_USER_CLOUDS =
    'SELECT u.*, c.* FROM user u JOIN cloud AS c ON c.user_id = u.id JOIN user_profile AS p ON p.id = u.id WHERE (SELECT FROM_unixtime(c.created)) >= ? AND p.whitelist = 1 LIMIT ?';

    public function __construct() {
        parent::MY_Controller();

        if ( ! is_cli()) {
            show_404();
        }

        error_reporting( E_ALL );
        ini_set( 'display_errors', 1 );
        ini_set( 'error_log', 'syslog' );
    }

    public function help() {
        echo __METHOD__ . " ~ List sub-commands: \n  * ";

        $class = new ReflectionClass( $this );
        $methods = $class->getMethods( ReflectionMethod::IS_PUBLIC );
        $names = [];
        foreach ($methods as $md) {
            if ($md->class == __CLASS__ && $md->name != '__construct') { $names[] = $md->name; }
        }
        echo implode( "\n  * ", $names ) . "\n\n";
        echo self::HELP;
    }

    // ----------------------------------------------------------------------

    /** Count the number of spam comments after a certain date.
     *
     * @param string $sql_from_date  From date, e.g. '2017-01-01'
     * @return int   Count.
     */
    public function count_spam_comments( $sql_from_date = self::SQL_FROM_DATE ) {
        echo __METHOD__ . PHP_EOL;

        $this->db->_compile_select();
        $result = $this->db->query( self::SQL_COUNT_REGEX_COMMENT, [ $sql_from_date, self::SPAM_WORDS_REGEX ] );
        // $this->output->enable_profiler(TRUE);
        $count = $result->first_row()->num;

        printf( "Count of comments: %s\n", $result->num_rows );

        $this->_sql_log($result, __METHOD__);

        return $count;
    }

    /** Get comments from banned users after a date.
     *
     * @param string $sql_from_date From date, e.g. '2017-01-01'
     * @param int|string $limit     SQL limit, e.g. 20, or '--limit:-1' (-1 means 'unlimited')
     * @return object CI_DB_mysql_result
     */
    public function banned_user_comments( $sql_from_date = self::SQL_FROM_DATE, $limit = self::LIMIT ) {
        echo __METHOD__ . PHP_EOL;

        $this->db->_compile_select();
        $result = $this->db->query( self::SQL_BANNED_USER_COMMENTS, [ $sql_from_date, self::_limit($limit) ] );

        printf( "Count of comments: %s\n", $result->num_rows );

        $this->_sql_log($result, __METHOD__);

        return $result;
    }

    /** Get clouds from whitelisted users after a date.
     * @return object CI_DB_mysql_result
     */
    public function whitelisted_user_clouds( $sql_from_date = self::SQL_FROM_DATE, $limit = self::LIMIT ) {
        echo __METHOD__ . PHP_EOL;

        $this->db->_compile_select();
        $result = $this->db->query( self::SQL_WHITELIST_USER_CLOUDS, [ $sql_from_date, self::_limit($limit) ] );

        printf( "Count of clouds: %s\n", $result->num_rows );

        $this->_sql_log($result, __METHOD__);

        return $result;
    }

    // ----------------------------------------------------------------------

    /** Test Akismet call - 'comment-check'.
     * @return void
     */
    public function test_spam( $author = 'viagra-test-123', $content = 'Hello world!' ) {
        echo __METHOD__ . PHP_EOL;

        $akismet = $this->_init_akismet();

        $result = $akismet->is_spam([
            'comment_type' => 'comment',
            'comment_author' => $author,
            'comment_content' => $content,
        ]);

        printf( "Akismet result: %s\n", $result );
        print_r( $akismet->get_info() );
        echo PHP_EOL;
        self::_akismet_log($akismet, __METHOD__);
    }

    /** Call Akismet 'submit-spam' multiple times, based on 'banned_user_comments' DB query.
     */
    public function learn_spam( $sql_from_date = self::SQL_FROM_DATE, $limit = self::LIMIT ) {
        echo __METHOD__ . PHP_EOL;

        $comments = $this->banned_user_comments( $sql_from_date, $limit );

        $akismet = $this->_init_akismet();

        foreach ($comments->result() as $comment) {
            $comment_label = "comment-$comment->comment_id";
            $result = $akismet->submit_spam([
                'permalink' => config_item('akismet_url') . '/cloud/view/' . $comment->cloud_id . '#' . $comment_label,
                'comment_type' => 'comment',
                'comment_content' => $comment->body,
                'comment_author' => $comment->user_name,
                'comment_author_email' => $comment->email,
                'comment_date_gmt' => date( 'c', $comment->timestamp ),
                'user_role' => $comment->role, // 'user'
                'blog_lang' => 'en,en_gb',
                'user_ip' => '127.0.0.1',  // Unknown :(.
                'user_agent' => 'unknown',
            ]);
            echo '.';
            // echo "$comment_label: $result\n";
            self::_akismet_log($akismet, __METHOD__, $comment_label, $comment->user_name, $comment->body);
            sleep( self::SLEEP );
        }

        printf( "\nCount of Akismet calls: %s\n", $comments->num_rows );
    }

    /** Call Akismet 'submit-spam' multiple times, based on 'whitelisted_user_clouds' DB query.
     */
    public function learn_ham( $sql_from_date = self::SQL_FROM_DATE, $limit = self::LIMIT  ) {
        echo __METHOD__ . PHP_EOL;

        $clouds = $this->whitelisted_user_clouds( $sql_from_date, $limit );

        $akismet = $this->_init_akismet();

        foreach ($clouds->result_array as $cloud) {
            $result = $akismet->submit_ham(self::_assemble_cloud( $cloud ));

            echo '.';
            // echo "cloud-$cloud->cloud_id: $result\n";
            self::_akismet_log($akismet, __METHOD__, 'cloud-' . $cloud->cloud_id, $cloud->user_name, $cloud->body);
            sleep( self::SLEEP );
        }

        printf( "\nCount of Akismet calls: %s\n", $clouds->num_rows );
    }

    // ----------------------------------------------------------------------

    protected static function _assemble_cloud( $cloud ) {
        $cloud_content = <<<EOT
        <p>$cloud->summary
        <p><a href="$cloud->primary_url">$cloud->primary_url</a>
        <p><a href="$cloud->url">$cloud->url</a></p>

        $cloud->body
EOT;
        return [
            'permalink' => config_item('akismet_url') . '/cloud/view/' . $cloud->cloud_id,
            'comment_type' => 'blog-post',
            'comment_content' => $cloud_content,
            'comment_author' => $cloud->user_name,
            'comment_author_email' => $cloud->email,
            'comment_date_gmt' => date( 'c', $cloud->timestamp ),
            // 'user_role' => $cloud->role, // 'user'
            'blog_lang' => 'en,en_gb',
            'user_ip' => '127.0.0.1',  // Unknown :(.
            'user_agent' => 'unknown',
        ];
    }

    protected static function _limit($limit) {
        $limit = (int) preg_replace('/(--)?limit[:=]/', '', (string) $limit);
        return $limit === -1 ? 10000 : $limit;
    }

    // ----------------------------------------------------------------------

    protected function _sql_log($query, $method, $label = null) {
        $sql = $this->db->last_query();

        return self::_log(sprintf( "u:%s r:%s m:%s L:%s sql:%s;", '//db', $query->num_rows(), $method, $label, $sql ));
    }

    protected static function _akismet_log($akismet, $method, $label = null, $author = null, $comment = null) {
        $comment = $comment ? substr($comment, 0, 16) : '';
        $inf = $akismet->get_info();
        $result = str_replace( 'Thanks for making the web a better place.', 'Thanks..', $inf->result );
        $hdr = $inf->akismet_hdr[ 0 ];

        return self::_log(sprintf( "u:%s r:%s m:%s L:%s a:%s c:%s k:%s", $inf->info[ 'url' ], $result, $method, $label, $author, $comment, $hdr ));
    }

    protected static function _log($text) {
        $file = config_item( 'log_path' ) . 'akismet-' . date( 'Y-m-d') . '.log';
        return file_put_contents( $file, date( 'c' ) . sprintf( " %s\n", $text ), FILE_APPEND );
    }

    // ----------------------------------------------------------------------

    protected function _init_akismet() {
        $this->load->library( 'Akismet' );
        $this->CI =& get_instance();
        $config = $this->CI->config;

        $akismet = new Akismet([
             'api_key' => $config->item('akismet_key'),
             'blog_url' => $config->item('akismet_url'),
             'proxy' => $config->item('proxy'),
             /*'blog_lang' => 'en,en_gb',
             'user_ip' => '127.0.0.1',  // Unknown :(.
             'user_agent' => 'unknown',
             */
        ]);

        $info = $akismet->get_info();
        $result = $info->result || 'false';
        echo "Init:$result\n";

        return $akismet;
    }
}
