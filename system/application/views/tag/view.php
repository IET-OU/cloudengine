
<div class="grid headline">
    <div class="c1of1">
       <h1><?=t("Items tagged !tag", array('!tag' => $tag)) ?></h1>
    </div>
</div>
<div id="region1">
    <p class="rss"><?= anchor('tag/rss/'.urlencode($tag), t("RSS feed")) ?></p>     
    <div class="grid">
        <div class="c1of2">
            <h2><?=t("Clouds")?></h2>
            <?php if (count($clouds) > 0): ?>
                <ul class="clouds">
                <?php foreach($clouds as $cloud): ?>
                    
               <li><?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?></li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?=t("No clouds yet")?></p>
            <?php endif; ?>
        </div>
        <div class="c2of2">
            <h2><?=t("Cloudscapes")?></h2>
            <?php if (count($cloudscapes) > 0): ?>
                <ul class="cloudscapes">
                <?php foreach($cloudscapes as $cloudscape): ?>
                    <li>
                    <?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title) ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?=t("No cloudscapes yet")?></p>
            <?php endif; ?>
            <h2><?=t("Users")?></h2>
            <?php if (count($users) > 0): ?>
                <ul class="users">
                <?php foreach($users as $user): ?>
                    <li><a href="<?= base_url() ?>user/view/<?= $user->id ?>"><?= $user->fullname ?></a></li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?=t("No users yet")?></p>
            <?php endif; ?>        
        </div>
    </div>
</div>
<div id="region2">
<?php $this->load->view('search/search_box'); ?>
<?php $this->load->view('user/user_block'); ?>
<p>
<?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?></p>

</div>