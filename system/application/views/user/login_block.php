<p class="login">
    <a href="#login" class="link-arrow show"><?=t("Log in")?></a> 
    <?=anchor($this->config->item('FAL_register_uri'), t("Sign up"))?>
</p>

<div id="login" class="collapsed">
    <?=isset($this->fal_validation->login_error_message) ? $this->fal_validation->login_error_message : ''?>
    <?=form_open('/user/login', array('id' => 'login-form'))?>
        <p><label for="user_name"><?=t("Username")?>:</label><br />
     	<?=form_input(array('name'=>'user_name', 'id'=>'user_name', 'value'=>''))?>
    	<?=(isset($this->fal_validation) ? $this->fal_validation->{'user_name'.'_error'} : '')?></p>
    	<p><label for="password"><?=t("Password")?>:</label><br />
        <?=form_password(array('name'=>'password', 'id'=>'password','value'=>''))?></p>
        <?=(isset($this->fal_validation) ? $this->fal_validation->{'password'.'_error'} : '')?>
    
        <p><?=form_submit(array('name'=>'login', 'id'=>'edit-submit', 'class'=>'submit', 'value'=>t("Log in")))?>
    <?=anchor($this->config->item('FAL_forgottenPassword_uri'), t("Forgotten password"))?></p>
    <?=form_close()?>
</div>

