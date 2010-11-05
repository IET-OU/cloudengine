<p class="create-cloud-link"><?=anchor("cloud/add", t("Create a Cloud")) ?></p>
<p class="login">
    <a href="#login" class="link-arrow show"><?= $loggedinprofile->fullname ?></a> 
    <?=anchor('auth/logout', t('Sign out')) ?>
    <?php if($this->auth_lib->is_admin() && $total_items != 0): ?>
    <br />
    <br />
    <a href="<?=base_url() ?>admin/moderate"><?=plural(_("!count item requires moderation"), _("!count items require moderation"), $total_items) ?></a>
  <?php endif; ?>
</p>

<div id="login" class="collapsed">
    <ul>
        <li><a href="<?=base_url() ?>user/view/<?= $loggedinprofile->user_id ?>"><?=t("Your Profile")?></a></li>
        <li><a href="<?=base_url() ?>user/preferences"     ><?=t("Preferences")?></a></li>
        <li><a href="<?=base_url() ?>user/favourites"><?=t("Your Favourites")?></a></li>
        <?php if ($this->auth_lib->is_admin()): ?>
        <li><a href="<?=base_url() ?>admin/panel"    ><?=t("Administration")?></a></li>
        <?php endif; ?>  
    </ul>
</div>