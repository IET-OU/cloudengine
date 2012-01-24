<h1><?=t("Change e-mail")?></h1>   
<p><?= t("The following e-mail address is currently associated with your account: ").$user->email ?>
<p><?= t("You can change this e-mail address here.") ?></p>
<p><?= t(" When you 
enter the new e-mail address, an e-mail will be sent to your previous e-mail with a 
link that you will need to click to confirm that you wish to change your e-mail address.") 
?></p>
<?= validation_errors() ?>
    <label for="user_name"><?=t("New e-mail")?>:</label>
            <?=form_email(array('name'=>'email',
                'required' =>true,
                'maxlength'=>254,
                'value'    =>set_value('email') )) ?>

    
    <label>
        <input type="submit" name="submit" id="submit" class="submit" value="<?= t("Change e-mail") ?>" />
    </label>
<?=form_close()?>
</fieldset>

