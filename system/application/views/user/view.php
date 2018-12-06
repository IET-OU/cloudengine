
<script src="<?=base_url()?>_scripts/iframe_strip.js"></script>

<div id="user-profile" class="<?= $show_description() ? 'show-desc' : 'hide-desc' ?>" >

<div class="grid headline">

    <?php if ($picture): ?>
        <img src="<?= base_url() ?>image/user/<?= $user->id ?>" class="go2" alt="" />
    <?php else: ?>
        <img src="<?=base_url() ?>_design/avatar-default-32.jpg" class="go2" alt="" />
    <?php endif; ?>
    <h1><?= $user->fullname ?>


   <?php if($current_user): ?>
             <a href="<?= base_url() ?>user/edit/" class="button" title="<?=t("Edit Profile")?>"><?=t("Edit")?></a>
             <a href="<?= base_url() ?>user/edit_picture/" class="button" title="<?=t("Edit Picture")?>"><?=t("Edit Picture")?></a>
             <a href="<?= base_url() ?>auth/change_password/" class="button" title="<?=t("Change Password")?>"><?=t("Change Password")?></a>
			 <a href="<?= base_url() ?>auth/change_email/" class="button" title="<?=t("Change Email")?>"><?=t("Change Email")?></a>
    <?php endif;?>
    <?php if($admin): ?>
      <?=anchor("auth/admin_change_password/$user->user_id", t('Change Password'), array('title'=>t('Change Password'), 'class'=>'button')) ?>
      <?php if(!$user->deleted): ?>
        <?=anchor("user/delete/$user->user_id", t('Delete'), array('title'=>t('Delete'), 'class'=>'button')) ?>
      <?php else: ?>
        <?=anchor("user/undelete/$user->user_id", t('Undelete'), array('title'=>t('Undelete'), 'class'=>'button')) ?>
      <?php endif; ?>
    <?php endif; ?>
    <?php if($admin): ?>
      <?php if(!$user->banned): ?>
        <?=anchor("user/ban/$user->user_id", t('Ban'), array('title'=>t('Ban'), 'class'=>'button')) ?>
      <?php else: ?>
        <?=anchor("user/unban/$user->user_id", t('Unban'), array('title'=>t('Unban'), 'class'=>'button')) ?>
      <?php endif; ?>
    <?php endif; ?>
    <?php if($admin && config_item('x_moderation') && !$user->whitelist): ?>
	     <?=anchor("user/whitelist/$user->user_id", t('Whitelist'), array('title'=>t('Whitelist'), 'class'=>'button')) ?>
    <?php endif; ?>
    <?php if(!$current_user && !$isfollowing): ?>
        <a href="<?= base_url() ?>user/follow/<?= $user->id ?>" class="button"><?=t("Follow")?></a>
    <?php elseif(!$current_user): ?>
        <a href="<?= base_url() ?>user/unfollow/<?= $user->id ?>" class="button"><?=t("Unfollow")?></a>
    <?php endif; ?>
    <?php if(!$current_user && $this->config->item('x_message')): ?>
        <?= anchor("message/compose/$user->id", t('Send message'), 'class="button"') ?>
	<?php endif; ?>
    </h1>
    <?php if ($reputation): ?>
    <p class="reputation"><strong><?=t('Reputation: !count', array('!count'=> $reputation)) ?></strong></p>
    <?php endif; ?>
    <?php if ($admin && $user->do_not_delete): ?>
      <p class="do-not-delete" title="<?=t('Emeritus, admins & significant Cloudworks users')?>"><i><?=t('Do not delete')?></i></p>
    <?php endif; ?>

    <p class="institution"><?= $user->institution ?>

    </p>
    <?php if (count($badges) > 0 && $this->config->item('x_badge')): ?>
	<div id="open-badges">
      <?php foreach($badges as $badge): ?>
      <a class="badge" href="<?=site_url('badge/view/'.$badge->badge_id) ?>"><img src="<?=site_url('image/badge/'. $badge->badge_id) ?>"
        width="45" height="45" title="<?=t('Badge: !name', array('!name' => $badge->name)) ?>"
        alt="<?=t('Badge: !name', array('!name' => $badge->name)) ?>" /></a>
      <?php endforeach; ?>
	  </div>
      <?php endif; ?>

</div>

<div id="region1">

    <?php if(($user->banned) || ($user->deleted) ): ?>
      <p id="profile-inactive">
        <?php if($user->banned): ?>
       	  <?= t('User has been banned.') ?>
        <?php endif; ?>
        <?php if($user->deleted): ?>
       	  <?= t("User has been 'deleted.'") ?>
        <?php endif; ?>
      </p>
   	<?php endif; ?>

    <div class="user-entry">

      <?php if ($admin || $is_own_profile): ?>
          <p><strong><?=t("Email")?></strong>: <?= $user->email ?> </p>
          <p><strong><?=t("Username")?></strong>: <?= $user->user_name ?> </p>
      <?php endif;?>

      <?= $show_description() ? $user->description : '' ?>

      <?php if ($user->institution): ?>
          <p><strong><?=t("Institution")?></strong>:
          <?= anchor('user/institution/'.urlencode(trim($user->institution)),$user->institution) ?></p>
      <?php endif;?>
      <?php if ($user->department): ?><p><strong><?=t("Department")?></strong>: <?=$user->department ?></p><?php endif;?>
  <?php if ($display_email): ?><p><strong><?=t("Email")?></strong>: <?= $user->email ?></a></p><?php endif;?>

        </div>


    <div class="grid">
        <div class="c1of2">
            <?php $this->load->view('user/clouds_block.php'); ?>
        </div>
        <div class="c2of2">
            <?php $this->load->view('user/cloudscapes_block.php'); ?>
        </div>
    </div>
    <?php $this->load->view('event/user_block'); ?>
</div>

<div id="region2">
    <?php $this->load->view('search/search_box.php'); ?>

    <?php $this->load->view('user/user_block.php'); ?>

     <?php if($current_user || count($tags) > 0): ?>
    <?php $this->load->view('tag/tag_block.php'); ?>
    <?php endif; ?>
    <?php if($current_user): ?>
     <p class="add-link"><?= anchor('tag/add_tags/user/'.$user->user_id, t("Add a tag")) ?></p>
    <?php endif; ?>

     <?php if ($total_favourites > 0): ?>
         <h2><?= t("Favourites") ?></h2>
    <?=anchor("user/favourites/$user->user_id",
         t("!person's favourites (!count)", array('!person' => $user->fullname,
             '!count' => $total_favourites))) ?>
    <?php endif; ?>
    <?php $this->load->view('user/following_block.php'); ?>
    <?php $this->load->view('user/followers_block.php'); ?>
    <?php $this->load->view('events/current_events_block'); ?>
    <?php $this->load->view('events/past_events_block'); ?>

<?php if ($admin && $user->statistics): ?>
<script type="application/json">
{
"user": <?= json_encode($user, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK); ?>

}
</script>
<?php endif; ?>
</div>
