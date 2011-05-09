<div class="grid">

<?php if(count($comments) > 0):?>
<?php $i = 1; ?>
    <?php foreach ($comments as $comment):?>

                <div class="user-comment">

                <div class="posted-by">                 
                     
                    <p class="date-stamp">
                       <?= anchor('user/view/'.$comment->user_id, $comment->fullname) ?>
	                    <br />
                        <?= date("g:ia j F Y", $comment->timestamp) ?>
	                    <?php if ($comment->modified): ?>
	                        (<small><em><?=t("Edited !date", array('!date'=>date("g:ia j F Y", $comment->modified))) ?></em></small>)
	                    <?php endif; ?>

                    </p>
                    </div>
                    <br />
                    
                   <?= $comment->body ?>
        </div>
        <?php $i++; ?>
    <?php endforeach; ?>
<?php endif; ?>
</div>


