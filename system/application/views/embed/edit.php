<?php $this->load->view('layout/tinymce.php'); ?>
<div id="region1">
<?php if ($new): ?>
<h1><?=t("Add embedded content to the cloud !title",
    array('!title'=>anchor("cloud/view/$cloud->cloud_id", $cloud->title))) ?></h1>
<p><?=t("You can add embedded content from supported sites by entering the URL here.")?> </p>
<?php else: ?>
<h1><?=t("Edit embedded content for the cloud !title", 
  array('!title'=>anchor("cloud/view/$cloud->cloud_id", $cloud->title))) ?></h1>
<?php endif; ?>

<?php echo '<b>'.validation_errors().'</b>'; ?>
<?=form_open($this->uri->uri_string(), array('id' => 'add-embed-form'))?>
<p>
	<label for="title"><?=t("Title !optional",
	  array('!optional'=> /*/Translators: The form field is optional. */ form_required(t("optional"))))?></label>
	<input type="text" id="title" name="title" size="80" value="<?= $embed->title ?>"/>
	<label for="url"><?=t("URL !required!")?></label>
	<input type="text" id="url" name="url" value="<?php if ($embed->url): ?><?= $embed->url ?><?php else: ?>http://<?php endif; ?>"  size="80" />
	<label for="accessible_alternative"><?= t("Accessible Alternative !optional", array('!optional'=> /*/Translators: The form field is optional. */ form_required(t("optional")))) ?></label>
	<textarea id="accessible_alternative" name="accessible_alternative" cols="60" rows= "10"><?= $embed->accessible_alternative ?></textarea>	
	
</p>

<?php $button = ($new) ? t("Add embedded content") : t("Save embedded content"); ?>
<p><button type="submit" name="submit" class="submit" value="Save"><?=$button ?></button></p>
<?=form_close()?>
</div>
<div id="region2">
<div class="box">
<h2><?= t("Supported sites for embeds") ?></h2>
<p>5min, Amazon Product Images, Flickr, Google Video, Hulu, Imdb, Metacafe, Qik, Revision3, Slideshare, Twitpic, Viddler, Vimeo, Wikipedia, WordPress, YouTube </p>
</div>
<div class="box">
<h2><?=t('Want to add an embed from a site not on the list?') ?></h2>
<p><?= t("We only allow embeds from sites that support a standard called oEmbed. If 
you want to add embeds from a site not on the list above, please ask them to support oEmbed and let us know when they have added support for it.") ?>
</p>
</div>
</div>