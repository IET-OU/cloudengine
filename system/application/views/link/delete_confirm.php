<h1><?=t("Delete link from the cloud !title", 
    array('!title'=>"<a href='".base_url()."cloud/view/$cloud->cloud_id'>$cloud->title</a>"))?></h1>
<p><?=t("Are you sure that you want to delete the following link? Deleting a link removes it permanently and cannot be undone.")?></p>
<p><?php if ($link->title): ?><strong><?= $link->title ?>:&nbsp;&nbsp;</strong> <?php endif; ?><a href="<?= $link->url ?>"><?= $link->url ?></a> </p>
<?=form_open($this->uri->uri_string(), array('id' => 'link-delete-form'))?>
    <p><button type="submit" name="submit" value="Delete"><?=t("Delete Link")?></button></p>
<?php form_close(); ?>
<br />