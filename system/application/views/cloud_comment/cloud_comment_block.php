<h2><?=t("Comments") ///Translators: singular-plural forms. ?></h2>

<p class="comment"><a href="#comments"
  ><?=plural(_("!count comment"), _("!count comments"), $total_comments) ?></a></p>

<p class="post-comment"><a href="<?= base_url() ?>cloud/view/<?= $cloud->cloud_id ?>/#post"><?=t("Post a comment")?></a></p>