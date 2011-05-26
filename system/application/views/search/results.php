<?php if (config_item('x_search')): ?>
<?php if (isset($error)): ?>
    <p class="test_install warn"><?=t("Woops, search didn't succeed.") ?> <?=$error ?></p>
<?php endif; ?>

<div class="grid headline">
	<h1><?= $title ?></h1>
  <p><?=t("Your search produced a total of !results results. A maximuim of !output_limit results for clouds, cloudscapes and users are shown below.", array('!results'=>$total_hits,'!output_limit'=>$output_limit))?><br />
  <?=t("The figure in brackets following each result is the relevance of the search result 
        to the highest ranked result, which will usually be 100%.")?></p>
</div>
<div id="region1">

    <div id="search-nav">
      <div class="search-result-nav-section" id="cloud"><a href="#"><?= t("Clouds (!clouds)", array('!clouds'=>$cloud_hits))?></a></div>
      <div class="search-result-nav-section" id="cloudscape"><a href="#"><?= t("Cloudscapes (!cloudscapes)", array('!cloudscapes'=>$cloudscape_hits))?></a></div>     
      <div class="search-result-nav-section" id="user"><a href="#"><?=t("Users (!users)", array('!users'=>$user_hits))?></a> </div>                                   
    </div> 
        
    <div class="grid g1">

        <div id="cloud-results"> 
        <?php if (count($clouds) > 0): ?>
          <ol class="paging">
          <?php for($i=0; $i<$cloud_output_limit; $i++): ?>          
              <li>
                  <a class="cloud-link" href="<?= $clouds[$i]->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $clouds[$i]->title) 
                      .'(' .round(($clouds[$i]->score * 100),1) .'%)'?></a>
              </li>
          <?php endfor;?>
          </ol>
          <?php if (count($clouds) > $page_limit): ?>
            <a href="#" class="show-all-results">Display <?php if (count($clouds) < $output_limit): ?>all <?php else: ?>top <?php endif; ?><?= $cloud_output_limit ?> cloud results</a> <?= t('(fast to load)') ?><br />
          <?php endif; ?>
          <?php if (count($clouds) > $output_limit): ?>
            <br /><a class="search_all_link" href="<?= base_url() .'search/all_results/cloud?q=' .$query_string ?> "><?= t("View all !clouds cloud results on one page", array('!clouds'=>$cloud_hits))?></a> <?= t(' (may result in large/slow page download)') ?><br />
          <?php endif; ?>
        <?php else: ?>
            <p><?=t("No clouds yet")?></p>
        <?php endif; ?>
        </div>

    
        <div id="cloudscape-results"> 
        <?php if (count($cloudscapes) > 0): ?>
          <ol class="paging">
          <?php for($i=0; $i<$cloudscape_output_limit; $i++): ?>          
              <li>
                  <a class="cloudscape-link" href="<?= $cloudscapes[$i]->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $cloudscapes[$i]->title) 
                      .'(' .round(($cloudscapes[$i]->score * 100),1) .'%)'?></a>
              </li>
          <?php endfor;?>
          </ol>
          <?php if (count($cloudscapes) > $page_limit): ?>
            <a href="#" class="show-all-results">Display <?php if (count($cloudscapes) < $output_limit): ?>all <?php else: ?>top <?php endif; ?><?= $cloudscape_output_limit ?> cloudscape results</a> <?= t('(fast to load)') ?><br />
          <?php endif; ?>
          <?php if (count($cloudscapes) > $output_limit): ?>
            <br /><a class="search_all_link" href="<?= base_url() .'search/all_results/cloudscape?q=' .$query_string ?> "><?= t("View all !cloudscapes cloudscape results on one page", array('!cloudscapes'=>$cloudscape_hits))?></a> <?= t(' (may result in large/slow page download)') ?><br />
          <?php endif; ?>
        <?php else: ?>
            <p><?=t("No cloudscapes yet")?></p>
        <?php endif; ?>
        </div>
        
        <div id="user-results"> 
        <?php if (count($users) > 0): ?>
          <ol class="paging">
          <?php for($i=0; $i<$user_output_limit; $i++): ?>          
              <li>
                  <a class="user-link" href="<?= $users[$i]->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $users[$i]->title) 
                      .'(' .round(($users[$i]->score * 100),1) .'%)'?></a>
              </li>
          <?php endfor;?>
          </ol>
          <?php if (count($users) > $page_limit): ?>
            <a href="#" class="show-all-results">Display <?php if (count($users) < $output_limit): ?>all <?php else: ?>top <?php endif; ?><?= $user_output_limit ?> user results</a> <?= t('(fast to load)') ?><br />
          <?php endif; ?>
          <?php if (count($users) > $output_limit): ?>
            <br /><a class="search_all_link" href="<?= base_url() .'search/all_results/user?q=' .$query_string ?> "><?= t("View all !users user results on one page", array('!users'=>$user_hits))?></a> <?= t(' (may result in large/slow page download)') ?><br />
          <?php endif; ?>
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