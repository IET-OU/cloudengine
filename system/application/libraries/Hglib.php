<?php
/**
 * Hglib: a basic Mercurial library, to get revision/changeset information.
 *
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license   http://gnu.org/licenses/gpl-2.0.html GNU GPL v2
 */
/* Usage

$$ visudo
# Allow apache user to run Hg.
# See, http://www.sudo.ws/sudo/sudoers.man.html
apache localhost = NOPASSWD: NOEXEC: /usr/bin/hg

config/cloudengine.php
    $config['hg_path'] = 'sudo -u apache /usr/bin/hg';

Mac: /usr/local/bin/hg
RHEL/Linux: /usr/bin/hg
*/

class Hglib {

    protected static function exec($cmd) {
        //Security!
        $hg_path = config_item('hg_path');
        if (!$hg_path) return FALSE;
        if (!preg_match("#\/hg$#", $hg_path)) { #[/\\]
            echo "Error, unexpected 'hg_path' value in config/cloudengine.php, $hg_path";
            return FALSE;
        }

        chdir(BASEPATH);
        $result = FALSE;
        // The path may contain 'sudo ../hg'.
        #if (file_exists($hg_path)) {
            $handle= popen("$hg_path $cmd 2>&1", 'r');
            #echo "'$handle'; ".gettype($handle).PHP_EOL;
            $result = fread($handle, 2096);
            pclose($handle);

            if (FALSE!==strpos($result, 'hgrc from untrusted user')) {
                // How/where to output a warning cleanly?
                echo 'Warning, hg user/permissions problem. ';
                return FALSE;
            }
        #}
        return $result;
    }

    public static function paths($username=FALSE) {
        $path = self::exec('paths');
        if (!$path) return FALSE;
        //We assume there's only one path, and its named 'default'.
        $path = trim(str_replace('default =', '', $path));
        $p = parse_url($path);
        if (!$p) return FALSE;
        if (!$username) {
            $path = 'http://'.$p['host'].$p['path'];
        }
        return $path;
    }

    public static function tip() {
        $tip = self::exec('tip');
        $tip = explode("\n", $tip);
        $result = FALSE;
        //Hmm, a more efficient way?
        foreach ($tip as $line) {
            if (FALSE !== ($p = strpos($line, ':'))) {
                $result[trim(substr($line, 0, $p))] = trim(substr($line, $p+1));
            }
        }
        return $result;
    }

    public static function parents() {
        $rev = self::exec('parents');
        $rev = explode("\n", $rev);
        $result = FALSE;
        //Hmm, a more efficient way?
        foreach ($rev as $line) {
            if (FALSE !== ($p = strpos($line, ':'))) {
                $result[trim(substr($line, 0, $p))] = trim(substr($line, $p+1));
            }
        }
        return $result;
    }

    public static function revision() {
        $result= FALSE;
        $path  = self::paths();
        if ($path) {
            $rev = self::parents();
            $changeset = preg_replace('#\d+:#', '', $rev['changeset']);
            //'/changeset/..' - Are all repositories structured like Bitbucket?
            $link = "$path/changeset/$changeset";
            $result = array(
                'url' => $link,
                'tag' => $rev['tag'],
                'date'=> $rev['date'],
                'changeset' => $rev['changeset'],
            );
        }
        return $result;
    }
}
