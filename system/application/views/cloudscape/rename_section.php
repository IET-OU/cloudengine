<h1><?=t("Rename '!title'", array('!title'=>$section->title)) ?></h1>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'cloud-add-form'))?>
    <p>
    <label for="title"><?=t("New section name")?>:</label>
    <input type="text" maxlength="128" name="title" id="title" size="80" value="" />
    <button type="submit" name="submit" class="submit" value="Rename"><?=t("Rename section")?></button>
    </p>
<?=form_close()?>