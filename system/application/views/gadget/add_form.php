<?php $this->load->view('layout/tinymce.php'); ?>
<?= '<b>'.validation_errors().'</b>' ?>
 <p><?= t("You can add Google Gadgets to a Cloud if you know the URL of the XML file for the Google Gadget. 
Gadgets will only work if they are compatible with Google Friend Connect.") ?></p>
 <p><?= t("The gadget will display in the right sidebar of a cloud so please make sure it is suitable dimensions for this") ?></p>
<?=form_open($this->uri->uri_string(), array('id' => 'add-gadget-form'))?>
<p>
	<label for="title"><?=t("Gadget Title !required!")?></label>
	<input type="text" id="title" name="title" size="80" value="<?= $gadget->title ?>"/>
	<label for="url"><?=t("URL of the XML file for the Google Gadget !required!")?></label>
	<input type="text" id="url" name="url" value="<?php if ($gadget->url): ?><?= $gadget->url ?><?php else: ?>http://<?php endif; ?>"  size="80" />
	
	<label for="accessible_alternative"><?= t("Accessible Alternative !optional", array('!optional'=> /*/Translators: The form field is optional. */ form_required(t("optional")))) ?></label>
	<textarea id="accessible_alternative" name="accessible_alternative" cols="60" rows= "10"><?= $gadget->accessible_alternative ?></textarea>
</p>
<p><button type="submit" name="submit" value="Save"><?= t('Add Google Gadget') ?></button></p>
<?=form_close()?>
