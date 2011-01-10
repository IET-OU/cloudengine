<h1><?=t("Delete Google Gadget from all your clouds")?></h1>
<p> <?=t("Are you sure that you want to delete the Google gadget '!title' from all your clouds?",
        array('!title' => $gadget->title))?>
    <?=t('Deleting a Google Gadget removes it permanently and cannot be undone.') ?></p>
<?=form_open($this->uri->uri_string(), array('id' => 'gadget-delete-form'))?>
    <p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete Google Gadget")?></button></p>
<?php form_close(); ?>
<br />