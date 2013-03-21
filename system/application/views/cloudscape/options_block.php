  <div class="grid">
        <ul class="options">
        <?php if ($edit_permission): ?>
             <li class="button"><?= anchor('cloudscape/edit/'.$cloudscape->cloudscape_id, t("Edit")) ?></li>
        <li class="button"><?= anchor('cloudscape/edit_picture/'.$cloudscape->cloudscape_id, 
        $cloudscape->image_path? t("Edit picture") : t("Add picture")) ?></li>
        <?php endif; ?>
        
        <?php if ($admin && !$cloudscape->omit_from_new_list): ?>
            <li class="button"><?= anchor('cloudscape/hide/'.$cloudscape->cloudscape_id, t("Hide")) ?></li>
        <?php endif; ?>
         
        <?php if ($this->auth_lib->is_logged_in()): ?>
            <?php if ($following): ?>
               <li class="button"><?= anchor('cloudscape/unfollow/'.$cloudscape->cloudscape_id, t("Unfollow")) ?></li>
            <?php else: ?>
                <li class="button"><?= anchor('cloudscape/follow/'.$cloudscape->cloudscape_id, t("Follow")) ?></li>
            <?php endif; ?>
			<?php if ($this->config->item('x_flag')): ?>
				<?php if ($flagged): ?>
					<?= t("Flagged as spam")  ?>
				<?php else: ?>
					<li class="button"><?= anchor('flag/item/cloudscape/'.$cloudscape->cloudscape_id, t("Flag as spam")) ?></li>
				<?php endif; ?>
			<?php endif; ?>
   
            <!-- Attend or unattend button if the cloudscape is an event -->
        <?php if ($cloudscape->start_date): ?>
                <?php if ($attended): ?>
                <li class="button"><?= anchor('cloudscape/unattend/'.$cloudscape->cloudscape_id, $past_event ? t("Mark as not attended") : t("Mark as not attending")) ?></li>
                <?php else: ?>
                <li class="button"><?= anchor('cloudscape/attend/'.$cloudscape->cloudscape_id, $past_event ? t("Mark as attended") : t("Mark as attending")) ?></li>
                <?php endif; ?>
        <?php endif; ?>        
        
        <?php if ($admin && $cloudscape->start_date): ?>
            <?php if ($cloudscape->display_event): ?>
            <li class="button"><?= anchor('cloudscape/remove_diary/'.$cloudscape->cloudscape_id, t("Remove from diary")) ?></li>
            <?php else: ?>
            <li class="button"><?= anchor('cloudscape/add_diary/'.$cloudscape->cloudscape_id, t("Add to diary")) ?></li>
            <?php endif; ?>
        <?php endif; ?>        
        
        <?php if($favourite): ?>
            <li class="unfavourite"><?= anchor('cloudscape/unfavourite/'.$cloudscape->cloudscape_id, t("Unfavourite")) ?> </li> 
        <?php elseif ($show_favourite_link): ?>
            <li class="favourite"><?= anchor('cloudscape/favourite/'.$cloudscape->cloudscape_id, t('Favourite')) ?>
            </li>
        <?php endif; ?>
        <?php endif; ?>
        <li class="stats"><div class="nolink"><?php ///Translators: singular and plural forms - !count view/s. ?>
             <?=plural(_("!count view"), _("!count views"), $total_views) ?></div></li>
        <?php if ($total_favourites != 0): ?>
            <li  class="stats"><?= anchor('cloudscape/favourited/'.$cloudscape->cloudscape_id, 
                plural(_("!count favourite"), _("!count favourites"), $total_favourites)) ?>
            </li>
        <?php endif; ?>
        </ul>
        </div>