<?php #Cloudscape picture.
      if (!$cloudscape->image_path): ?>
  <h1><label for="file"><?=t("Add a Cloudscape Picture")?></label></h1>
<?php else: ?>
  <h1><label for="file"><?=t("Edit the Cloudscape Picture")?></label></h1>
  <p><img src="<?= base_url() ?>image/cloudscape/<?= $cloudscape->cloudscape_id ?>" alt="<?=t("Current Cloudscape picture") ?>" /></p>
<?php endif; ?>

<?php if ($error): ?>
  <p class="error"><b><?=$error;?></b></p>
<?php endif; ?>

<?php echo form_open_multipart($this->uri->uri_string());?>

  <p><input type="hidden" name="cloudscape_id" value="<?= $cloudscape_id?>" />
  <label for="file"><?=t("Image File")?>:</label>
      <input type="file" id="file" name="userfile" size="30" maxlength="139" /></p>

        <ul class="arrows">
    <li><?=t("If you are interested in your cloudscape being featured on the home page, please make your image as close to !dimensions pixels as possible.",
          array('!dimensions' => '256 &times; 192'))
          /*/Translators: There is an opportunity to translate 'KB' (Kilo Bytes) separately. */ ?></li>
    <li><?=t("Maximum file-size of !sizeKB.", array('!size'=>1500))?></li>
    <li><?=t("JPG, GIF, or PNG image formats.")?></li>
    <li><?=t("This is an experimental feature. If you have any problems, please contact us at !email!")?></li>
  </ul>
    <p><strong><?=t("Please make sure you have permission to use the photo you upload.")?></strong></p>
    <p><?=t("If you want to use an image from a site like !link, please check the owner of the image gives permission for re-use, and provide attribution if required.",
        array('!link'=>'<a href="http://www.flickr.com/">Flickr</a>'))?></p>
       <?php  ?>
  <p><label for="a_name" title="<?=t("For example"); /*/Translators: name of person who should be given attribution. */
  ?> '4_EveR_YounG'"><?=t("Attribution name")?> <small style="font-weight:normal"></small></label>
      <input id="a_name" name="attr_name" size="30" maxlength="139" value="<?=$image_attr_name ?>" />
  <br /><label for="a_link" title="<?=t("For example")?> http://flickr.com/photos/4everyoung/313308360/"><?=t("Attribution link")?> </label>
      <input id="a_link" name="attr_link" size="30" maxlength="499" value="<?=$image_attr_link ?>" /></p>

  <p><button type="submit" name="submit" class="submit" value="Save"><?=t("Save")?></button></p>
</form>