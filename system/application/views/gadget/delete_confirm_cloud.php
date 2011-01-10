<h1><?=t("Delete Google Gadget from the cloud !title", 
    array('!title'=>anchor('cloud/view/'.$cloud->cloud_id, $cloud->title)))?></h1>
<p> <?=t("Are you sure that you want to delete the Google gadget '!title'?",
        array('!title' => $gadget->title)) ?>
    <?=t('Deleting a Google Gadget removes it permanently and cannot be undone.') ?></p>
<?=form_open($this->uri->uri_string(), array('id' => 'gadget-delete-form'))?>
    <p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete Google Gadget")?></button></p>
<?php form_close(); ?>
<br />