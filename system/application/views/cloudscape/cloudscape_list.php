<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Cloudscapes")?></h1>
        <p><?=t("Cloudscapes are collections of clouds about a certain topic.")?></p>
    </div>
    <div class="c2of2">
        <p class="create-cloudscape-link"><a href="<?=base_url()?>cloudscape/add"><?=t("Create a Cloudscape")?></a></p>
    </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
            <ul class="a-z">
                <?php for ($character = 65; $character < 91; $character++) {?>
                                    <?php if (chr($character) != $alpha): ?>
                 <li> <a href="<?=base_url()?>cloudscape/cloudscape_list/<?= chr($character) ?>"><?= chr($character) ?></a>&nbsp;</li>
                                    <?php else: ?>
                    <li><strong><?= chr($character) ?></strong></li>
                    <?php endif; ?>
                    <?php }?>
            </ul>
            <table>
                <thead>            
                    <tr>
                        <th scope="col"><?=t("Title")?></th>
                        <th scope="col"><?=t("Introduction")?></th>
                        <th scope="col"><?=t("Clouds")?></th>
                    </tr>     
                </thead>
                <tbody>
                <?php if($cloudscapes): ?>
                    <?php $row = 1; ?>
                    <?php  foreach ($cloudscapes as $cloudscape):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td><a href="<?=base_url()?>cloudscape/view/<?=$cloudscape->cloudscape_id ?>"><?= $cloudscape->title ?></a></td>
                            <td><?= $cloudscape->summary ?></td>
                            <td><?= $cloudscape->no_clouds ?></td>
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                	<tr><td colspan="3"><?=t("No cloudscapes")?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <?php $this->load->view('cloudscape/new_cloudscapes'); ?>
</div>