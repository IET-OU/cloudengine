<?php $this->load->view('layout/tinymce.php'); ?>

<div id="region1">
    <?php if ($new): ?>
      <h1><?=t("Create a Badge")?></h1>
    <?php else: ?>
         <h1><?=t("Edit Badge ")?>
           <a href="<?=base_url() ?>badge/delete/<?=$badge->badge_id ?>" class="button" title="<?=t("Delete this Badge")?>"><?=t("delete")?></a></h1>
    <?php endif; ?>

    <?='<b>'.validation_errors().'</b>'; ?>
    <?= $error ?>
    <?=form_open_multipart($this->uri->uri_string(), array('id' => 'badge-add-form'))?>
        <?php if (!$new): ?>
            <input type="hidden" id="badge_id" name="badge_id" value="<?=$badge->badge_id?>" />
        <?php endif; ?>

    
        <label for="name"><?=t("Name !required!")?>:</label>
        <input type="text" maxlength="128" name="name" id="name"  size="80" 
               value="<?= $badge->name ?>" />
        <label for="description"><?=t("Description !required!")?>:</label>
        <input type="text" maxlength="128" name="description" id="description"  size="80" 
               value="<?= $badge->description ?>" />
        <?php if ($new): ?>       
        <label for="file"><?=t("Image File !required!")?>:</label>
         <input type="file" id="filename" name="filename" size="30" maxlength="139" /></p>
        <ul class="arrows">
            <li><?=t("Must be square - !dimensions pixels",
                  array('!dimensions' => '90 &times; 90'))
                  /*/Translators: There is an opportunity to translate 'KB' (Kilo Bytes) separately. */ ?></li>
            <li><?=t("Maximum file-size of !sizeKB.", array('!size'=>256))?></li>
            <li><?=t("PNG image format only")?></li>
            <li><?=t("You must ensure that you either own the copyright or have copyright clearance for the image.")?></li>
         </ul>         
         <?php else: ?>
            <br />
            <br />
            <p>
            <img src="<?= base_url() ?>image/badge/<?= $badge->badge_id ?>" alt="" />
            <span class="button"><?= anchor('badge/edit_image/'.$badge->badge_id, t("Edit image")) ?></li>
            </p>
         <?php endif; ?>
        <lab
        el for="criteria"><?=t("Criteria !required!")?>:</label>
        <textarea cols="61" rows="10" name="criteria" id="criteria"><?= $badge->criteria ?></textarea>
        <input type="submit" name="submit" id="submit" class="submit" value="<?php if ($new):
          ?><?=t("Create Badge")?><?php else:?><?=t("Save Badge")?><?php endif;?>" />
        <?=form_close()?>
</div>
<div id="region2">

</div>

