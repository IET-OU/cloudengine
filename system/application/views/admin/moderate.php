<h1> Moderation Queue</h1>
<p><?= anchor('admin/panel', t("Back to admin panel")) ?></p>


<?php if ($clouds): ?>
<h2>Clouds</h2>
    <?php foreach ($clouds as $cloud): ?>
        Cloud ID:  <?= $cloud->cloud_id ?>
        <p><?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?></p>
        <p>Cloud created  by <a href="<?= base_url() ?>user/view/<?= $cloud->user_id ?>"><?= $cloud->fullname ?></a></p>
        <p><?= $cloud->summary ?></p>
        <?= $cloud->body ?>
        <p class="login">
        <?=anchor("user/ban/$cloud->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'cloud-approve'))?>
            <input type="hidden" id="type" name="type" value="cloud" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$cloud->cloud_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>


<?php if ($comments): ?>

<h2>Comments</h2>
    <?php foreach ($comments as $comment): ?>
        Cloud ID: <?= $comment->cloud_id ?> / comment ID: <?= $comment->comment_id ?>
        <p>Comment  by <a href="<?= base_url() ?>user/view/<?= $comment->user_id ?>"><?= $comment->fullname ?></a></p>
        <?= $comment->body ?>
        <p class="login">
        <?=anchor("user/ban/$comment->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'comment-approve'))?>
            <input type="hidden" id="type" name="type" value="comment" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$comment->comment_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>



<?php if ($cloudscapes): ?>
<h2>Cloudscapes</h2>
    <?php foreach ($cloudscapes as $cloudscape): ?>
    <p><a href="<?= base_url() ?>cloudscape/view/<?= $cloudscape->cloudscape_id ?>"><?= $cloudscape->title ?></a></p>
        <p>Cloudscape created  by <a href="<?= base_url() ?>user/view/<?= $cloudscape->user_id ?>"><?= $cloudscape->fullname ?></a></p>
        <p><?= $cloudscape->summary ?></p>
        <?= $cloudscape->body ?>
        <p class="login">
        <?=anchor("user/ban/$cloudscape->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'cloudscape-approve'))?>
            <input type="hidden" id="type" name="type" value="cloudscape" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$cloudscape->cloudscape_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($news_comments): ?>
<h2>Blog post comments</h2>
    <?php foreach ($news_comments as $comment): ?>
        <p>Comment  by <?= anchor('user/view'.$comment->user_id, $comment->fullname) ?></p>
        <?= $comment->body ?>
        <p class="login">
        <?=anchor("user/ban/$comment->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'news_comment-approve'))?>
            <input type="hidden" id="type" name="type" value="news_comment" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$comment->comment_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>


<?php if ($links): ?>
<h2>Links</h2>
    <?php foreach ($links as $link): ?>
        <p><?= $link->title ?> <?= $link->url ?> by
        <?= anchor('user/view/'.$link->user_id, $link->fullname )?></p>
        <p class="login">
        <?=anchor("user/ban/$link->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'link-approve'))?>
            <input type="hidden" id="type" name="type" value="link" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$link->link_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>


<?php if ($references): ?>
<h2>References</h2>
    <?php foreach ($references as $reference): ?>
        <p><?= $reference->reference_text ?>  by
        <?= anchor('user/view/'.$reference->user_id, $reference->fullname) ?></p>
        <p class="login">
        <?=anchor("user/ban/$reference->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'reference-approve'))?>
            <input type="hidden" id="type" name="type" value="reference" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$reference->reference_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>


<?php if ($contents): ?>
<h2>Content</h2>
    <?php foreach ($contents as $content): ?>
        Cloud ID: <?= $content->cloud_id ?> / content ID: <?= $content->content_id ?>
        <p><?= $content->body ?>  by  <?= anchor('user/view/'.$content->user_id, $content->fullname) ?></p>
        <p class="login">
        <?=anchor("user/ban/$content->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'content-approve'))?>
            <input type="hidden" id="type" name="type" value="content" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$content->content_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>


<?php if ($embeds): ?>
<h2>Embeds</h2>
    <?php foreach ($embeds as $embed): ?>
        <p><?= $embed->title ?>  <a href="<?= $embed->url ?>"><?= $embed->url ?></a>
        by  <?= anchor('user/view/'.$embed->user_id, $embed->fullname) ?></p>
        <p class="login">
        <?=anchor("user/ban/$embed->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'embed-approve'))?>
            <input type="hidden" id="type" name="type" value="embed" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$embed->embed_id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($profiles): ?>
<h2>Profiles</h2>
    <?php foreach ($profiles as $profile): ?>
        <p><?= $profile->description ?>  <?= $profile->homepage ?> by  <?= anchor('user/view/'.$profile->user_id, $profile->fullname) ?></p>
        <p class="login">
        <?=anchor("user/ban/$profile->user_id", t('Ban User'), array('title'=>t('Ban User'), 'class'=>'button')) ?>
      </p>
        <?=form_open($this->uri->uri_string(), array('id' => 'profile-approve'))?>
            <input type="hidden" id="type" name="type" value="profile" ?>
            <input type="hidden" id="action" name="action" value="approve" ?>
            <input type="hidden" id="id" name="id" value="<?=$profile->id ?>" ?>
            <input type="submit" name="submit" id="submit" class="submit" value="Approve" />
        <?=form_close()?>
    <?php endforeach; ?>
<?php endif; ?>
