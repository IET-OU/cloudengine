
<div class="grid headline">
    <div class="c1of1">
       <h1><?php if ($current_user): ?>Your<?php else: ?><?= $user->fullname ?>'s<?php endif; ?> Favourites</h1>
    </div>
</div>
<div id="region1">
       
<div class="grid">
    <div class="c1of2">
        <h2>Clouds</h2>
        <?php if (count($clouds) > 0): ?>
            <ul class="clouds">
            <?php foreach($clouds as $cloud): ?>
                <li>
                <?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?></li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No clouds yet</p>
        <?php endif; ?>
    </div>
    <div class="c2of2">
        <h2>Cloudscapes</h2>
        <?php if (count($cloudscapes) > 0): ?>
            <ul class="cloudscapes">
            <?php foreach($cloudscapes as $cloudscape): ?>
                <li>
                <?= anchor('cloudscape/view/'.$cloudscape->cloudscape_id, $cloudscape->title) ?>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No cloudscapes yet</p>
        <?php endif; ?>       
        </div>
    </div>
</div>

    </div>
    <div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <p>You can also search for <?= anchor('user/people/', t("people")) ?> and <?= anchor('user/institution_list', t("institutions")) ?></p>
<?php $this->load->view('support/favourite_block') ?>
</div> 