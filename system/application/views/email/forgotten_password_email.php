<?=t("Dear !fullname,", array('!fullname'=>$fullname))?>
<p>
<?=t("You or somebody else requested a password remind about your account. If you do not remember your password and want to change it, please click on the following link:")?>
</p>
<p>
<?= anchor('auth/new_password/'.$user_id.'/'.$forgotten_password_code, 
site_url('auth/new_password/'.$user_id.'/'.$forgotten_password_code)) ?>
</p>
<p>
<?=t("If you did not make this request please ignore this e-mail.")?>
</p>
<p><?=base_url();?></p>
