<?php
/**
 * Copy of the "admin" view, modified to allow banning, and spam-learning.
 */
?>

<div id="region1" class="admin-stream-ban">
    <h1><?= $title ?></h1>

    <div class="grid">
        <?php // $this->load->view('event/header'); ?>
        </div>
        <div class="grid">
        <?php if ($events): ?>
            <?php // $this->load->view('event/event_stream'); ?>
            <ul class="cloudstream">

            <?php foreach ($events as $idx => $event): ?>
                <?php $raw = $events_raw[ $idx ]; ?>
                <?php if ($raw->event_type === 'login_attempt') { continue; } ?>

                <?= $event->event ?>
                <a href="<?= base_url() ?>user/ban_and_learn/<?= $event->user_id ?>?from=admin-stream-ban"
                  title="Ban user and learn spam" class="button link-arrow user-ban">Ban user and learn spam</a></li>
            <?php endforeach; ?>

            </ul>
        <?php else: ?>
            <p><?=t("No events in this cloudstream.")?></p>
        <?php endif; ?>
    </div>
     <?php /* <p><a href=""><?=t("Back to cloudscape")?></a> | <a class="rss" href="<?= $rss ?>"><?=t("RSS")?></a></p> */ ?>
</div>
<div id="region2">
    <?php // $this->load->view('search/search_box'); ?>
    <?php // $this->load->view('user/user_block'); ?>
    <p>You can also search for <?= anchor('user/people', 'people') ?> and <?= anchor('user/institution_list', 'institutions') ?></p>

</div>


<script>
jQuery(function ($) {

    $ban_links = $('.admin-stream-ban a.user-ban');

    $ban_links.each(function () {

      $(this).on('click', function (ev) {
        var result = window.confirm("Are you sure you want to ban this user?");
        if (! result) {
          ev.preventDefault();
        }
        console('ban click:', $(this), result);
      });
    });

    console.warn('ban links:', $ban_links);
});
</script>
