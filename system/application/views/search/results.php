<?php if (config_item('x_search')): ?>
<?php if (isset($error)): ?>
    <p class="test_install warn"><?=t("Woops, search didn't succeed.") ?> <?=$error ?></p>
<?php endif; ?>

<div class="grid headline">
	<h1><?= $title ?></h1>
  <p><?=t("Your search produced a total of !results results", array('!results'=>$total_hits))?><br />
  <?=t("The figure in brackets following each result is the relevance of the search result 
        to the highest ranked result, which will usually be 100%.")?></p>
</div>
<div id="region1">

    <div id="search-nav">
      <div class="search-result-nav-section" id="cloud-section"><a href="#" id="jump-to-clouds"><?= t("Clouds (!clouds)", array('!clouds'=>$cloud_hits))?></a></div>
      <div class="search-result-nav-section" id="cloudscape-section"><a href="#" id="jump-to-cloudscapes"><?= t("Cloudscapes (!cloudscapes)", array('!cloudscapes'=>$cloudscape_hits))?></a></div>     
      <div class="search-result-nav-section" id="user-section"><a href="#" id="jump-to-users"><?=t("Users (!users)", array('!users'=>$user_hits))?></a> </div>                                   
    </div> 
        
    <div class="grid g1">
        
        <div id="cloud-results"> 
          <?php if (count($clouds) > 0): ?>
            <?php if (count($clouds) > 15): ?>
              <a class="search_all_link" href="<?= base_url() .'search/all_results/cloud?q=' .$query_string ?> "><?= t("View all !clouds cloud results", array('!clouds'=>$cloud_hits))?></a><br /><br />
            <?php endif; ?>
          <ul class="clouds">
          <?php for($i=0; $i<$cloud_output_limit; $i++): ?>          
              <li>
                  <a href="<?= $clouds[$i]->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $clouds[$i]->title) 
                      .'(' .round(($clouds[$i]->score * 100),1) .'%)'?></a>
              </li>
          <?php endfor;?>
          </ul>
        <?php else: ?>
            <p><?=t("No clouds yet")?></p>
        <?php endif; ?>
        </div>

        <div id="cloudscape-results">
          <?php if (count($cloudscapes) > 0): ?>
            <?php if (count($cloudscapes) > 15): ?>
              <a class="search_all_link" href="<?= base_url() .'search/all_results/cloudscape?q=' .$query_string ?> "><?= t("View all !cloudscapes cloudscape results", array('!cloudscapes'=>$cloudscape_hits))?></a><br /><br />
            <?php endif; ?>          
          <ul class="cloudscapes">
          <?php for($i=0; $i<$cloudscape_output_limit; $i++): ?>          
                <li>
                   <a href="<?= $cloudscapes[$i]->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $cloudscapes[$i]->title) 
                      .'(' .round(($cloudscapes[$i]->score * 100),1) .'%)'?></a>
                </li>
          <?php endfor;?>
          </ul>
          <?php else: ?>
              <p><?=t("No cloudscapes yet")?></p>
          <?php endif; ?>
        </div>
          
        <div id="user-results">          
          <?php if (count($users) > 0): ?>
            <?php if (count($users) > 15): ?>
              <a class="search_all_link" href="<?= base_url() .'search/all_results/user?q=' .$query_string ?> "><?= t("View all !user user results", array('!users'=>$user_hits))?></a><br /><br />
            <?php endif; ?>            
          <ul class="users">
          <?php for($i=0; $i<$user_output_limit; $i++): ?>   
                <li>
                   <a href="<?= $users[$i]->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $users[$i]->title)
                      .'(' .round(($users[$i]->score * 100),1) .'%)'?></a>
                </li>
            <?php endfor;?>
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