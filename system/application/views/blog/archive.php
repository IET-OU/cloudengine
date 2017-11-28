<div id="about">
    <div class="grid headline">
        <div class="c1of1">
        <h1><?=$this->config->item('site_name').t(" Blog Archive")?></h1>

        </div>
    </div>
    <div id="region1">

        <?php foreach($news_items as $news): ?>
<h2>
    <?= $news->title ?>
    <?php if ($admin): ?>
        <a href="<?= base_url() ?>blog/edit/<?= $news->post_id ?>" class="button"
                     title="<?=t("Edit this Blog Post")?>"><?=t("edit")?></a>
    <?php endif; ?>
</h2>
<?= $news->body ?>
<p>  <?php if ($news->picture): ?>
                <img src="<?= base_url() ?>image/user_32/<?= $news->user_id ?>" class="go2" alt=""/>
            <?php else: ?>
                <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" alt=""/>
            <?php endif; ?>

&nbsp;<?=t("Posted by !person on !date",
    array('!person'=>"<a href='".base_url()."user/view/$news->user_id'>$news->fullname</a>", '!date'=>date("j F Y", $news->created)))?></p>
<p><a href="<?=base_url() ?>blog/view/<?= $news->post_id ?>" title="<?= $news->title ?>"><?php
if ($comments_enabled):

  $total = 0;
  if ($news->total_comments) {
    $total = $news->total_comments;
  }
?><?=plural(_("!count comment"), _("!count comments"), $total) ?>
<?php else: ?>
<?= t('View blog post') ?>
<?php endif; ?><i class="accesshide"><?= $news->title ?></i></a></p>
<br />


<?php endforeach; ?>

    </div>
    <div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <p>
<?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
    array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?></p>

</div>
</div>
