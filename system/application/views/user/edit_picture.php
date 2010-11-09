<?php if (!$picture): ?>
	<h1><?=t("Add Picture")?></h1>
<?php else: ?>
	<h1><?=t("Edit Picture")?></h1>
	<img src="<?= base_url() ?>image/user/<?= $user_id ?>" alt="Existing picture"/>
<?php endif; ?>

<p><b><?=$error;?></b></p>

<?php echo form_open_multipart($this->uri->uri_string());?>
	<input type="hidden" id="user_id" name="user_id" value="0<?= $user_id?>" ?>
	<label for="userfile"><?=t("File")?></label>
	<input type="file" id="userfile" name="userfile" size="20" />
	<ul class="arrows">
		<li><?=t("Maximum size of !sizeKB.", array('!size' => 500))?> </li>
		<li><?=t("JPG, GIF, or PNG formats.")?></li>
		<li><?=t("We will crop the picture to a square. If you want to control how the picture will be cropped, upload a square photo.")?> </li>
		<li><?=t("Please make sure you have permission to use the photo you upload.")?></li>
		<li><?=t("Maximum dimensions (in pixels): !dimensions", array('!dimensions' => '1024 &times; 768'))?></li>
	</ul>
	<input type="submit" name="submit" id="submit" class="submit" value="<?=t("Save")?>" /> 
</form>