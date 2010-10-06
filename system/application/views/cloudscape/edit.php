<?php $this->load->view('layout/tinymce.php'); ?>
<?php $this->load->view('layout/calendar.php'); ?>


<?php if ($new): ?>
<h1><?=t("Create a Cloudscape")?></h1>
<p><?=t("A Cloudscape is a collection of Clouds.")?></p>
<?php else: ?>
    <h1><?=t("Edit Cloudscape")?>
    <a href="<?= base_url() ?>cloudscape/delete/<?= $cloudscape->cloudscape_id ?>" class="button" title="<?=t("Delete this Cloudscape")?>"><?=t("delete")?></a>
              </h1>
              <p><a href="<?= base_url() ?>cloudscape/edit_picture/<?= $cloudscape->cloudscape_id ?>">Add or edit a picture for this cloudscape</a></p>

<?php endif; ?>
<?php echo '<b>'.validation_errors().'</b>'; ?>
 <?php if($end_date_but_not_start_date): ?><b><?=t("Please enter a start date as well as an end date.")?></b><?php endif; ?>
  <?php if($start_date_after_end_date): ?><b><?=t("Please enter a start date that is before the end date.")?></b><?php endif; ?>

<?=form_open($this->uri->uri_string(), array('id' => 'cloud-add-form'))?>
<?php if (!$new): ?>
    <input type="hidden" id="cloudscape_id" name="cloudscape_id" value="<?=$cloudscape->cloudscape_id ?>" ?>
<?php endif; ?>

 <label for="title"><?=t("Title")?>:</label>
 <input type="text" maxlength="128" name="title" id="title"  size="95" value="<?= $cloudscape->title ?>" />

 

 <label for="summary"><?=t("Summary")?>: </label>
 <input type="text" maxlength="128" name="summary" id="summary"  size="95" value="<?= $cloudscape->summary ?>" />

 <label for="body"><?=t("Description")?>: </label>
 <textarea cols="60" rows="20" name="body" id="body"  ><?= $cloudscape->body ?></textarea>
 <?php if (config_item('x_twitter')): ?>
	 <p>
	 <label for="twitter_tag"><?=t("Twitter tag")?>: </label>
	 <input type="text" maxlength="128" name="twitter_tag" id="twitter_tag"  size="60" value="<?= $cloudscape->twitter_tag ?>" class="form-text" />
	
	</p>
	 <p><?=t("Twitter only stores tweets for a hashtag for a limited period of time. If you want to archive them for longer, 
	 you can use !link.",
	   array('!link'=>'<a href="http://www.twapperkeeper.com/">Twapper Keeper</a>'))?></p>
<?php endif; ?>
 <?php if (!$new): ?>
 <fieldset>
 <legend><?= t("Customisation")?></legend>
<p>
 <label for="colour">Hexcode for background colour for headings</label>
 <input type="text" id="colour" name="colour" value="<?= $cloudscape->colour?>"/>
 <br /><small>This is an experimental feature. If you specify the hexcode of a colour then the background colour of headings
 for the cloudscape will be displayed as that colour. <br />If it is successful, we'll extend the feature, including adding a 
 colourpicker to the user interface here. </small>
 </p>
 </fieldset>
 <?php endif; ?>
<fieldset>
<legend><?= t("Extra information if your Cloudscape is for an event")?></legend>
<p><?=t("If this cloudscape is for a conference or other event, please add the following information:")?></p>
 <label for="start_date"><?=t("Start Date e.g. !date", array('!date'=>date('j F Y', time())))?>: </label>
 <input type="text" class="date-pick" maxlength="128" name="start_date" id="start_date"  size="95" value="<?php if ($cloudscape->start_date): ?><?= date('d F Y', $cloudscape->start_date) ?><?php endif; ?>" />
 <br />
 <label for="end_date"><?=t("End Date e.g. !date (leave blank for one-day events)", array('!date'=>date('j F Y', time())))?>: </label>

 <input type="text" class="date-pick" maxlength="128" name="end_date" id="end_date"  size="95" value="
 <?php if ($cloudscape->end_date): ?><?=  date('d F Y', $cloudscape->end_date) ?><?php endif; ?>" />
  <br />
 <label for="location"><?=t("Location")?>: </label>
 <input type="text" maxlength="128" name="location" id="location"  size="95" value="<?= $cloudscape->location ?>" />
</fieldset>
 <br />
 <br />
<?php $button = ($new) ? t("Create Cloudscape") : t("Save Cloudscape"); ?>
<p><button type="submit" name="submit" value="Save"><?=$button?></button></p>
       
<?=form_close()?>
