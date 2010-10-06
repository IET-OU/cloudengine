<h1><?=t("Manage clouds for the cloudscape !title", 
    array('!title'=>"<a href='".base_url()."cloudscape/view/$cloudscape->cloudscape_id'>$cloudscape->title</a>"))?></h1>
<p><?=t("At the moment you can just remove clouds from the cloudscape, but we plan to expand the functionality here in future.")?></p>
<p><?=t("Removing a cloud just removes it from the cloudscape. It does not delete the cloud.")?></p>
<?php if (count($clouds) > 0):?>
    <table>
        <tbody>
        <?php $i = 0; ?>
        <?php  foreach ($clouds as $cloud):?>
            <tr <?php if ($i%2 == 0): ?>class="even"<?php endif; ?>>
                <td>
                    <?= anchor('cloud/view/'.$cloud->cloud_id, $cloud->title) ?>
                </td>
                <td>
                <?= anchor('cloudscape/remove_cloud/'.$cloudscape->cloudscape_id.'/'.$cloud->cloud_id, t("remove cloud")) ?>
                </td>
            </tr>
       		<?php $i++; ?>
       <?php endforeach; ?>
       </tbody>
    </table>
<?php else: ?>
    <p><?=t("No clouds yet")?></p>
<?php endif; ?>
</div>