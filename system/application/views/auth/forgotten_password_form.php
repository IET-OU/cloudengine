<h1><?=t("Forgot your password?")?></h1>
<p>
<?=t("Enter the e-mail address associated with your account and we will send you password reset instructions.")?>
</p>

<?= validation_errors() ?>
<?=form_open($this->uri->uri_string(), array('id' => 'forgotten_password_form'))?>
	<p><label for="email"><?=t("E-mail address")?>:</label>
	<?=form_input(array('name'=>'email', 
	                       'id'=>'email',
	                       'maxlength'=>'100', 
	                       'size'=>'60',
	                       'value'=>''))?></p>
	<p><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>t("Send")))?>
	                     
 </p>
<?=form_close()?>
