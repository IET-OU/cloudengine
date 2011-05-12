
<h1>Site settings</h1>

<?php echo validation_errors(); ?>

<?=form_open($this->uri->uri_string(), array('id' => 'site-settings-form'))?>

  <?= form_fieldset(t('Debug status')) ?>

    <?
      //debug setting
      echo form_label(t($debug->title), 'db_'.$debug->name, array('id' => 'debug-label')); 
      echo '<p>' .t($debug->description) .'</p>';
      $options = array(
                  '0'   => 'Debug off',
                  '1'   => 'Debug for admin only',
                  '2'   => 'Debug for all users',
                );
      
      echo form_dropdown('db_'.$debug->name,$options,$debug->value);
    ?>
    <br /><br />
  </fieldset>

  <?= form_fieldset(t('Online / Offline settings')) ?>
  
    <p><strong><?= t($site_live->title) ?></strong></p>
    <p><?= t($site_live->description) ?></p>
    <? 
      //site status - online radio button
      echo form_label(t('Online'), 'online', array('id' => 'online-label')); 
      $data = array(  'name'    =>  'db_'.$site_live->name,
                      'id'      =>  'online',
                      'value'   =>  1,
                      'checked' =>  $site_live->value );
      echo form_radio($data);
      
      //site status - offline radio button
      echo form_label(t('Offline'), 'offline', array('id' => 'offline-label'));
      $data = array(  'name'    =>  'db_'.$site_live->name,
                      'id'      =>  'offline',
                      'value'   =>  0,
                      'checked' =>  !$site_live->value );
      echo form_radio($data); 
    ?>

    <?
      //offline public message 
      echo form_label(t($offline_message_public->title), 'db_'.$offline_message_public->name); 
      echo '<p>' .t($offline_message_public->description) .'</p>';
      $value  = str_replace( '!site-name!',config_item('site_name'),$offline_message_public->value);
      $value  = str_replace( '!site-email!',mailto(config_item('site_email')),$value);
      $data   = array('name'    =>  'db_'.$offline_message_public->name,
                      'id'      =>  'offline-message-public',
                      'value'   =>  $value,
                      'cols'    =>  '60',
                      'rows'    =>  '5',
                    );      
      echo form_textarea($data); 
    ?>

    <?
      //offline admin message
      echo '<br />' ;
      echo form_label(t($offline_message_admin->title), 'db_'.$offline_message_admin->name); 
      echo '<p>' .t($offline_message_admin->description) .'</p>';
      $value  = str_replace( '!site-name!',config_item('site_name'),$offline_message_admin->value);
      $data   = array('name'    =>  'db_'.$offline_message_admin->name,
                      'id'      =>  'offline-message-admin',
                      'value'   =>  $value,
                      'cols'    =>  '60',
                      'rows'    =>  '2',
                    );      
      echo form_textarea($data); 
    ?>
    
  </fieldset>
  
  <input type="submit" name="save" class="submit" id="save" value="Save" />
  
</form>