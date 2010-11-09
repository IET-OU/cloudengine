<h1><?=t("Delete embedded content from the cloud !title", 
    array('!title'=>"<a href='".base_url()."cloud/view/$cloud->cloud_id'>$cloud->title</a>"))?></h1>
<p><?=t("Are you sure that you want to delete the following embedded content? Deleting embedded content removes it permanently and cannot be undone.")?></p>
<p><?php if ($embed->title): ?><strong><?= $embed->title ?>:&nbsp;&nbsp;</strong> <?php endif; ?><a href="<?= $embed->url ?>"><?= $embed->url ?></a> </p>
<?=form_open($this->uri->uri_string(), array('id' => 'embed-delete-form'))?>
    <p><button type="submit" name="submit" class="submit" value="Delete"><?=t("Delete Embedded Content")?></button></p>
<?php form_close(); ?>
<br />