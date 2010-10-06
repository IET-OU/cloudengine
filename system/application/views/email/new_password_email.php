<p><?= t("Dear !fullname", array('!fullname' => $fullname)) ?></p>
<p>
<?=t("Here is your new login information:")?>
</p>
<p>
<?=t('User name')?>: <?=$user_name?>
</p>
<p>
<?=t('New Password')?>: <?=$password?>
</p>
<p>

<?=t("Please change this password as soon as possible by logging in with this new information and then going to the following link:")?>

</p>
<p><?= anchor('auth/change_password', site_url('auth/change_password')) ?>
</p>

<p>
<?=base_url();?>
</p>