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
        <label for="description"><?=t("Issuer Name (optional, if left blank will default to
        '!site_name')", array('!site_name'=>$this->config->item('site_name')))?>:</label>       
        <input type="text" maxlength="128" name="issuer_name" id="issuer_name"  size="80" 
               value="<?= $badge->issuer_name ?>" />               
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
         <?php endif; ?>
        <label for="criteria"><?=t("Criteria !required!")?>:</label>
        <textarea cols="61" rows="10" name="criteria" id="criteria"><?= $badge->criteria ?></textarea>
<?php if ($new): ?>   
        <fieldset>
<legend>Badge approval process:</legend>

<input type="radio" name="type" id="verifier" value="verifier" 
       <?= ($badge->type == 'verifier') ? 'checked="checked"' : "" ?>>
<label class="radio" for="approve" /><?= t("Specify a set of users who can approve or reject badge applications") ?></label>
<br />
<input type="radio" name="type" id="crowdsource" value="crowdsource" 
       <?= ($badge->type == 'crowdsource') ? 'checked="checked"' : "" ?>>
<label class="radio" for="crowdsource" /><?= t("The badge is awarded if a specified number of users on the site approve it") ?></label>
</fieldset>
 <label for="num_approves"><?=t("If the latter, number of users who must approve the badge for it to be awarded")?>:</label>
         <input type="text" maxlength="3" name="num_approves" id="num_approves"  size="10" 
               value="<?= $badge->num_approves ?>" />
<?php endif; ?>               
        <input type="submit" name="submit" id="submit" class="submit" value="<?php if ($new):
          ?><?=t("Create Badge")?><?php else:?><?=t("Save Badge")?><?php endif;?>" />
        <?=form_close()?>
</div>
<div id="region2">

</div>

