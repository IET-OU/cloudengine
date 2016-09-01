<h1><?=t("Admin Change password")?> </h1>
<?php echo validation_errors(); ?>
<?=form_open($this->uri->uri_string(), array('id' => 'settings_form'))?>
<p><strong><?=t("Username")?>: <?= $user->user_name ?></strong></p>

    <p><label for="new_password"><?=t("New Password")?>:</label>
    <?=form_password(array('name'=>'password',
	                       'id'=>'new_password',
	                       'maxlength'=>'16',
	                       'size'=>'16',
	                       'value'=>''))?>

    </p>
      <p><label for="password_confirm"><?=t("Confirm New Password")?>:</label>
      <?=form_password(array('name'=>'password_confirm',
	                       'id'=>'password_confirm',
	                       'maxlength'=>'16',
	                       'size'=>'16',
	                       'value'=>''))?>
      </p>
	  <p><input type="submit" name="submit" value="<?=t("Change Password")?>" class="submit" /></p>
</form>
