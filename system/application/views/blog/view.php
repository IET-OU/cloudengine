<div class="grid headline">
    <div class="c1of1">
<h1>
<?= $news->title ?>
<?php if ($admin): ?>
    <a href="<?= base_url() ?>blog/edit/<?= $news->post_id ?>" class="button" title="<?=t("Edit this News Item")?>"><?=t("edit")?></a>
<?php endif; ?>
</h1>
    </div>
</div>
<div id="region1">


<?= $news->body ?>
<p>
<?php if ($news->picture): ?>
            <img src="<?= base_url() ?>image/user_32/<?= $news->user_id ?>" class="go2" alt=""/>
        <?php else: ?>
            <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" alt=""/>
        <?php endif; ?>
   
&nbsp; <?=t("Posted by !person on !date", 
array('!person'=>"<a href='".base_url()."user/view/$news->user_id'>$news->fullname</a>", '!date'=>date("j F Y", $news->created)))?>

<?php $this->load->view('blog_comment/display.php'); ?>

</div>
<div id="region2">
<?php $this->load->view('search/search_box'); ?>
<?php $this->load->view('user/user_block'); ?>
<p>
<?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?></p>

</div> 
