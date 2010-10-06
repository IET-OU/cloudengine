<?php if (config_item('x_twitter') && $cloudscape->twitter_tag): ?>
    <h2 id="tweets"><?=t("Tweets for !tag", array('!tag'=>$cloudscape->twitter_tag))?></h2>
    <p><a href="http://twitter.com/#search?q=<?= $cloudscape->twitter_tag ?>">View <?= $cloudscape->twitter_tag ?> on twitter</a></p>
    <?php if ($tweets): ?>
        <?php foreach($tweets as $tweet): ?>
            <div class="tweet">
            <img src="<?= $tweet->profile_image_url ?>" alt=""/>
            <p><a href="http://www.twitter.com/<?= $tweet->from_user ?>"><?= $tweet->from_user ?></a>: <?= $tweet->text ?></p>
            <p class="date-stamp"><?= date('j-m-Y \a\t h:i a', strtotime($tweet->created_at))?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><?=t("No tweets")?></p>
    <?php endif; ?>
<?php endif; ?>