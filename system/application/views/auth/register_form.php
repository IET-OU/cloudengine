<h1><?=t("Join !site-name!")?></h1>
<div id="register">

<p>
<?=t("By creating an account, you agree to our [link-at]Terms and Conditions of Use[/link].",
    array('[link-at]' => t_link('about/tandc')))?> 
</p>
<?=form_open($this->uri->uri_string(), array('id'=>'register_form', 'class'=>'__h5fm')); /*Uses HTML5 form attributes (BB #115).*/ ?>
<?= validation_errors() ?>
<table>
    <tr>
        <td>
            <?=form_label(t("Username"), 'user_name') ?>
        </td>
        <td>
            <?=form_input(array('name'=>'user_name',
                'required'=>true,
                'title'=>t('Minimum !N characters, letters, numbers, dash and underscore (no spaces)',
                          array('!N'=> 4 )),
                'maxlength'=>45,
                'value'=>set_value('user_name'),
            ))?>
        </td>

    </tr>
    <tr>
        <td>
            <label for="email"><?=t("E-mail")?></label>
        </td>
        <td>
            <?=form_email(array('name'=>'email',
                'required' =>true,
                'maxlength'=>254,
                'value'    =>set_value('email') )) ?>
         </td> 
    </tr>
    <tr>
        <td>
            <label for="fullname"><?=t("Full name")?></label>
        </td>
        <td>
            <?=form_input(array('name'=>'fullname',
                'required' =>true,
                'title'    =>t('Minimum !N words, letters, apostrophe, dash and accents', array('!N'=>2)),
                'maxlength'=>140,
                'value'    =>set_value('fullname') ))?>
    </tr>
    <tr>
        <td>
            <label for="institution"><?=t("Institution")?></label>
        </td>
        <td>
            <?=form_input(array('name'=>'institution',
                'required'=>true,
                'value'   =>set_value('institution') ))?>

        <?php
            $view_data = array(
                'html_id'  => 'institution',
                'item_type'=> 'institutions',
                // IDs of the next and previous form controls, jQuery syntax.
                'next_prev'=> '#country_id,#fullname,#password',
                'title'    => _('Suggested institutions'),
            );
        ?>
        </td>
    </tr>   
    <tr>
    <td>
        <label for="country_id"><?=t("Country")?></label>
        </td>
        <td>
    	<?=form_dropdown('country_id', $countries,
    	                 (isset($user->country_id) ? $user->country_id : 0 ), 'id="country_id"')?>
        </td>
    </tr>    
    <tr>
        <td>
            <label for="password"><?=t("Password")?></label>
        </td>
    	<td>
    	   <?=form_password(array('name'=>'password',
    	       'required'=>true,
    	       'title'=>t('Minimum !N characters, letters, numbers and symbols (no spaces)',
    	                 array('!N' => 5 )),
    	                       'maxlength'=>'16',
    	                       'size'=>'16',
    	                       'value'=>''))?>
        </td>
    </tr>
    <tr>
        <td>
            <label for="password_confirm"><?=t("Confirm Password")?></label>
    	</td>
        <td>
            <?=form_password(array('name'=>'password_confirm',
                 'required'=>true,
                 'oninput'=>"setCustomValidity(value!=password.value ? '"
                     .t('Error, the passwords should match')."' : '')",
    	                       'maxlength'=>'16', 
    	                       'size'=>'16',
    	                       'value'=>''))?>
        </td>
    </tr>
    <?php if (config_item('x_captcha')): ?>
    <tr>
        <td>
    	   <label for="captcha"><?=t("Please type the letters. ")?>
<?=t("If you are unable to read the letters, please e-mail !email! so that we can register you manually.") ?></label>
    	</td>
    	<td>
            <?=$this->load->view('auth/html_for_captcha', null, true)?><br />
    	   <?=form_input(array('name'=>'captcha',
    	         'required'=>true,
    	         'autocomplete'=>false,
    	                       'maxlength'=>'45', 
    	                       'size'=>'45',
    	                       'value'=>''))?>
            </td>
    </tr>
   <?php endif; ?>
</table>


        <input type="submit" name="submit" id="submit" class="submit" value="<?= t("Create my account") ?>" />
<?=form_close()?>

</div><!--END REGISTER DIV-->