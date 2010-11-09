<h1><?=t("Delete section '!title'", array('!title'=>$section->title)) ?></h1>
<p>
<?=t("Are you sure you want to delete the section '!title'. This does not delete the clouds in that section or remove them from the cloudscape.",
    array('!title'=>$section->title)) ?>
</p>
<?=form_open($this->uri->uri_string(), array('id' => 'cloud-add-form'))?>
    <p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete Section") ?></button></p>
<?=form_close()?>