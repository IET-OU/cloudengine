<div class="grid headline">

<h1><?=t("Popular Tags")?></h1> 
</div>

<div id="region1">

<div class="grid g1">

<div class="c1of2">

<?php /*/Translators: 'Top 10' etc. */ ?>
<h2><?=t("Top !count", array('!count'=>10)?></h2>

<ul class="top-tags">
<?php foreach($toptags as $tag): ?>
    <li><?= anchor('tag/view/'.urlencode($tag->tag), $tag->tag) ?></li>
<?php endforeach; ?>
</ul>

</div>

<div class="c2of2">

<ul class="tags-list">
<?php foreach($tags as $tag): ?>
    <li>
    <?= anchor('tag/view/'.urlencode($tag->tag), $tag->tag ) ?></a>
    </li>
<?php endforeach; ?>
<!-- about 50 here, tops. perhaps these should pull in randomly from all available tags -->
</ul>

</div>

</div>

</div> 

<div id="region2">


        <?php $this->load->view('search/search_box'); ?>
        <?php $this->load->view('user/user_block'); ?>
<p>
<?=t("You can also search for [link-up]people[/link] and [link-ui]institutions[/link]",
    array('[link-up]' => t_link('user/people'), '[link-ui]' => t_link('user/institution_list')))?></p>

</div> 
  
</div>


</div>

