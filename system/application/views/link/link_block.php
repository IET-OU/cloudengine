<div class="grid">
<?php if ($links): ?>
	<ul class="arrows">
	    <?php foreach($links as $link): ?>
	        <li class="cloud-link"><a name="link-<?=$link->link_id ?>" href="<?= $link->url ?>" <?= Nofollow::attr() ?> class="<?php if ($link->type == 'cloud'):?> cloud<?php endif; ?><?php if ($link->type == 'cloudscape'):?> cloudscape<?php endif; ?>"><?= $link->title? $link->title : '<div style="word-wrap: break-word">'.$link->url.'</div>' ?></a>
	        <br /><small><?=t("added by !person",
	            array('!person'=>"<a href='".base_url()."/user/view/$link->user_id'>$link->fullname")) ?></a></small>

	            <?php if ($edit_permission || $link->edit_permission): ?>
	            <br />
	                <small><?= anchor('cloud/delete_link/'.$link->link_id, t("delete")) ?>
	                </small>
                <?php endif; ?>
	            <?php if ($link->edit_permission): ?>
	              &nbsp;
	              <small><?= anchor('cloud/edit_link/'.$link->link_id, t("edit")) ?></small>
	            <?php endif; ?>
					<?php if ($this->auth_lib->is_logged_in()): ?>
						<?php if ($this->config->item('x_flag')): ?>
							<?php if ($link->flagged): ?>
								<?= t("Flagged as spam")  ?>
							<?php else: ?>
								<small><?= anchor('flag/item/link/'.$link->link_id, t("Flag as spam")) ?></small>
							<?php endif; ?>
						<?php endif; ?>
				    <?php endif; ?>
	            <?php if ($admin && !$cloud->primary_url): ?>
	            &nbsp; <small>
	            <?= anchor('cloud/make_link_primary/'.$cloud->cloud_id.'/'.$link->link_id, t("make primary")) ?></small>
	            <?php endif; ?>
	            <?php if ($link->total): ?>&nbsp;
	           <small><?= $link->total ?>&nbsp;recommendation<?php if($link->total > 1): ?>s<?php endif; ?>
	            </small><?php endif; ?>
	            <?php if ($link->show_favourite_link): ?>
	                     &nbsp;<small>
	                     <?= anchor('cloud/link_favourite/'.$cloud->cloud_id.'/'.$link->link_id,
	                     t("recommend")) ?></small>
	            <?php endif; ?>
	        </li>
	    <?php endforeach; ?>
	</ul>
<?php endif; ?>

<p class="add-link"><?= anchor('cloud/add_link/'.$cloud->cloud_id, t("Add link")) ?></p>
</div>
