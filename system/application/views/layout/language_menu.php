<?php if (!config_item('x_translate')) {
  return;
} ?>

<!-- The language drop-down menu - jump to the top/#skip. -->

<form id="form-lang" action="#skip" method="post">
<p class="right"><label for="lang_select">
<?=t("Preferred language")?> </label>
<select id="lang_select" name="lang">
  <?php foreach ($this->lang->get_options() as $code => $name): ?>
  <?php $selected = $this->lang->lang_code()==$code ? 'selected="selected"' : ''; ?>
    <option value="<?=$code ?>" <?=$selected ?>><?=$name ?></option>
  <?php endforeach; ?>
</select>
<button type="submit" ><?=t("Load")?></button>
</p>
</form>