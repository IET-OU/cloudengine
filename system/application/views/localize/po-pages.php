
<?php foreach ($pages as $idx=>$page): ?>
#. /Page title
#: <?=$page->ref ?>:title
msgid "<?=$page->title ?>"
msgstr ""

#. /Page body
#: <?=$page->ref ?>:body
msgid ""
<?php   foreach ($page->lines as $line): ?>
"<?=      $line ?>\n"
<?php   endforeach; ?>
msgstr ""


<?php endforeach; ?>
