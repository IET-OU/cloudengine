<div class="grid">

<?php if(count($comments) > 0):?>
<?php $i = 1; ?>
    <?php foreach ($comments as $comment):?>
              <h3 class="hidden"><?= t('Comment ').$i.t(' by ').$comment->fullname ?></h3>
                <div class="user-comment">

                <div class="posted-by">
                    <?php if ($comment->picture): ?>
                        <img src="<?= base_url() ?>image/user_32/<?= $comment->user_id?>" alt="" style="float:left; margin-right: 5px"/>
                    <?php else: ?>
                        <img src="<?=base_url() ?>_design//avatar-default-32.jpg" alt="" style="float:left; margin-right: 5px"/>
                    <?php endif; ?> 
                   
                     
                    <p class="date-stamp">
                       <?= anchor('user/view/'.$comment->user_id, $comment->fullname) ?>
	                    <br />
                        <?= date("g:ia j F Y", $comment->timestamp) ?>
	                    <?php if ($comment->modified): ?>
	                        (<small><em><?=t("Edited !date", array('!date'=>date("g:ia j F Y", $comment->modified))) ?></em></small>)
	                    <?php endif; ?>
	                    <?php if ($comment->edit_permission): ?>
	                        <a href="<?= base_url() ?>comment/edit/<?= $comment->comment_id ?>" class="button" title="<?=t("Edit this Comment")?>"><?=t("Edit")?></a>
	                   <?php endif; ?>
	                   <?php if ($admin): ?>
	                   <?= anchor('comment/move_to_content/'.$comment->comment_id,t("Move to content"))?>
	                   
	            		<?php endif; ?> 
						<?php if ($this->auth_lib->is_logged_in()): ?>
						<?= anchor('flag/item/cloud_comment/'.$comment->comment_id, t("Flag as spam")) ?>
						<?php endif; ?>
                    </p>
                    </div>
                    <br />
                    
                   <?= $comment->body ?>
        </div>
        <?php $i++; ?>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<div class="grid">
    <a name="post"></a><h3 id="post-comment"><?=t("Contribute to the discussion")?></h3>
    <?php if ($this->auth_lib->is_logged_in()): ?>
        <?php $this->load->view('cloud_comment/add'); ?>
    <?php else: ?>
<p><?=t("Please [link-log]log in[/link] to post a comment. [link-join]Register here[/link] if you haven't signed up yet.",
    array('[link-log]' => t_link('auth/login'), '[link-join]' => t_link('user/register')))?></p>
    <?php endif; ?>
</div>
