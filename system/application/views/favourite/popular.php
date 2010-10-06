
    <div class="grid headline">
        <div class="c1of1">
           <h1><?=t("Popular Clouds and Cloudscapes") ?></h1>
        </div>
    </div>
    <div id="region1">
       
<div class="grid">
    <div class="c1of2">
        <h2>Clouds</h2>
        <?php if (count($clouds) > 0): ?>
            <ul class="clouds">
            <?php foreach($clouds as $cloud): ?>
                <li> <?=anchor("cloud/view/$cloud->item_id",
                 "$cloud->title  $cloud->total_favourites <img src=\"".base_url().'_design/icon-unfavourite.gif" alt="'.t("favourites").'" />') ?></li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?=t("No clouds yet") ?></p>
        <?php endif; ?>
    </div>
    <div class="c2of2">
        <h2>Cloudscapes</h2>
        <?php if (count($cloudscapes) > 0): ?>
            <ul class="cloudscapes">
            <?php foreach($cloudscapes as $cloudscape): ?>
                <li> <?=anchor("cloudscape/view/$cloudscape->item_id",
                 "$cloudscape->title  $cloudscape->total_favourites <img src=\"".base_url().'_design/icon-unfavourite.gif" alt="'.t("favourites").'" />') ?></li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?=t("No cloudscapes yet") ?></p>
        <?php endif; ?>      
        </div>
    </div>
</div>

    </div>
    <div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <p><?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
    array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?>
</p>
</div> 