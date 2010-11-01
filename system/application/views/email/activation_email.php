<?php /*/Translators: Email messages to manage user accounts. */ ?>
<?=t("Dear !fullname,", array('!fullname'=>$fullname))?>
<p>
<?=t("Thank you for registering on !site-name!. To activate your account, 
please visit the following URL:")?>
</p>
<p>
<?= anchor('auth/activation'.'/'.$temp_user_id.'/'.$activation_code, 
site_url('auth/activation'.'/'.$temp_user_id.'/'.$activation_code)) ?>
</p>
<p>
<?=t("This URL will work for one week. After activating your account you can login with your username, !username.",
    array('!username'=>$user_name))?>
</p>
<p><?=t("The !site-name! Team")?></p>
<p>
<?=base_url();?>
</p>