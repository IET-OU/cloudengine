<?php $this->load->view('layout/tinymce.php'); /* NOT USED - see edit.php */ ?>
<div id="region1">

    <h1><?=t("Edit Profile") ?></h1>

<?=form_open($this->uri->uri_string(), array('id' => 'user-edit-form'))?>
<?php if (!$new): ?>
    <input type="hidden" id="user_id" name="user_id" value="<?=$profile->user_id ?>" ?>
<?php endif; ?>

    <label for="fullname"><?=t("Full name !required!", array('!required!'=> /*A Required form field. (Please don't delete.)*/ form_required(t('required'))))?></label>
    <input type="text" maxlength="128" name="fullname" id="fullname"  size="80" value="<?= $profile->fullname ?>" />

    <label for="department"><?=t("Department")?> </label>
    <input type="text" maxlength="128" name="department" id="department"  size="80" value="<?= $profile->department ?>" />
 
    <label for="institution"><?=t("Institution")?> </label>
    <input type="text" maxlength="128" name="institution" id="institution"  size="80" value="<?= $profile->institution ?>" />
 
    <label for="description"><?=t("Description !required!")?></label>
    <textarea cols="61" rows="20" name="description" id="description"><?= $profile->description ?></textarea>

    <label for="homepage"><?=t("Home page")?> </label>
    <input type="text" maxlength="128" name="homepage" id="homepage"  size="80" value="<?= $profile->homepage ?>" />

    <label for="rss"><?=t("RSS feed")?> </label>
    <input type="text" maxlength="128" name="rss" id="rss"  size="80" value="<?= $profile->rss ?>" />
   
    <label for="twitter_username"><?=t("Twitter username")?> </label>
    <input type="text" maxlength="128" name="twitter_username" id="twitter_username"  size="80" value="<?= $profile->twitter_username ?>" />

    
    <input type="submit" name="submit" id="submit" class="submit" value="<?=t("Save")?>" />
<?=form_close()?>
</div>