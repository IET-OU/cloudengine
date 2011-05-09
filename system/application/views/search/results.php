<?php if (config_item('x_search')): ?>
<?php if (isset($error)): ?>
    <p class="test_install warn"><?=t("Woops, search didn't succeed.") ?> <?=$error ?></p>
<?php endif; ?>

<div class="grid headline">
	<h1><?=t("Search results for '!query'", array('!query'=>$query_string))?></h1>
  <p><?=t("Your search produced a total of !results results", array('!results'=>$total_hits))?></p>
</div>
<div id="region1">

    <div class="grid g1">
        <div class="c1of2">
            <h2><?=t("Clouds (!clouds)", array('!clouds'=>$cloud_hits))?></h2>
                <?php if (count($clouds) > 0): ?>
                <ul class="clouds">
	            <?php foreach($clouds as $result):?>
	                <li>
	                    <a href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title) 
                          .'(' .round($result->score,2) .')'?></a>
	                </li>
	            <?php endforeach;?>
	            </ul>
            <?php else: ?>
                <p><?=t("No clouds yet")?></p>
            <?php endif; ?>
        </div>
        
        <div class="c2of2">
            <h2><?=t("Cloudscapes (!cloudscapes)", array('!cloudscapes'=>$cloudscape_hits))?></h2>
                <?php if (count($cloudscapes) > 0): ?>
                <ul class="cloudscapes">
    	            <?php foreach($cloudscapes as $result):?>
    	                <li>
    	                   <a href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title) 
                            .'(' .round($result->score,2) .')'?></a>
    	                </li>
    	            <?php endforeach;?>
	            </ul>
                <?php else: ?>
                    <p><?=t("No cloudscapes yet")?></p>
                <?php endif; ?>
                       <h2><?=t("Users (!users)", array('!users'=>$user_hits))?></h2>
                <?php if (count($users) > 0): ?>
                <ul class="users">
    	            <?php foreach($users as $result):?>
    	                <li>
    	                   <a href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title)
                            .'(' .round($result->score,2) .')'?></a>
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