<h1><?=t("Join !site-name!")?></h1>
<div id="register">

<p>
<?=t("By creating an account, you agree to our [link-at]Terms and Conditions of Use[/link].",
    array('[link-at]' => t_link('about/tandc')))?> 
</p>
<?=form_open($this->uri->uri_string(), array('id' => 'register_form'))?>
<?= validation_errors() ?>
<table>
    <tr>
        <td>
        	<?=form_label(t("Username"), 'user_name') ?>
        </td>
        <td>
        <input type="text" name="user_name" id="user_name" max_length="45" size="45" 
               value="<?= isset($user->user_name) ? $user->user_name : '' ?> " />
        </td>

    </tr>
    <tr>
        <td>
            <label for="email"><?=t("E-mail")?></label>
        </td>
        <td>
        <input type="text" name="email" id="email" max_length="320" size="45" 
               value="<?= isset($user->email) ? $user->email : '' ?> " />

         </td> 
    </tr>
    <tr>
        <td>
            <label for="fullname"><?=t("Full name")?></label>
        </td>
        <td> <input type="text" id="fullname" name="fullname" value="<?= isset($user->fullname) ? $user->fullname : '' ?>"/>
        </td>
    </tr>
    <tr>
        <td>
            <label for="institution"><?=t("Institution")?></label>
        </td>
        <td> 
            <input type="text" id="institution" name="institution" value="<?= isset($user->institution) ? $user->institution : '' ?>"/>
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
    	                       'id'=>'password',
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
    	                       'id'=>'password_confirm',
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
    	                       'id'=>'captcha',
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