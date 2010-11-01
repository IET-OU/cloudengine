<?php $this->load->view('layout/tinymce.php'); ?>
<script type="text/javascript" src="<?=base_url()?>_scripts/date.js"></script>
<script src="<?=base_url()?>_scripts/jquery.datePicker.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
Date.firstDayOfWeek = 0;
Date.format = 'dd mmmm yyyy';

$(function()
{
	$('.date-pick').datePicker({startDate:'01/01/2000'});
});
</script>
<div id="region1">
    <?php if ($new): ?>
      <h1><?php if ($cloudscape_id): ?>
          <?=t("Create a Cloud in the Cloudscape !title", array('!title'=>
            "<a href='".base_url()."cloudscape/view/$cloudscape->cloudscape_id'>$cloudscape->title</a>"))?>
        <?php else: ?>
          <?=t("Create a Cloud")?>
        <?php endif; ?>
      </h1>
          <?php else: ?>
         <h1><?=t("Edit Cloud ")?>
           <a href="<?=base_url() ?>cloud/delete/<?=$cloud->cloud_id ?>" class="button" title="<?=t("Delete this Cloud")?>"><?=t("delete")?></a></h1>
    <?php endif; ?>


    <?php echo '<b>'.validation_errors().'</b>'; ?>
    <?=form_open($this->uri->uri_string(), array('id' => 'cloud-add-form'))?>
        <?php if (!$new): ?>
            <input type="hidden" id="cloud_id" name="cloud_id" value="<?=$cloud->cloud_id?>" />
        <?php endif; ?>
        <?php if ($cloudscape_id): ?>
            <input type="hidden" id="cloudscape_id" name="cloudscape_id" value="<?=$cloudscape_id ?>" ?>
        <?php endif; ?>
    
        <label for="title"><?=t("Title !required!")?>:</label>
        <input type="text" maxlength="165" name="title" id="title"  size="80" value="<?= $cloud->title ?>" />
        <?php if (!$new): ?>
        <label for="summary"><?=t("Summary")?>: </label>
        <input type="text" maxlength="128" name="summary" id="summary"  size="80" value="<?= $cloud->summary ?>" class="form-text" />
        <?php endif; ?>
        <label for="body"><?=t("Text")?>:</label>
        <textarea cols="61" rows="10" name="body" id="body"><?= $cloud->body ?></textarea>

        <br /><?=t("and/or")?>
        <label for="url"><?php if($new): ?><?=t("Add a link")?><?php else: ?><?=t("Link")?><?php endif; ?></label>
	    <input type="text" id="url" name="url" size="80" value="<?php if ($cloud->primary_url): ?><?= $cloud->primary_url ?><?php endif; ?>"/>
	    <?php if (!$new): ?>
	    <br />
	    <br />
	    <p><?=t("If this cloud is for a call of papers or for a call for book chapters, please enter the deadline here for it to appear in the diary.")?></p>
	     <label for="call_deadline"><?=t("Deadline")?>: </label>
 <input type="text" class="date-pick" maxlength="128" name="call_deadline" id="call_deadline"  size="95" value="<?php if ($cloud->call_deadline): ?><?= date('d F Y', $cloud->call_deadline) ?><?php endif; ?>" />
 <br /> <br /> <br />
        <?php endif; ?>
        <input type="submit" name="submit" id="submit" value="<?php if ($new):
          ?><?=t("Create Cloud")?><?php else:?><?=t("Save Cloud")?><?php endif;?>" />
        <?=form_close()?>
</div>
<div id="region2">
<div class="box">
<h2><?=t("What is a Cloud?")?></h2>
<p><?=t("Clouds can be anything of relevance to learning and teaching for example a website, a description of a learning activity, a case study, a resource or tool, or a summary of a presentation.")?> 
 </p>
    <p><?=t("You can also use clouds to pose questions to the !site-name! community.")?></p>

    <h2><?=t("Want to add embedded content?")?></h2>
<p><?=t("You can add embedded content from sites such as Slideshare and Youtube once you have created the cloud (just click on 'add embedded content'):")?> 
    </p>
    <h2><?=t("File Uploads")?></h2>
<p><?=t("It is not currently possible to directly upload files to this site. However you can upload your files to another site and then link to the file here.")?>
    </p>
<p><?=t("We suggest the following sites:")?>
<ul class="arrows">
<li><?=t("Word Docs, PDFs, Powerpoint presentations: !site_link",
    array('!site_link'=>'<a href="http://www.slideshare.net/">SlideShare</a>'))?></li>
<li><?=t("Videos: !site_link",
    array('!site_link'=>'<a href="http://www.youtube.com/">YouTube</a>'))?></li>
<li><?=t("Photos: !site_link",
    array('!site_link'=>'<a href="http://www.flickr.com">Flickr</a>'))?></li>
</ul>

</p>
  <h2><?=t("Cloud or Cloudscape?")?></h2>
<p>
<?=t("A Cloudscape is simply a collection of related Clouds. You might set up a Cloudscape to group Clouds around a topic (i.e. assessment), an event (i.e. a conference), or set up a personal Cloudscape to group Clouds you are interested in. One Cloud can belong to any number of Cloudscapes. For more guidance, see [link-c2554]How to set up a Cloud or Cloudscape[/link]",
    array('[link-c2554]' => t_link('cloud/view/2554')))?>
</p>
</div>
</div>