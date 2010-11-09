<h1>
<?php $title = array('!title' => anchor("cloud/view/$cloud->cloud_id", $cloud->title));
      if ($new): ?>
<?=t("Add a link to the cloud !title", $title)?>
<?php else: ?>
<?=t("Edit link for the cloud !title", $title)?>
<?php endif; ?>
</h1>

<?php echo '<b>'.validation_errors().'</b>'; ?>
<?php if ($duplicate):?><b><?=t("The URL specified has already been added as a link to this cloud")?></b><?php endif; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'add-link-form')) /*/Translators: The form field is optional. */ ?>
<p>
	<label for="title"><?=t("Title !required",
	  array('!required'=>form_required(t("required"))))?></label>
	<input type="text" id="title" name="title" size="80" value="<?= $link->title ?>"/>
	<label for="title"><?=t("URL")?></label>
	<input type="text" id="url" name="url" value="<?php if ($link->url): ?><?= $link->url ?><?php else: ?>http://<?php endif; ?>"  size="80" />
</p>
  <?php $button = ($new) ? t("Add link") : t("Save link"); ?>
  <p><button type="submit" name="submit" class="submit" value="Save"><?=$button ?></button></p>
<?=form_close()?>