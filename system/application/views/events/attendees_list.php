<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("!item is attended by ".plural('!count person', '!count people', count($users)),
         array('!item'=>"<a href='".base_url()."cloudscape/view/$cloudscape->cloudscape_id'>$cloudscape->title</a>"))?></h1>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
    		<?php $this->load->view('user/users'); ?>    
        </div>
    </div>
</div> 

