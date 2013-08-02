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
        <input type="text" maxlength="128" name="name" id="name" required="" size="80"
               value="<?= $badge->name ?>" />
        <label for="description"><?=t("Description !required!")?>:</label>
        <input type="text" maxlength="128" name="description" id="description" required="" size="80"
               value="<?= $badge->description ?>" />
        <label for="issuer_name"><?=t("Issuer Name (optional, if left blank will default to
        '!site_name')", array('!site_name'=>$this->config->item('site_name')))?>:</label>       
        <input type="text" maxlength="128" name="issuer_name" id="issuer_name"  size="80" 
               value="<?= $badge->issuer_name ?>" />               
        <?php if ($new): ?>       
        <label for="filename"><?=t("Image File !required!")?>:</label>
         <input type="file" id="filename" name="filename" size="30" maxlength="128" required="" /></p>
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
        <textarea cols="61" rows="10" name="criteria" id="criteria" required=""><?= $badge->criteria ?></textarea>
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
<div class="box">
<h2><?= t("First steps") ?></h2>
<p><?= t("Begin by giving your badge a name and describing it. Then add the 
name of the issuer (this might be your name, the name of your course or 
institution but make sure you have permission to use the course or institution's 
name on your badge). The default issuer is !site_name.",
array('!site_name' => config_item('site_name'))) ?>
</p>
<h2><?= t("Choosing an image") ?></h2>
<p><?= t("Badges need to be a particular size and format; these restrictions 
are based on the Mozilla Open Badges requirements. You will also need to make sure that you have permission 
to use the image. Once someone has been awarded a badge, the badge image it 
will appear on their Cloudworks profile page, and may also be added to a 
Mozilla Open Badge Backpack. Remember that badges should look worth having!") ?>
</p>
<h2><?= t("Setting badge criteria") ?></h2>
<p>
<?= t("Setting the right criteria for your badge is really important. All the 
rules you might normally apply to setting clear, achievable and demonstrable 
assessment criteria apply here too but with open-badges you also need to be 
careful not to overburden the verifiers - remember there is a chance that 
hundreds of people might apply for you badge! As you set the criteria, also 
remember that applicants will need to be able to prove that they have achieved 
the criteria by linking to one URL as 'evidence' (which of course might be a 
Cloud or blog post linking to other URL evidence but essentially all evidence 
will have to sit online). It is up to the applicant to decide how to 
demonstrate they have met the criteria but it is helpful if you are able to 
make suggestions about how they might do this.") ?>
</p>
<h2><?= t("Choosing a badge approval process") ?>
</h2>
<p><?= t("You can either choose a specified verifier or group of verifiers who 
will check evidence meets criteria and approve or reject applications, or you 
can leave it to the !site_name community to judge whether the badge should be 
awarded. If you use the latter process then you need to say how many of the 
community need to agree that criteria have been met. There are pros and cons to 
each process so think about what will work best in your case.",
array('!site_name' => config_item('site_name')) ) ?>
</p>


</div>
</div>

