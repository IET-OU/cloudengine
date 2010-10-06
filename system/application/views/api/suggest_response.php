<?php
/** Render the /api/suggest response as HTML.
*/
if (config_item('x_api_debug')) {
  header("Content-Type: text/plain; charset=UTF-8");
} else {
  @header("Content-Type: text/html; charset=UTF-8");
}

if (!$label) $label = t("Suggestions");
if (!$items) {
    $items = array( (object)array('name'=>null, 'total'=>null) );
}

if (!$datalist): //The non-HTML5 way - a select box.
?>
<select id="<?=$html_id ?>" size="<?=count($items) +1;
 ?>" title="<?=$label ?>" lang="<?=$this->lang->lang_code() ?>" style="width:95%">
<optgroup label="<?=$query ?>">
<?php foreach ($items as $option):
  $truncated = strlen($option->name)>35 ? substr($option->name, 0, 33).'...' : $option->name; ?>
  <option value="<?=$option->name ?>"><?=$truncated ?> <?=$option->name ? '- '.
      plural($pattern, $pattern_plural, $option->total) : t('No suggestions') ?></option>
<?php endforeach; ?>
</optgroup>
</select>

<?php else: //Opera supports HTML5 <datalist>.
?>
<datalist id="<?=$html_id ?>" title="<?=$label ?>" lang="<?=$this->lang->lang_code() ?>" -->
<?php foreach ($items as $option): ?>
  <option value="<?=$option->name ?>" label="** <?=$option->name ?
      plural($pattern, $pattern_plural, $option->total) : t('No suggestions') ?>" />
<?php endforeach; ?>
</datalist>

<?php
endif;

/*
<datalist id="suggestions">
  <option value="The Open University" label="3 users"  id="test1" />
  <option value="Open University" label="1 users" />
</datalist>

<script __src="http://html5rocks.googlecode.com/svn-history/r26/trunk/www.html5rocks.com/static/js/modernizr-1.1.min.js"></script>
*/ ?>