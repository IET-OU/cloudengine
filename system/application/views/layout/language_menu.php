<?php if (!config_item('x_translate')) {
  return;
} ?>

<!-- The language drop-down menu. -->

<form id="form-lang" action="" method="post">
<p class="right">
<?=t("Preferred language")?>:
<select id="lang_select" name="lang">
  <?php foreach ($this->lang->get_options() as $code => $name): ?>
  <?php $selected = $this->lang->lang_code()==$code ? 'selected="selected"' : ''; ?>
    <option value="<?=$code ?>" <?=$selected ?>><?=$name ?></option>
  <?php endforeach; ?>
</select>
<button type="submit" ><?=t("Load")?></button>
</p>
</form>