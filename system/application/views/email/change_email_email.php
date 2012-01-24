<?=t("Dear !fullname,", array('!fullname'=>$fullname))?>
<p>
<?=t("You or somebody else requested a change to the email address for your account to the following address:")?></p>
<p>
<?= $new_email ?>
</p>
<p>
<?= t("If you would like to change your email, please click on the following link:")?>
</p>
<p>
<?= anchor('auth/new_email/'.$user_id.'/'.$code, 
site_url('auth/new_email/'.$user_id.'/'.$code)) ?>
</p>
<p>
<?=t("If you did not make this request please ignore this e-mail.")?>
</p>
<p><?=base_url();?></p>
