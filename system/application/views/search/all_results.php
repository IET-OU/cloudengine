<?php if (config_item('x_search')): ?>
<?php if (isset($error)): ?>
    <p class="test_install warn"><?=t("Woops, search didn't succeed.") ?> <?=$error ?></p>
<?php endif; ?>

<div class="grid headline">
	<h1><?= $title ?></h1>
  <p><?=t("Your search produced a total of !results results", array('!results'=>$total_hits))?></p>
  <p><a href="<?= base_url() .'search/result?q=' .$query_string ?>" class="back-link" >Back to results</a></p>  
</div>

<div id="region1">
        
    <div class="grid g1">
        
        <div id="results"> 
          <?php if (count($type_plural) > 0): ?>
          <ol class="all-results <?= $type_plural ?>">
          <?php $counter = 0 ?>
          <?php foreach($$type_plural as $result): ?>        
              <li>
                  <a class="<?= $type_single ?>-link"  href="<?= $result->url ?>"><?= str_replace('- '.$this->config->item('site_name'), '', $result->title) 
                       .'(' .round(($result->score * 100),1) .'%)'?></a>
              </li>
          <?php ++$counter ?>
          <?php endforeach;?>
          </ol>
        <?php else: ?>
            <p><?=t("No " .$type_plural ." yet")?></p>
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