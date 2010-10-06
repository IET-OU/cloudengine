<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("!name follows !count people.",
          array('!name'=>$profile->fullname, '!count'=>count($users))) ?></h1>
        <p><?=anchor("user/view/$profile->user_id",
          t("Back to !name's profile", array('!name'=>$profile->fullname))) ?></p>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
    <?php $this->load->view('user/users'); ?>
            
        </div>
    </div>
</div> 

