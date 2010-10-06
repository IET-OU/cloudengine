
    <div class="grid headline">
        <div class="c1of1">
           <h1>Popular Clouds and Cloudscapes</h1>
        </div>
    </div>
    <div id="region1">
       
<div class="grid">
    <div class="c1of2">
        <h2>Clouds</h2>
        <?php if (count($clouds) > 0): ?>
            <ul class="clouds">
            <?php foreach($clouds as $cloud): ?>
                <li><a href="<?= base_url() ?>cloud/view/<?= $cloud->cloud_id ?>"><?= $cloud->title ?> <?= $cloud->total_votes ?> favourite<?php if ($cloud->total_votes != 1): ?>s<?php endif; ?></a></li>
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
   <p>You can also search for <?= anchor('user/people', 'people') ?> and <?= anchor('user/institution_list', 'institutions') ?></p>

</div> 