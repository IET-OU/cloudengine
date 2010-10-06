<h1><?= t("View statistics for specific cloudscape") ?></h1>
<p><?= anchor('/admin/panel', t("Back to admin panel")) ?></p>
<div id="region1">
    
    <?=form_open($this->uri->uri_string(), array('id' => 'stats-cloudscape-form'))?>
       

<?= form_dropdown('cloudscape_id', $cloudscapes) ?>
    
         
        <input type="submit" name="submit" id="submit" value="<?= t("View Statistics") ?>" />
        <?=form_close()?>
</div>
