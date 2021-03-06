<p class="create-cloud-link"><?=anchor("cloud/add", t("Create a Cloud")) ?></p>
<p class="login">
    <a href="#login" class="link-arrow show"><?= $loggedinprofile->fullname ?></a> 
    <?=anchor('auth/logout', t('Sign out')) ?>
    <?php if($this->auth_lib->is_admin() && isset($total_items) && $total_items != 0): ?>
    <br />
    <br />
    <a href="<?=base_url() ?>admin/moderate"><?=plural(_("!count item requires moderation"), _("!count items require moderation"), $total_items) ?></a>
  <?php endif; ?>
</p>

<div id="login" class="collapsed">
    <ul>
	<?php if($this->auth_lib->is_admin() && isset($online_users)): ?>
	    <li class="online-users"><?=t('There are curently <b>!U users</b> and <b>!G guests</b> online.', 
		    array('!U'=>$online_users->loggedin, '!G'=>$online_users->guests)); ?></li>
	<?php endif; ?>
        <li><a href="<?=base_url() ?>user/view/<?= $loggedinprofile->user_id ?>"><?=t("Your Profile")?></a></li>
        <li><a href="<?=base_url() ?>user/preferences"     ><?=t("Preferences")?></a></li>
        <li><a href="<?=base_url() ?>user/favourites"><?=t("Your Favourites")?></a></li>
        <?php if ($this->auth_lib->is_admin()): ?>
        <li><a href="<?=base_url() ?>admin/panel"    ><?=t("Administration")?></a></li>
        <?php endif; ?>  
    </ul>
</div>