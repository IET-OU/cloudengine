<h1><?=t("Add clouds to a section !title", array('!title'=>$cloudscape->title))?></h1>
<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'cloud-add-form'))?>
    <label for="section"><?=t("Section where clouds will be added") ?></label>
    <select name="section_id" id="section">
        <?php foreach($sections as $section): ?>
            <option value="<?= $section->section_id ?>"><?= $section->title ?></option>
        <?php endforeach ?>
    </select> 
    <br />
    
    <label for="clouds"><?=t("Check the clouds from this cloudscape to add")?></label>
    <?php foreach ($clouds as $cloud): ?>
        <input type="checkbox" name="clouds[]" id="cl-<?=$cloud->cloud_id?>" value="<?= $cloud->cloud_id ?>"
          /><label for="cl-<?= $cloud->cloud_id ?>"><?= $cloud->title ?></label> <br />
    <?php endforeach; ?>

    <p><button type="submit" name="submit" value="Add"><?=t("Add clouds")?></button></p>
<?=form_close()?>