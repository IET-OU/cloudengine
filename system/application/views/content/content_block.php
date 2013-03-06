
<h2 id="extra-content"><?=t("Extra content")?></h2>
<?php if(count($contents) > 0):?>
    <?php foreach ($contents as $content):?>
		<div class="extra-content">
			<?= $content->body ?>
			<div class="posted-by">
					<?php if ($content->picture): ?>
					    <img src="<?= base_url() ?>image/user_32/<?= $content->user_id?>" alt="" style="float:left;margin-right:5px"/>
					<?php else: ?>
					    <img src="<?=base_url() ?>_design//avatar-default-32.jpg" alt="" style="float:left;margin-right:5px"/>
					<?php endif; ?>
					
					<p class="date-stamp">
					<?= anchor('user/view/'.$content->id, $content->fullname) ?>
					<br /> <?=format_date(NULL, $content->created) ?>
					<?php if ($content->modified): ?>
						<?php #=t("Edited !date") ?>
						(<?=format_date(_("Edited !date-time!"), $content->modified) ?>)
					<?php endif; ?>
					<?php if ($content->edit_permission): ?>
						<a href="<?=base_url()?>content/edit/<?= $content->content_id ?>" class="button" title="<?=t("Edit this content")?>"><?=t("Edit")?></a>
					<?php endif; ?>
					<?php if ($admin): ?>
						
					<?= anchor('content/move_to_comment/'.$content->content_id, t("Move to comment")) ?>
					<?php endif; ?>
					<?php if ($this->auth_lib->is_logged_in()): ?>
						<?= anchor('flag/item/content/'.$content->content_id, t("Flag as spam")) ?>
				    <?php endif; ?>
					</p>
			</div>
			

		</div>
    <?php endforeach; ?>
<?php endif; ?>

<p class="add-link"><?= anchor('content/add/'.$cloud->cloud_id, t("Add extra content")) ?></p>