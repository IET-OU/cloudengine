<div class="grid headline">
    <div class="c1of2">
        <h1><?=t('!item is attended by !people',
            array('!item'=>anchor("cloudscape/view/$cloudscape->cloudscape_id", $cloudscape->title),
		          '!people'=>plural(_('!count person'), _('!count people'), count($users)))) ?></h1>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
    		<?php $this->load->view('user/users'); ?>    
        </div>
    </div>
</div> 

