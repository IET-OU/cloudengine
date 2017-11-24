<?php if(count($comments) > 0):?>
    <h2 id="comments"><?=t("Comments")?></h2>
    <?php  foreach ($comments as $row):?>
        <div class="grid comment">
            <div class="c1of2">
                <?php if ($row->picture): ?>
                    <img src="<?= base_url() ?>image/user_32/<?= $row->user_id ?>" alt="" />
                <?php else: ?>
                    <img src="<?=base_url() ?>_design/avatar-default-32.jpg" alt="" />
                <?php endif; ?>
            </div>
            <div class="c2of2">
                <div class="comment-body">
                    <h3><a href="<?= base_url() ?>user/view/<?= $row->id ?>"><?= $row->fullname ?></a> says...</h3>
                    <?= Nofollow::f($row->body) ?>
                    <p class="date-stamp"><?= date("j F Y", $row->timestamp) ?>
                    <?php if ($admin): ?>
                    	<a href="<?= base_url() ?>blog/comment_edit/<?= $row->comment_id ?>" class="button" title="Edit this Comment">Edit</a>
                	<?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="grid">
	<a name="post"><h2 id="post-comment"><?=t("Post a comment")?></h2></a>
	<?php if ($this->auth_lib->is_logged_in()): ?>
		<?php $this->load->view('blog_comment/add'); ?>
	<?php else: ?>
		<p>
        <?=t("Please [link-log]log in[/link] to post a comment. [link-join]Register here[/link] if you haven't signed up yet.",
            array('[link-log]' => t_link('auth/login'), '[link-join]' => t_link('user/register')))?></p>
	<?php endif; ?>
</div>
