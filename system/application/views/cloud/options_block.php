<div class="grid">
    <ul class="options">
    <?php if ($edit_permission): ?>
         <li class="button"><?= anchor('cloud/edit/'.$cloud->cloud_id, t("Edit")) ?></li>
    <?php endif; ?>
    
    <?php if ($admin && !$cloud->omit_from_new_list): ?>
        <li class="button"><?= anchor('cloud/hide/'.$cloud->cloud_id, t("Hide")) ?></li>
    <?php endif; ?>
     
    <?php if ($this->auth_lib->is_logged_in()): ?>
        <?php if ($following): ?>
           <li class="button"><?= anchor('cloud/unfollow/'.$cloud->cloud_id, t("Unfollow")) ?></li>
        <?php else: ?>
            <li class="button"><?= anchor('cloud/follow/'.$cloud->cloud_id, t("Follow")) ?></li>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if($favourite): ?>
        <li class="unfavourite"><?= anchor('cloud/unfavourite/'.$cloud->cloud_id, t("Unfavourite")) ?> </li> 
    <?php elseif ($show_favourite_link): ?>
        <li class="favourite"><?= anchor('cloud/favourite/'.$cloud->cloud_id, t('Favourite')) ?>
        </li>
    <?php endif; ?>
    
    <li class="stats"><div class="nolink"><?php ///Translators: singular and plural forms - !count view/s. ?><?=plural(_("!count view"), _("!count views"), $total_views) ?></div></li>
    <?php if ($total_favourites != 0): ?>
        <li  class="stats"><?= anchor('cloud/favourited/'.$cloud->cloud_id, 
            plural(_("!count favourite"), _("!count favourites"), $total_favourites)) ?>
        </li>
    <?php endif; ?>
    </ul>
</div>