<?php
/** Add 'suggestions'/ 'autocomplete' support to a form control (ie. an API client).
 *
 * (Not happy with the $.browser.msie line below.)
 */
if (!config_item('x_api_suggest') || !config_item('x_api')) {
    return;
}
?>
<div id="suggest_<?=$html_id ?>" style="min-width:28em; min-height:2.5em;"> </div>

<?php /*<datalist id="h5_<?=$html_id ?>"></datalist>
    <p id="debug"></p>  //javascript:$('#debug').text( $(this).val()); */ ?>

<script type="text/javascript">
<?php /*HTML5. function datalist_supported() {
  var is = 'options' in document.createElement('datalist');//Dive into HTML5.
  document.getElementsByTagName('html')[0].className += (is) ? " datalist ":" no-datalist ";
  return 0; //is; Disable HTML5.
}
function oninput_supported() {
  //?? http://perfectionkills.com/detecting-event-support-without-browser-sniffing/
  var el=document.createElement('input');
  var is=('oninput' in el);
  if (!is) {
    el.setAttribute('oninput', 'return;');
    is = typeof el['oninput']=='function';
  }
  el=null;
  return ! $.browser.msie; //is;
}*/ ?>
$(document).ready(function() {
  <?php /*HTML5. $('input#<?=$html_id ?>').attr('list', '_suggestions');*/ ?>
  $('input#<?=$html_id ?>').attr('autocomplete', 'off');
  var u = '<?=base_url() ?>api/suggest/<?=$item_type;
      ?>?lang=<?=$this->lang->lang_code() ?>&q=';
  <?php /*HTML5. u += (datalist_supported()) ? 'datalist=1&q=' : 'q=';*/?>
  var ev_name = $.browser.msie ? 'propertychange' : 'input';
  $('#<?=$html_id ?>').bind(ev_name, function(){
    var text = $(this).val(); //.val().trim() - bug in Opera?
    if (text.length > 0) {
      $('#suggest_<?=$html_id ?>').load(u + encodeURI(text),
        function(resp, status, ht){
          $('#suggest_<?=$html_id ?> *').show('fast');
          $("select#_suggestions").change(function() { //MSIE issue.
            $('#<?=$html_id ?>').val($(this).val());
          });
      });
    }
  });
  $('<?=$next_prev ?>').focus(function(){
    $('#suggest_<?=$html_id ?> *').hide('slow');
  });
});
</script>
