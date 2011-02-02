#!/usr/bin/env php
<?php
/**
 Run php's built-in lint (php -l) recursively on a collection of files.

 @copyright 2009, 2010 The Open University. See CREDITS.txt
 @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 @author    Nick Freear <n.d.freear AT open.ac.uk>, 2011-01-27.

 @uses   libraries/Hglib.php, file_helper.php
 @return int Exits with 0 on success, 1 otherwise.

Usage - Mercurial hook:
pre-commit.lint=/var/www/cloudengine2/system/application/cli/lint.php
    --ci        // Assume CodeIgniter.
    --hgstatus  // Perform lint on the result of hg status.
    --all       // Or, lint on all files
    --rel       // Relative path.
    --exclude   // Exclude this (comma)-separated list.

*/
if('cli'!=php_sapi_name()) die(basename(__FILE__).": Must run as cli (?)"); #Security.

error_reporting(E_ALL | E_STRICT);
define('LINT_EXCLUDE', '.hg|.orig|.git|.svn|Zend|php-gettext|.js|.css|.gif|.png');
define('APP_CLI', basename(__FILE__));

$opts = 'ci hgstatus all rel exclude help h';
$do;
foreach ($argv as $idx => $arg) {
    if ($idx==0) continue;
    $arg = ltrim($arg, '-'); # Remove '--'
    if (FALSE!==strpos($opts, $arg)) {
        $do[$arg] = true;
    }
    else {
    	My_Lint::p( "Error, unrecognised option '$arg'.", 1 ); exit;
    }
}

$php_bin = 'php';
$src_dir  = '.';
$file_list= array();

//--ci - CodeIgniter.
//  Assume we're installed in a directory, APPPATH/cli/mylint.php
if (isset($do['ci'])) {
    define('APPPATH', dirname(__FILE__).'/../');
    define('BASEPATH', APPPATH.'../'); #Which CI?
    require_once BASEPATH.'helpers/file_helper.php';
    $src_dir = APPPATH; 
}
//--hgstatus - Use Hg status.
if (isset($do['hgstatus'])) {
    require_once APPPATH.'libraries/Hglib.php';
    $file_list = Hglib::status();
}
elseif (isset($do['all'])) {
   My_Lint::p( $src_dir );
   $file_list  = get_filenames($src_dir, TRUE); //CI helper.
}

$count_files= count($file_list);
$count_error=0;


My_Lint::p( "checking $count_files+ files (php -l)" );

$lint = new My_Lint($php_bin);
$count_error = $lint->parse($file_list, $src_dir, 0);

if ($count_error > 0) {
    #Error, exit 1.
    My_Lint::p( "Woops, found $count_error errors.", 1 ); 
}
#OK, exit 0.
My_Lint::p( "OK, found no errors.", 0 ); 

//Exit 1 or 0.


class My_Lint {

	protected $php_bin;

    public static function p($msg, $status=false) {
        echo "My lint: $msg".PHP_EOL;
        if (false!==$status) {
            echo "exit $status".PHP_EOL;
            exit($status);
        }
    }

    public function __construct($bin) {
		$this->php_bin = $bin;
	}

	public function parse($file_list, $src_dir=NULL, $count_error=0) {
		foreach ($file_list as $key => $file) {
			if (is_string($key)) {
			    $file = $key;
			}
			if (preg_match("/(".LINT_EXCLUDE.")/", $file)) {
				#echo "Skipping $file".PHP_EOL;
			} elseif (is_array($file)) { //Recurse?
				echo "Directory? $key".PHP_EOL;
				#$count_error += $this->parse("$src_dir/$key", $file, $count_error);
			} else {
				ob_start();
				system("$this->php_bin -l $file");
				$ob = ob_get_clean();
				if (0!==strpos($ob, 'No syntax errors')) {
					echo $ob;
					$count_error++;
				}
			}
		}
		return $count_error;
	}
}


/* hg update --rev 166:
array(24) {
  ["TERM_PROGRAM"]=>"Apple_Terminal"
  ["SHELL"]  => "/bin/bash"
  ["HG_OPTS"]=> "{'rev': '166', 'clean': None, 'date': '', 'check': None}"
  ["HG_ARGS"]=> "update"  OR "update --rev 166"
  ["_"] => "/usr/bin/php"
  ["HG"]=> "/usr/local/bin/hg"
  ["VERSIONER_PYTHON_VERSION"]=> "2.6"
  ["PWD"] => "/Users/Nick/workspace/cloudengine2"
  ["HOME"]=> "/Users/Nick"
  ["HG_PATS"]=> "[]"
}*/
?>