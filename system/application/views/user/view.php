<div id="user-profile">
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

    <?php endif;?>
    <?php if($admin && config_item('x_moderation') && !$user->whitelist): ?>
         <a href="<?= base_url() ?>user/whitelist/<?= $user->user_id ?>" class="button" title ="Whitelist">Whitelist</a>
    <?php endif; ?>
    <?php if(!$current_user && !$isfollowing): ?>
        <a href="<?= base_url() ?>user/follow/<?= $user->id ?>" class="button"><?=t("Follow")?></a>
    <?php elseif(!$current_user): ?>
        <a href="<?= base_url() ?>user/unfollow/<?= $user->id ?>" class="button"><?=t("Unfollow")?></a>
   
    <?php endif; ?>        
    <a href="<?= base_url() ?>message/compose/<?= $user->id ?>" class="button"><?=t("Send message")?></a>
    </h1>
    <?php if ($reputation): ?>
    <p><strong>Reputation: <?= $reputation ?></strong></p>
    <?php endif; ?>
    <p><?= $user->institution ?></p>

</div>

<div id="region1">
    <div class="user-entry">   
      <?= $user->description ?>
      <?php if ($user->institution): ?>
          <p><strong><?=t("Institution")?></strong>: 
          <?= anchor('user/institution/'.urlencode(trim($user->institution)),$user->institution) ?></p>
      <?php endif;?>
      <?php if ($user->department): ?><p><strong><?=t("Department")?></strong>: <?=$user->department ?></p><?php endif;?>
      <?php if ($user->twitter_username): ?><p><strong>Twitter</strong>: <a href="http://www.twitter.com/<?=$user->twitter_username ?>"><?=$user->twitter_username ?></a></p><?php endif;?>
      <?php if ($user->homepage): ?><p><strong><?=t("Webpage")?></strong>: <a href="<?=$user->homepage ?>"><?=$user->homepage ?></a></p><?php endif;?>   
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
</div>