<?php if (config_item('x_search')): ?>
<div class="grid headline">
	<h1><?=t("Search results for '!search'", array('!search'=>$query_string))?></h1>
</div>
<div id="region1">

    <div class="grid g1">
        <div class="c1of2">
            <h2><?=t("Clouds")?></h2>
                <?php if (count($clouds) > 0): ?>
                <ul class="clouds">
	            <?php foreach($clouds as $result):?>
	                <li>
	                    <a href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title)?></a>
	                </li>
	            <?php endforeach;?>
	            </ul>
            <?php else: ?>
                <p><?=t("No clouds yet")?></p>
            <?php endif; ?>
        </div>
        
        <div class="c2of2">
            <h2><?=t("Cloudscapes")?></h2>
                <?php if (count($cloudscapes) > 0): ?>
                <ul class="cloudscapes">
    	            <?php foreach($cloudscapes as $result):?>
    	                <li>
    	                   <a href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title)?></a>
    	                </li>
    	            <?php endforeach;?>
	            </ul>
                <?php else: ?>
                    <p><?=t("No cloudscapes yet")?></p>
                <?php endif; ?>
                       <h2><?=t("Users")?></h2>
                <?php if (count($users) > 0): ?>
                <ul class="users">
    	            <?php foreach($users as $result):?>
    	                <li>
    	                   <a href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title)?></a>
    	                </li>
    	            <?php endforeach;?>
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
    <p><?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
    array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?></p>
 </div> 
<?php endif; ?>