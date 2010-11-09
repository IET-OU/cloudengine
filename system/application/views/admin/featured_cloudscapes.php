
<h1>Manage featured cloudscapes</h1>
<p>Enter the IDs of the cloudscapes that you would like to be featured in order here. Remember to 
check that they each have a image first. You will be shown a preview of the cloudscapes 
selected together with their images before you save them.</p>
<?=form_open($this->uri->uri_string(), array('id' => 'featured-cloudscapes-form'))?>
<label>1:</label>
<input type="text" name="cloudscape0" id="cloudscape0" value="<?= $cloudscapes[0]->cloudscape_id ?>"/>
<label>2:</label>
<input type="text" name="cloudscape1" id="cloudscape1" value="<?= $cloudscapes[1]->cloudscape_id ?>"/>
<label>3:</label>
<input type="text" name="cloudscape2" id="cloudscape2" value="<?= $cloudscapes[2]->cloudscape_id ?>"/>
<label>4:</label>
<input type="text" name="cloudscape3" id="cloudscape3" value="<?= $cloudscapes[3]->cloudscape_id ?>"/>
<label>5:</label>
<input type="text" name="cloudscape4" id="cloudscape4" value="<?= $cloudscapes[4]->cloudscape_id ?>"/>
<input type="submit" name="preview" class="submit" id="preview" value="Preview" />
</form>