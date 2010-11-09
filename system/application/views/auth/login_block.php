<p class="login">
    <a href="#login" class="link-arrow show"><?=t("Log in")?></a> 
    <?=anchor('auth/register', t("Sign up"))?>
</p>

<div id="login" class="collapsed">
      <?=form_open('/auth/login', array('id' => 'login-form'))?>
        <p><label for="user_name"><?=t("Username")?>:</label><br />
     	<?=form_input(array('name'=>'user_name', 'id'=>'user_name', 'value'=>''))?>
    </p>
    	<p><label for="password"><?=t("Password")?>:</label><br />
        <?=form_password(array('name'=>'password', 'id'=>'password','value'=>''))?></p>    
        <p>
        <input type="submit" name="submit" id="submit" class="submit" value="<?= t("Log in") ?>" />
    <?=anchor('auth/forgotten_password', t("Forgotten password"))?>
    
    
    </p>
    <?=form_close()?>
</div>

