<?php if (config_item('x_twitter') && $cloudscape->twitter_tag): ?>
    <h2 id="tweets"><?=t("Tweets for !tag", array('!tag'=>$cloudscape->twitter_tag))?></h2>
    <p><a href="http://twitter.com/hashtag/<?= str_replace('#', '', $cloudscape->twitter_tag) ?>">View <?= $cloudscape->twitter_tag ?> on twitter</a></p>
<?php endif; ?>
