<h1>Featured Cloudscapes Preview</h1>

<p>Use the save button below if you are happy with your selection. Remember to check 
that each of the cloudscapes has an image.</p>

<?php  foreach ($featured_cloudscapes as $cloudscape):?>
    <h2> <?= $cloudscape->title ?></h2>
    <img src="<?= base_url() ?>image/cloudscape/<?= $cloudscape->cloudscape_id ?>" 
        alt="View Cloudscape" width="240" height="180" />
    <a class="view-cs" href="<?= base_url() ?>cloudscape/view/<?= $cloudscape->cloudscape_id ?>">View Cloudscape</a>
    <p>
        <?= $cloudscape->summary ?> 
        <a href="<?= base_url() ?>cloudscape/view/<?= $cloudscape->cloudscape_id ?>">View Cloudscape</a> 
        <?php if ($cloudscape->image_attr_name): ?>
            <small>(image by <a href="<?= $cloudscape->image_attr_link ?>">
            <?= $cloudscape->image_attr_name ?></a>)</small>
        <?php endif; ?>
    </p>
    <br />
<?php endforeach; ?>         
                
<?=form_open($this->uri->uri_string(), array('id' => 'featured-cloudscapes-form'))?>
    <input type="hidden" name="cloudscape0" value="<?= $featured_cloudscapes[0]->cloudscape_id ?>" />
    <input type="hidden" name="cloudscape1" value="<?= $featured_cloudscapes[1]->cloudscape_id ?>" />
    <input type="hidden" name="cloudscape2" value="<?= $featured_cloudscapes[2]->cloudscape_id ?>" />
    <input type="hidden" name="cloudscape3" value="<?= $featured_cloudscapes[3]->cloudscape_id ?>" />
    <input type="hidden" name="cloudscape4" value="<?= $featured_cloudscapes[4]->cloudscape_id ?>" />
    <input type="submit" name="submit" id="submit" class="submit" value="Save" />
</form>


