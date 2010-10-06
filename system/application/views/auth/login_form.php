<h1><?=t("Login")?></h1>   
<?= isset($error) ? $error : ''?>
<?=form_open($this->uri->uri_string(), array('id' => 'login_form'))?>
	<label for="user_name"><?=t("Username")?>:</label>
	<?=form_input(array('name'=>'user_name', 
	                       'id'=>'user_name',
	                       'maxlength'=>'45', 
	                       'size'=>'45',
	                       'value'=>''))?>

	<label for="password"><?=t("Password")?>:</label>
	<?=form_password(array('name'=>'password', 
	                       'id'=>'password',
	                       'maxlength'=>'16', 
	                       'size'=>'16',
	                       'value'=>''))?>
    

    
	<label>
        <input type="submit" name="submit" id="submit" value="<?= t("Log in") ?>" />
	</label>
<?=form_close()?>
</fieldset>
<p><?= anchor('user/forgotten_password',t("Forgotten Password"))?>&nbsp;
 &nbsp; 
 <?= anchor('user/register', t("Register")) ?></p>
