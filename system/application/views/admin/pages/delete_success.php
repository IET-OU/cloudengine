<h1><?= t("Page deleted")?></h1>
<p><?= t("Your page !title has been deleted successfully",
    array('!title' => $page->title))?></p>
    
<p><?= anchor('admin/manage_pages', t("Back to Manage Pages")) ?></p>    
