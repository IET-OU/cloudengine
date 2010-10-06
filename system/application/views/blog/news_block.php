<div class="box">
<h2><?=$this->config->item('site_name').t(" Blog")?></h2>

<ul class="news">
    <?php foreach($news as $item): ?>
        <li>
        	<a href="<?= base_url() ?>blog/view/<?= $item->post_id ?>"><?= $item->title ?></a>
        </li>
    <?php endforeach; ?>
</ul>
<p><a href="<?=base_url() ?>blog/archive"><?=t("Blog Archive")?></a>
 | <a class="rss" href="<?= base_url() ?>blog/rss"><?=t("RSS !feed", array('!feed'=>NULL))?></a></p>
</div>
