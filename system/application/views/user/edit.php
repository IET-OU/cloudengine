<?php $this->load->view('layout/tinymce.php'); ?>
<div id="region1" class="<?= $show_description() ? 'show-desc' : 'hide-desc' ?>" >
<h1><?=t("Edit Profile")?></h1>

<?=form_open($this->uri->uri_string(), array('id' => 'user-edit-form'))?>
<?= validation_errors() ?>

	<p>
	    <label for="fullname"><?=t("Name !required", array('!required'=>form_required(t('required - please include your surname'))))?>:</label>
	    <input type="text" name="fullname" value="<?= $user->fullname   ?>" id="fullname" maxlength="55" size="55"  />
	</p>

<p>
    <label for="department"><?=t("Department")?>:</label>
    <input type="text" name="department" value="<?= $user->department?>" id="department" maxlength="55" size="55"  />
</p>

<p>
    <label for="institution"><?=t("Institution !required",
      array('!required'=> /*A Required form field. (Please don't delete.)*/ form_required(t('required'))))?>:</label>
    <input type="text" name="institution" value="<?= $user->institution?>" id="institution" maxlength="55" size="55"  />
</p>

<?php if ($show_description()): ?>
<p>
    <label for="description"><?=t("Description")?>:</label>
    <textarea name="description" id="description" cols="50" rows="10"><?= $user->description ?></textarea>
</p>
<?php endif; ?>

<p><button type="submit" name="submit" class="submit" value="Save"><?=t("Save")?></button></p>
</form>
</div>
<div id="region2">
</div>
