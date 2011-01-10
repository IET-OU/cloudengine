
<h1><?=t("Add tags to !title", array('!title'=>anchor($url, $item_title))) ?></h1>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'add-tags-form'))?>
	<label for="tags"><?=t("Tags !note", array('!note'=> form_required(t("comma-separated")))) ?>:<br/> 
	<small><?= t("e.g. collaborative learning, problem based learning, calculus, moodle") ?></small></label>
	<input type="text" id="tags" name="tags" size="80" />
	<p><button type="submit" name="submit" class="submit" value="Add"><?=t("Add tags")?></button></p>
<?=form_close()?>
