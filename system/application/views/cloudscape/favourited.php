<div id="region1">
<h1>The 
cloudscape <a href="<?= base_url() ?>cloudscape/view/<?= $cloudscape->cloudscape_id ?>"><?= $cloudscape->title ?></a> is a favourite of the following people
</h1>
<?= $this->load->view('user/users'); ?>

</div>
 <div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <p>You can also search for <?= anchor('user/people', 'people') ?> and 
    <?= anchor('user/institution_list', 'institutions') ?></p>
<?php $this->load->view('support/favourite_block'); ?>