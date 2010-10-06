<?php if ($user_profile['twitter_username']): ?>
<div class="grid">
    <h2 id="tweets"><?=t("Tweets for !name", array('!name'=>$user_profile['twitter_username']))?></h2>
       <?php if ($tweets): ?>
            <?php foreach($tweets as $tweet): ?>
                <div class="grid comment" id="comment1">
                    <div class="c1of2">
                        <img src="<?= $tweet->profile_image_url ?>" alt="" />
                    </div>
                    <div class="c2of2">
                        <div class="comment-body">
                            <p><a href="http://www.twitter.com/<?= $tweet->from_user ?>"><?= $tweet->from_user ?></a>: <?= $tweet->text ?></p>
                            <p class="date-stamp"><?= date('j-m-Y \a\t h:i a', strtotime($tweet->created_at)) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?=t("No tweets")?></p>
        <?php endif; ?>
</div>
 <?php endif; ?>