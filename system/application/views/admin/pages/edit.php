<?php $this->load->view('layout/tinymce.php'); ?>

<h1><?= t("Edit Page") ?></h1>
<?= '<b>'.validation_errors().'</b>' ?>
<?=form_open($this->uri->uri_string(), array('id' => 'page-edit-form'))?>
    <label for="name">
        <?= t("Page name. This will be used to determine the URL of the page so this should be short with no spaces. If you change this you may break any links to the page.") ?>
    </label>
    <input type="text" maxlength="128" name="name" id="name"  size="95" value="<?= $page->name ?>" />
    
    <label for="title">
        <?= t("Page Title") ?>
    </label>
    <input type="text" maxlength="128" name="title" id="title"  size="95" value="<?= $page->title ?>" />
    
    <label for="lang">
        <?= t("Page Language (put 'en' for English)") ?>
    </label>
    <input type="text" maxlength="128" name="lang" id="lang"  size="95" value="<?= $page->lang?>" />
    
    <label for="body">
        <?= t("Page Body") ?>
    </label>
    <textarea cols="80" rows="40" name="body" id="body"  ><?= $page->body ?></textarea>
 
    <p><button type="submit" name="submit" class="submit" value="Save"><?= t("Save") ?></button></p>
<?=form_close()?>