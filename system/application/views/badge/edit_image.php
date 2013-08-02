<div id="region1">
    <h1><?=t("Edit Image for badge '!name'", array('!name'=>$badge->name))?></h1>
    <p><strong><?= t("Current Image") ?>:</strong></p>
<p><img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" /> </p>
    <?= $error ?>
    <?=form_open_multipart($this->uri->uri_string(), array('id' => 'badge-add-form'))?>
        <?php if (!$new): ?>
            <input type="hidden" id="badge_id" name="badge_id" value="<?=$badge->badge_id?>" />
        <?php endif; ?>

        <label for="filename"><?=t("New image !required!")?>:</label>
         <input type="file" id="filename" name="filename" size="30" maxlength="139" /></p>
        <ul class="arrows">
            <li><?=t("Must be square - !dimensions pixels",
                  array('!dimensions' => '90 &times; 90'))
                  /*/Translators: There is an opportunity to translate 'KB' (Kilo Bytes) separately. */ ?></li>
            <li><?=t("Maximum file-size of !sizeKB.", array('!size'=>256))?></li>
            <li><?=t("PNG image format only")?></li>
            <li><?=t("You must ensure that you either own the copyright or have copyright clearance for the image.")?></li>
         </ul>         
       
        <input type="submit" name="submit" id="submit" class="submit" value="<?=t("Update Image")?>" />
        <?=form_close()?>
</div>


