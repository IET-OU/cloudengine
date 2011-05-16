<div class="grid">
<?php if ($links): ?>
	<ul class="arrows">
	    <?php foreach($links as $link): ?>
	        <li class="cloud-link"><a href="<?= $link->url ?>" class="<?php if ($link->type == 'cloud'):?> cloud<?php endif; ?><?php if ($link->type == 'cloudscape'):?> cloudscape<?php endif; ?>"><?= $link->title? $link->title : '<div style="word-wrap: break-word">'.$link->url.'</div>' ?></a>
	        <br /><small><?=t("!person",
	            array('!person'=>"<a href='".base_url()."/user/view/$link->user_id'>$link->fullname")) ?></a></small>
	        </li>
	    <?php endforeach; ?>
	</ul>  
<?php endif; ?>

</div>

