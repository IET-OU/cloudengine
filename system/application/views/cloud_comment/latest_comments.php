<h2><?=t("Latest comments")?></h2>

<ul class="comments">
	<?php foreach ($comments as $comment): ?>
		<li>
	<a href="<?=base_url()?>cloud/view/<?= $comment->cloud_id ?>"><?=t("!cloud-comment... <em>(in !title !time)</em>",
	    array('!cloud-comment'=>truncate_content(strip_tags($comment->body)), '!title'=>$comment->title, '!time'=>time_ago($comment->timestamp)))?></a>
		</li>
	<?php endforeach; ?>
</ul>
