<?php
/**
 * Controller for API tests. 
 * 
 * To run the tests go to test/api_test?test=0 on a fresh install.
 * @copyright 2009, 2010 The Open University. See CREDITS.txt
 * @license GNU General Public License version 2. See LICENCE.txt
 * @package Test 
 */
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html><html lang="en"><meta charset="UTF-8"/><meta name="robots" content="noindex,nofollow"/>
<style>.tests li{border:1px solid green;padding:3px;margin:6px} .tests .fail{border:1px solid red;color:#b00}</style>

<?php
class Api_tests extends Controller {

  protected $api_key = 12345;
  protected $format;
  protected $test_index;

  public function Api_tests() {
    parent::__construct();
	
    if (! $this->config->item('x_api')) {
       show_404('/test');
    }
    $this->load->library('Api_lib', NULL, 'apilib');
    $this->format = $this->uri->file_extension() ? $this->uri->file_extension() : 'json';
    $this->test_index = (is_numeric($this->input->get('test')) && $this->input->get('test') < 3) ? $this->input->get('test') : 1;
  }


/** Simple PHP-based tests.
 */
public function index() {
  ?>
  <title>API tests</title>
  <?php
  // Test some predictable failure types.
  $fail_tests = array(
    array('expect'=>'404',  'url'=>'api/404_TEST'),
    array('expect'=>'404',  'url'=>'api/clouds/99999999.json'),
    array('expect'=>'400.3','url'=>'api/clouds/a.json'), #Or -1.
  );

  // And, systematically test for success!
  $success_tests = array(
    'clouds' => array(
      'id'  => 'cloud_id',
      'terms'  => array(1, 2978, 2884, 390), #[new], ou-conf. , ..
      'related'=> array(null, 'comments', 'followers', 'favourited', 'cloudscapes', ) #null='view', comments?
    ),
    // 'clouds/active' is a special case.
    '__clouds' => array(
       'terms' => array('active'),
       'related'=>array(null),
    ),
    'cloudscapes' => array(
      'id'  => 'cloudscape_id',
      'terms'  => array(2, 2012, 1896, 1963, 387), #[new], ou-conf., image, event, other.
      'related'=> array(null, 'clouds', 'followers', 'favourited', 'attendees',)
    ),
    'users' => array(
      'id'  => 'user_id',
      'terms'  => array(1, 3, 1,), #[new], GC.
      'related'=> array(null, 'followers', 'favourites', 'clouds', 'cloudscapes', 'stream'),
    ),
    'tags' => array(
      'id'  => 'tag',
      'terms'  => array("example", 'Learning Design', 'OULDI', 'OERs'), #'l.d.' has some users!
      'related'=> array('clouds', 'cloudscapes', 'users'),
    ),
    'search' => array(
      'id'   => 'q',
      'terms'  => array("example", 'OULDI', 'OER'),
      'related'=> array('clouds', 'cloudscapes', 'users'),
    ),
  );
  if (config_item('x_api_suggest')) {
    $success_tests['__suggest'] = array(
      'id'     => 'q',
      'terms'  => array('open'),
      'related'=> array('institutions'),
    );
  }

  ?>
<p><?=anchor('', 'Home') ?> &bull; <?=anchor('test/api_tests/js', 'Javascript tests') ?></p>
<?php
  $limit = 3*25;
  set_time_limit($limit); //Default 30s.
  echo " Set time limit: {$limit}s <br>".PHP_EOL;
  echo " Test index: $this->test_index ";
?>
<h2>API Sanity Tests</h2>
<ol class="tests">
  <?php
  $count = $count_fail = 0;
  foreach ($success_tests as $item_type => $tests) {
    $item_type = str_replace('__', '', $item_type);
    //A hack for term=__active.
    $index= isset($tests['terms'][$this->test_index]) ? $this->test_index : 0;
    $term = $tests['terms'][$index];
    #$id_name = $tests['id'];

    foreach ($tests['related'] as $related) {
      $count++;
      $milli_seconds = rand(200, 300);
      usleep($milli_seconds * 1000);  //Play nice!

      $url = $this->apilib->url($item_type, $term, $related, $this->format, "api_key=$this->api_key");
      $response = $this->_http_request($url);
      $response = $this->_process($response);

      echo "<li class='$response->status'>$response->status, <a href='$url'>$url</a>  (sleep {$milli_seconds}ms)<br>".PHP_EOL;
      echo "Response type: $response->php_type".PHP_EOL;
      if ($response->success) {
        echo " / {$response->info['http_code']}; {$response->info['total_time']}s; {$response->data->api_version}, ";
        echo $response->count;
      } else {
        echo " / {$response->data->code}: {$response->data->message}";
        $count_fail++;
      }
      echo "</li>".PHP_EOL;
    }

  }#End outer foreach.
  ?>
</ol>
Test count: <?="$count; Number of tests failed: $count_fail" ?>
  <?php

}#End function.


/** Javascript-based tests.
 */
public function js() {
    ?>
<div id="cloudworks-js-debug"> </div>
<title>Javascript tests - CloudEngine</title>
<h2>Javascript tests - "My Cloudstream"</h2>
<p>(There should be lists of cloudstream events/ tags, with links at the foot similar to "X on Cloudworks".)</p>
<?php
    $this->_javascript_test('user', 03, 'stream', "icon=rss&count=4&title=".urlencode("*Grainne's cloudstream"));
    $this->_javascript_test('tag', 'OULDI', 'clouds', "icon=t&count=5&title=".urlencode("*Clouds tagged 'OULDI'"));
    $this->_javascript_test('tag', 'OULDI', 'cloudscapes', "icon=t&count=6&title=".urlencode("*Cloudscapes tagged 'OULDI'"));
}


/** A Javascript test - add a <script> tag.
 */
protected function _javascript_test($item_type, $term, $related, $params) {
    $js_url = $this->apilib->url($item_type, $term, $related, 'js', "$params&api_key=$this->api_key");
    ?>
<p><code>&lt;script src=<a href="<?=$js_url ?>"><?= htmlentities($js_url) ?></a>&gt;</code>
  <a href="<?=str_replace('.js', '.json', $js_url) ?>">JSON-P</a></p>
<script
  type="text/javascript"
  src="<?= $js_url ?>"
></script>

<?php
}

/** make a HTTP request using cURL.
 */
protected function _http_request($url, $method='GET') {
    if (!function_exists('curl_init')) die(' cURL is required for '.__CLASS__);

    $h_curl = curl_init($url);
    curl_setopt($h_curl, CURLOPT_USERAGENT, 'api_tests/0.1 (PHP/cURL)');
    curl_setopt($h_curl, CURLOPT_REFERER, "http://".$_SERVER['HTTP_HOST']);
    curl_setopt($h_curl, CURLOPT_HTTPHEADER, array("Accept: application/json"));
    curl_setopt($h_curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($h_curl, CURLOPT_VERBOSE, TRUE);
    curl_setopt($h_curl, CURLOPT_TIMEOUT, $timeout=5000);
    curl_setopt($h_curl, CURLOPT_CONNECTTIMEOUT, $timeout);

    if ($this->config->item('proxy')) {
        curl_setopt($h_curl, CURLOPT_PROXY, $proxy = $this->config->item('proxy'));
    }


    $response = new StdClass();
    $response->data = curl_exec($h_curl);
    if ($errno = curl_errno($h_curl)) {
      die("cURL $errno, ".curl_error($h_curl)." GET $url"); #throw new Exception.
    }
    $response->info = curl_getinfo($h_curl);
    $response->success = ($response->info['http_code'] < 300);
    return $response;
}

protected function _process($response) {
    if ('xml' == $this->format) {
        $doc = new DOMDocument();
        $doc->loadXML($response->data);
        $response->count= $doc->getElementsByTagName("item")->length;
        $response->data = simplexml_load_string($response->data);
    } else {
        $response->data = json_decode($response->data);
        $response->count= isset($response->data->items) ? count($response->data->items) : 1;
    }
    $response->php_type = gettype($response->data);
    $response->status   = $response->success&&'NULL'!=$response->php_type ? 'ok' : 'fail';

    return $response;
}

}
?>
</html>
