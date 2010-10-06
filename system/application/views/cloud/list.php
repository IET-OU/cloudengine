<div class="grid headline">
    <div class="c1of2">
        <h1><?=t("Clouds")?></h1>
        <p><?=t("Clouds can be anything of relevance to learning and teaching e.g. a description of a learning activity, a case study, a resource or tool, a summary of a presentation.")?></p>
    </div>
</div>

<div id="region1">
    <div class="grid g1">
        <div class="c1of1">
                    <ul  class="a-z">
                <?php for ($character = 65; $character < 91; $character++) {?>
                    <?php if (chr($character) != $alpha): ?>
                    <li><?=anchor("cloud/cloud_list/".chr($character), chr($character)) ?>&nbsp;</li>
                    <?php else: ?>
                    <li><strong><?= chr($character) ?></strong></li>
                    <?php endif; ?>
                <?php }?>
            </ul>
            <table>
                <caption class="hidden">List of Clouds</caption>
                <thead>            
                    <tr>
                        <th scope="col"><?=t("Title")?></th>
                        <th scope="col"><?=t("Introduction")?></th>
                        <th width="20%" scope="col"><?=t("Comments")?></th>
                    </tr>     
                </thead>
                <tbody>
                    <?php $row = 1; ?>
                    <?php  foreach ($clouds as $cloud):?>
                        <tr <?php if ($row % 2 == 0): ?>class="even"<?php endif; ?>>
                            <td><?=anchor("cloud/view/$cloud->cloud_id", $cloud->title) ?></td>
                            <td><?= $cloud->summary ?></td>
                            <td><?php if ($cloud->total_comments != 0) : ?>
                            <?=plural(_("!count comment"), _("!count comments"), $cloud->total_comments) ?>
                            <?php else: ?>
                            &nbsp;
                            <?php endif; ?>
                            </td>
                        </tr>
                        <?php $row++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> 

<div id="region2">
    <?php $this->load->view('search/search_box'); ?>
    <?php $this->load->view('user/user_block'); ?>
    <?php $this->load->view('cloud/newest_clouds'); ?>
</div>