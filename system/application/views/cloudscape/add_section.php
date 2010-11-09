<h1><?=t("Add a section to the cloudscape !title", array('!title'=>$cloudscape->title))?></h1>
    <?php echo '<b>'.validation_errors().'</b>'; ?>
    <?=form_open($this->uri->uri_string(), array('id' => 'cloud-add-form'))?>


        <label for="title"><?=t("Section name")?>:</label>
        <input type="text" maxlength="128" name="title" id="title"  size="80" value="" />     
        <p><button type="submit" name="submit" class="submit" value="Add"><?=t("Add section")?></button></p>
        <?=form_close()?>