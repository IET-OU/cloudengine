<div id="user-profile">
<div class="grid headline">
    <h1><?= $user->fullname ?></h1>
    <p><?= $user->institution ?></p>
</div>

<div id="region1">

    <div class="user-entry">   

      <?= $user->description ?>
      <?php if ($user->institution): ?>
          <p><?= anchor('user/institution/'.urlencode(trim($user->institution)),$user->institution) ?></p>
      <?php endif;?>
      <?php if ($user->department): ?><p><?=$user->department ?></p><?php endif;?>
      <?php if ($user->twitter_username): ?><p><a href="http://www.twitter.com/<?=$user->twitter_username ?>"><?=$user->twitter_username ?></a></p><?php endif;?>
      <?php if ($user->homepage): ?><p><a href="<?=$user->homepage ?>"><?=$user->homepage ?></a></p><?php endif;?>   
      <?php if ($display_email): ?><p> <?= $user->email ?></a></p><?php endif;?> 
        </div>


    <div class="grid">
        <div class="c1of2">
            <?php $this->load->view('user/clouds_block_search_view.php'); ?>     
        </div>  
        <div class="c2of2">
            <?php $this->load->view('user/cloudscapes_block_search_view.php'); ?>
        </div>
    </div>
</div> 


<div id="region2">


     <?php if($current_user || count($tags) > 0): ?>
    <?php $this->load->view('tag/tag_block_search_view.php'); ?>
    <?php endif; ?>
 
    <?php $this->load->view('events/current_events_block_search_view'); ?>    
    <?php $this->load->view('events/past_events_block_search_view'); ?>
</div>
