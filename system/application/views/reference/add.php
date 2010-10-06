<h1><?=t("Add an academic reference to the cloud !title", 
    array('!title'=>"<a href='".base_url()."/cloud/view/$cloud->cloud_id'>$cloud->title</a>"))?></h1>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'add-link-form'))?>

<label for="reference_text"><?=t("Reference")?></label>
<p><?=t("Please use the [link-ref]Harvard system of referencing[/link] if possible.",
    array('[link-ref]' => '<a href="http://www.open.ac.uk/skillsforstudy/referencing.php">'))?></p>
<p><textarea id="reference_text" name="reference_text" cols="60" /></textarea></p>
<p><button type="submit" name="submit" value="Add"><?=t("Add reference")?></button></p>
<?=form_close()?>

