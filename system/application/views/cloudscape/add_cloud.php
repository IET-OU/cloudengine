<h1><?=t("Add the !title cloud to a cloudscape", array('!title'=>"<a href='/cloud/view/$cloud->cloud_id'>$cloud->title</a>"))?></h1>
<?php if($search_string && count($cloudscapes) == 0): ?>
<p> <?=t("No results found for !search", array('!search'=>"<b>$search_string</b>"))?></p>
<?php elseif ($cloudscapes): ?>
<p> <?=t("Results for !search", array('!search'=>"<b>$search_string</b>"))?></p>
    <table>
    <?php foreach ($cloudscapes as $cloudscape): ?>
    <tr>
    <td> <?= $cloudscape->title ?></td>
     <td>
     <?= anchor('cloudscape/add_cloud/'.$cloud->cloud_id.'/'.$cloudscape->cloudscape_id, 
     t("Add to cloudscape"))?>
</td>
       </tr>
    <?php endforeach; ?>
    </table>
<?php else: ?>
<h2>Recently viewed cloudscapes</h2>

<?php if ($recent_cloudscapes): ?>
    <table>
    <?php foreach ($recent_cloudscapes as $cloudscape): ?>
    <tr >
    <td> <?= $cloudscape->title ?></td>
     <td>     <?= anchor('cloudscape/add_cloud/'.$cloud->cloud_id.'/'.$cloudscape->cloudscape_id, 
     t("Add to cloudscape"))?></td>
       </tr>
    <?php endforeach; ?>
    </table>
    <h2>Search Cloudscapes</h2>
<p>If the cloudscape that you want to add the cloud to is not in the list above, you can search for the cloudscape that 
you want to add the cloud to. </p>
<?php endif; ?>
<?php endif; ?>

<?=form_open($this->uri->uri_string(), array('id' => 'cloud-permissions-form'))?>
     <label for="search_string"><?=t("Search cloudscapes")?></label>
 <input type="text" maxlength="128" name="search_string" id="search_string"  size="95" value="" />
 <p><button type="submit" name="submit" value="Search" class="form-submit"><?=t("Search")?></button></p>
 <?=form_close()?>

</div>

