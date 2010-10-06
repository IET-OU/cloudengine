<?php
/**
 * Render a '.js' API response.
 * Wrap a JSON-P response in Javascript for a 1-line include in HTML. Also handles API errors.
 * This view file does a HTML include of the static Javascript /_scripts/api-streams.js
 *
 * Usage:
 * <script src="http://<site_url>/api/users/3/stream.js?count=5&title=My+Cloudstream&api_key=.." type="text/javascript"></script>

  See Delicious Linkrolls!
    http://delicious.com/help/linkrolls
    http://feeds.delicious.com/v2/js/nfreear?title=My%20Delicious%20Bookmarks&icon=m&count=5
    http://l.yimg.com/hr/14012350/js/del-linkrolls.js
*/
// Need the callback function name to be unique.
$callback = "Cloudworks.Streams_CB_7303";

header("Content-Type: text/javascript; charset=UTF-8");

if (isset($error)):
    //Need to give a 200 response to make Javascript function - not good practice :(
    //Add Mozilla console JS code, in place of alert().
    header('HTTP/1.1 200 OK');
    ?>
var cw_e=

<?=json_encode($error) ?>;

if (typeof window.Cloudworks == 'undefined') window.Cloudworks = {};
(Error = function() {
    var error_text = "Cloudworks API: "+cw_e.stat+", "+cw_e.code+", "+cw_e.message;
    // Mozilla console.
    if(typeof(console) !== 'undefined' && console != null) {
        console.error(error_text);
    }
    if (document.getElementById("cloudworks-js-debug")) {
        var el = document.getElementById("cloudworks-js-debug");
        el.innerHTML = error_text;
        el.setAttribute('style', "border:2px solid #e00; color:#900; padding:8px;");
    }
    //alert(error_text);
})();
<?php
    exit;

else:
// Normal output.

    $api_url = $this->apilib->url($item_type, $item_id, $related, 'json', "count=$count&callback=$callback&api_key=$api_key");
    $js_data = array(
        'item_type'=> $item_type,
        'item_id'  => $item_id,
        'related'  => $related,
        'html_url' => $html_url,
        'count'    => $count,
        'title'    => $title,
        'BASE_URL' => base_url(),
    );
?>
if (typeof window.Cloudworks == 'undefined') window.Cloudworks = {};
<?=$callback ?> = function(resp) {
    Cloudworks.Streams.writeln(<?= json_encode($js_data) ?>, resp.items);
};
document.writeln('<scr'+'ipt type="text/javascript" src="<?=base_url() ?>_scripts/api-streams.js"></scr'+'ipt>');
document.writeln('<scr'+'ipt type="text/javascript" src="<?=$api_url ?>"></scr'+'ipt>');

<?php endif; ?>