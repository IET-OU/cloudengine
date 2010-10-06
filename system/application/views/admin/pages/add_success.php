<h1><?= t("Page saved")?></h1>
<p><?= t("Your page !title has been added successfully",
    array('!title' => $page->title))?></p>
    
<p><?= anchor('admin/manage_pages', t("Back to Manage Pages")) ?></p>    
